<?php

namespace App\Http\Controllers\Guardian;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class GuardianDashboardController extends Controller
{
    public function index(): Response
    {
        $userId = Auth::id();

        $passMark = (int)(DB::table('exam_settings')->value('pass_mark') ?? 70);
        $subjectsCount = (int)DB::table('exam_subjects')->count();

        // KPI dasar milik guardian
        $myStudents = (int)DB::table('students')->where('guardian_user_id', $userId)->count();
        $admissions = DB::table('admissions as a')
            ->join('students as s', 's.id', '=', 'a.student_id')
            ->where('s.guardian_user_id', $userId)
            ->get(['a.id', 'a.created_at']);
        $myAdmissions = $admissions->count();

        // ambil agregat nilai per admission
        $agg = DB::table('exam_scores')
            ->select('admission_id', DB::raw('ROUND(AVG(score)) as avg_score'), DB::raw('COUNT(*) as cnt'))
            ->whereIn('admission_id', $admissions->pluck('id'))
            ->groupBy('admission_id')
            ->get()
            ->keyBy('admission_id');

        // hitung selesai/pending + rata-rata total & status terakhir
        $completed = 0;
        $pending = 0;
        $avgs = [];

        foreach ($admissions as $a) {
            $x = $agg[$a->id] ?? null;
            if (!$x || $subjectsCount === 0 || (int)$x->cnt < $subjectsCount) {
                $pending++;
                continue;
            }
            $completed++;
            $avgs[] = (int)$x->avg_score;
        }

        $overallAvg = count($avgs) ? (int)round(array_sum($avgs) / count($avgs)) : null;

        // status terakhir: berdasarkan admission terbaru yang COMPLETE
        $lastStatus = '-';
        if ($completed > 0) {
            // admission complete terbaru
            $latestComplete = $admissions
                ->filter(fn($a) => ($agg[$a->id]->cnt ?? 0) >= $subjectsCount)
                ->sortByDesc('created_at')
                ->first();

            if ($latestComplete) {
                $avg = (int)round($agg[$latestComplete->id]->avg_score ?? 0);
                $lastStatus = $avg >= $passMark ? 'Lulus Tes' : 'Tidak Lulus Tes';
            }
        }

        // tingkat penyelesaian (untuk progress bar)
        $completionRate = $myAdmissions > 0 ? round(($completed / $myAdmissions) * 100) : 0;

        return Inertia::render('guardian/Dashboard', [
            'summary' => [
                'myStudents' => $myStudents,
                'myAdmissions' => $myAdmissions,
                'subjectsCount' => $subjectsCount,
                'passMark' => $passMark,
                'completed' => $completed,
                'pending' => $pending,
                'overallAvg' => $overallAvg,  // bisa null
                'lastStatus' => $lastStatus,  // "-", "Lulus Tes", "Tidak Lulus Tes"
                'completionRate' => $completionRate, // 0..100
            ],
        ]);
    }
}
