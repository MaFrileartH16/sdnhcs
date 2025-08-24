<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class AdminApplicantController extends Controller
{
    public function index(Request $request): Response
    {
        $q = trim((string)$request->query('q', ''));
        $perPage = max(1, (int)$request->query('perPage', 10));

        // KKM (default 70)
        $passMark = (int)(DB::table('exam_settings')->value('pass_mark') ?? 70);

        // Total mata pelajaran → untuk mengecek kelengkapan skor
        $subjectsCount = (int)DB::table('exam_subjects')->count();

        // Agg nilai per admission: avg & count skor
        $scoreAgg = DB::table('exam_scores')
            ->select('admission_id', DB::raw('ROUND(AVG(score)) as avg_score'), DB::raw('COUNT(*) as cnt'))
            ->groupBy('admission_id')
            ->get()
            ->keyBy('admission_id'); // ->get('admission_id') => { avg_score, cnt }

        $pager = Student::query()
            ->with([
                'guardian:id,name,phone',
                'admission:id,student_id,admission_form_file,family_card_file,birth_certificate_file,guardian_id_card_file,kindergarten_certificate_file,average_score,created_at',
            ])
            ->when($q !== '', function ($qr) use ($q) {
                $qr->where('full_name', 'like', "%{$q}%")
                    ->orWhereHas('guardian', fn($w) => $w->where('name', 'like', "%{$q}%")
                        ->orWhere('phone', 'like', "%{$q}%")
                    );
            })
            ->latest()
            ->paginate($perPage)
            ->appends($request->query());

        $mapped = $pager->getCollection()
            ->map(function ($s) use ($passMark, $subjectsCount, $scoreAgg) {
                if (!$s) return null;

                // Ambil agregat skor dari exam_scores (jika sudah ada)
                $admissionId = optional($s->admission)->id;
                $agg = $admissionId ? ($scoreAgg[$admissionId] ?? null) : null;

                // avg untuk ditampilkan (jika ada skor sama sekali)
                $avgFromScores = $agg ? (int)$agg->avg_score : null;

                // Tentukan status:
                // - Jika tidak ada admission sama sekali → "-"
                // - Jika ada admission tetapi skor belum lengkap (cnt < total subjects) → "-"
                // - Jika lengkap: bandingkan avg vs KKM
                $status = '-';
                if ($admissionId) {
                    if ($agg && $subjectsCount > 0 && (int)$agg->cnt >= $subjectsCount) {
                        $status = $avgFromScores >= $passMark ? 'Lulus Tes' : 'Tidak Lulus Tes';
                    } else {
                        $status = '-';
                    }
                }

                // Helper URL publik /storage/...
                $url = function (?string $path) {
                    return $path ? Storage::url($path) : null;
                };

                return [
                    'id' => (int)$s->id,
                    'student_name' => $s->full_name,
                    'guardian_name' => $s->guardian?->name ?? '-',
                    'guardian_phone' => $s->guardian?->phone ?? '-',
                    'average_score' => $avgFromScores, // tampilkan avg dari exam_scores (bisa null kalau belum ada skor)
                    'status' => $status,
                    'created_at' => optional($s->admission?->created_at)->format('Y-m-d H:i'),
                    'files' => $s->admission ? [
                        ['label' => 'Formulir Pendaftaran', 'path' => $url($s->admission->admission_form_file)],
                        ['label' => 'Kartu Keluarga', 'path' => $url($s->admission->family_card_file)],
                        ['label' => 'Akte Kelahiran', 'path' => $url($s->admission->birth_certificate_file)],
                        ['label' => 'KTP Wali', 'path' => $url($s->admission->guardian_id_card_file)],
                        ['label' => 'Ijazah TK', 'path' => $url($s->admission->kindergarten_certificate_file)],
                    ] : [],
                ];
            })
            ->filter()
            ->values();

        $pager->setCollection($mapped);
        $arr = $pager->toArray();

        $subjects = DB::table('exam_subjects')
            ->select('id', 'name', 'link_url')
            ->orderBy('name')
            ->get()
            ->values()
            ->all();

        return Inertia::render('admin/Applicants', [
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
                'subjects' => $subjects,
            ],
        ]);
    }
}
