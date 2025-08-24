<?php

namespace App\Http\Controllers\Guardian;

use App\Http\Controllers\Controller;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class GuardianApplicantController extends Controller
{
    public function index(Request $request): Response
    {
        $q = trim((string)$request->query('q', ''));
        $perPage = max(1, (int)$request->query('perPage', 10));

        // KKM (default 70)
        $passMark = (int)(DB::table('exam_settings')->value('pass_mark') ?? 70);

        // Ambil semua subject (untuk modal & validasi FE)
        $subjects = DB::table('exam_subjects')
            ->select('id', 'name', 'link_url')
            ->orderBy('name')
            ->get();

        $subjectsCount = (int)$subjects->count();

        // Pager students
        $pager = Student::query()
            ->with([
                'guardian:id,name,phone',
                'admission:id,student_id,admission_form_file,family_card_file,birth_certificate_file,guardian_id_card_file,kindergarten_certificate_file,created_at',
            ])
            ->when($q !== '', function ($qr) use ($q) {
                $qr->where('full_name', 'like', "%{$q}%")
                    ->orWhereHas('guardian', fn($w) => $w->where('name', 'like', "%{$q}%")
                        ->orWhere('phone', 'like', "%{$q}%"));
            })
            ->latest()
            ->paginate($perPage)
            ->appends($request->query());

        // Admission IDs dari page ini
        $admissionIds = $pager->getCollection()
            ->pluck('admission.id')
            ->filter()
            ->values();

        // Ambil semua score untuk admission di page ini, lalu group
        $scoresByAdmission = DB::table('exam_scores')
            ->whereIn('admission_id', $admissionIds)
            ->select('admission_id', 'exam_subject_id', 'score')
            ->get()
            ->groupBy('admission_id');

        // Precompute agregat avg & cnt untuk status/avg
        $aggByAdmission = DB::table('exam_scores')
            ->whereIn('admission_id', $admissionIds)
            ->select('admission_id', DB::raw('ROUND(AVG(score)) as avg_score'), DB::raw('COUNT(*) as cnt'))
            ->groupBy('admission_id')
            ->get()
            ->keyBy('admission_id');

        // Map ke payload FE
        $mapped = $pager->getCollection()
            ->map(function ($s) use ($passMark, $subjectsCount, $scoresByAdmission, $aggByAdmission) {
                if (!$s) return null;

                $admissionId = optional($s->admission)->id;

                // daftar score per-subject untuk row ini (map: subject_id => score int)
                $scoresMap = [];
                if ($admissionId && isset($scoresByAdmission[$admissionId])) {
                    foreach ($scoresByAdmission[$admissionId] as $sc) {
                        $scoresMap[(int)$sc->exam_subject_id] = (int)$sc->score;
                    }
                }

                // avg & status
                $avg = null;
                $status = '-';
                if ($admissionId && isset($aggByAdmission[$admissionId])) {
                    $avg = (int)$aggByAdmission[$admissionId]->avg_score;
                    $complete = (int)$aggByAdmission[$admissionId]->cnt >= $subjectsCount && $subjectsCount > 0;
                    if ($complete) {
                        $status = $avg >= $passMark ? 'Lulus Tes' : 'Tidak Lulus Tes';
                    }
                }

                // Helper /storage/...
                $url = fn(?string $path) => $path ? Storage::url($path) : null;

                return [
                    'id' => (int)$s->id,
                    'student_name' => $s->full_name,
                    'guardian_name' => $s->guardian?->name ?? '-',
                    'guardian_phone' => $s->guardian?->phone ?? '-',
                    'average_score' => $avg,    // null jika belum ada skor
                    'status' => $status, // '-' jika belum lengkap
                    'created_at' => optional($s->admission?->created_at)->format('Y-m-d H:i'),
                    'files' => $s->admission ? [
                        ['label' => 'Formulir Pendaftaran', 'path' => $url($s->admission->admission_form_file)],
                        ['label' => 'Kartu Keluarga', 'path' => $url($s->admission->family_card_file)],
                        ['label' => 'Akte Kelahiran', 'path' => $url($s->admission->birth_certificate_file)],
                        ['label' => 'KTP Wali', 'path' => $url($s->admission->guardian_id_card_file)],
                        ['label' => 'Ijazah TK', 'path' => $url($s->admission->kindergarten_certificate_file)],
                    ] : [],
                    // ✅ kirim skor per-subject untuk prefill input modal:
                    'scores' => $scoresMap,
                ];
            })
            ->filter()
            ->values();

        $pager->setCollection($mapped);
        $arr = $pager->toArray();

        return Inertia::render('guardian/Applicants', [
            'items' => [
                'data' => array_values($arr['data']),
                'links' => array_values($arr['links']),
                'total' => (int)$arr['total'],
            ],
            'filters' => [
                'q' => $q,
                'perPage' => $perPage,
            ],
            'meta' => [
                'passMark' => $passMark,
                'perPages' => [10, 25, 50, 100],
                'subjects' => $subjects->values()->all(), // [{id,name,link_url}]
            ],
        ]);
    }

    /**
     * Upsert nilai-nilai untuk seorang siswa (guardian).
     * Body:
     *   scores: array<{ subject_id:int, score:int(0-100) }>
     */
    public function upsertScores(Request $request, Student $student)
    {
        $validated = $request->validate([
            'scores' => ['required', 'array', 'min:1'],
            'scores.*.subject_id' => ['required', 'integer', 'exists:exam_subjects,id'],
            'scores.*.score' => ['required', 'integer', 'min:0', 'max:100'],
        ]);

        // pastikan student punya admission
        $admission = DB::table('admissions')->where('student_id', $student->id)->first();
        if (!$admission) {
            return redirect()->route('guardian.applicants.index');
        }

        // upsert tiap skor
        $rows = [];
        foreach ($validated['scores'] as $row) {
            $rows[] = [
                'admission_id' => (int)$admission->id,
                'exam_subject_id' => (int)$row['subject_id'],
                'score' => (int)$row['score'],
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        // unique(admission_id, exam_subject_id) -> update score on conflict
        DB::table('exam_scores')->upsert(
            $rows,
            ['admission_id', 'exam_subject_id'],
            ['score', 'updated_at']
        );

        // (opsional) update admissions.average_score agar selaras
        $agg = DB::table('exam_scores')
            ->where('admission_id', $admission->id)
            ->selectRaw('ROUND(AVG(score)) as avg_score, COUNT(*) as cnt')
            ->first();

        if ($agg) {
            DB::table('admissions')
                ->where('id', $admission->id)
                ->update([
                    'average_score' => (int)$agg->avg_score,
                    'updated_at' => now(),
                ]);
        }

        // tanpa flash – balik ke index; FE boleh reload parsial
        return redirect()->route('guardian.applicants.index');
    }
}
