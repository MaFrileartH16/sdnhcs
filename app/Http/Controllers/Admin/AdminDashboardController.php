<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class AdminDashboardController extends Controller
{
    public function index(): Response
    {
        // KKM
        $passMark = (int)(DB::table('exam_settings')->value('pass_mark') ?? 70);

        // Metrik dasar
        $totalStudents = (int)DB::table('students')->count();
        $totalGuardians = (int)DB::table('users')->where('role', 'guardian')->count();
        $totalAdmissions = (int)DB::table('admissions')->count();
        $subjectsCount = (int)DB::table('exam_subjects')->count();

        // Ambil agregat skor per admission
        $agg = DB::table('exam_scores')
            ->select('admission_id', DB::raw('ROUND(AVG(score)) as avg_score'), DB::raw('COUNT(*) as cnt'))
            ->groupBy('admission_id')
            ->get()
            ->keyBy('admission_id');

        // Hitung lulus/gagal/complete
        $pass = 0;
        $fail = 0;
        $complete = 0;
        if ($subjectsCount > 0) {
            foreach ($agg as $row) {
                if ((int)$row->cnt >= $subjectsCount) {
                    $complete++;
                    $avg = (int)$row->avg_score;
                    if ($avg >= $passMark) $pass++; else $fail++;
                }
            }
        }

        // Pendaftar terbaru (5)
        $recent = DB::table('students as s')
            ->leftJoin('users as u', 'u.id', '=', 's.guardian_user_id')
            ->leftJoin('admissions as a', 'a.student_id', '=', 's.id')
            ->orderByDesc('s.created_at')
            ->limit(5)
            ->get([
                's.id',
                's.full_name',
                'u.name as guardian_name',
                'u.phone as guardian_phone',
                'a.created_at as admission_created_at',
            ])
            ->map(function ($r) {
                return [
                    'id' => (int)$r->id,
                    'student_name' => $r->full_name,
                    'guardian_name' => $r->guardian_name ?? '-',
                    'guardian_phone' => $r->guardian_phone ?? '-',
                    'applied_at' => $r->admission_created_at ? date('Y-m-d H:i', strtotime($r->admission_created_at)) : '-',
                ];
            });

        return Inertia::render('admin/Dashboard', [
            'stats' => [
                'passMark' => $passMark,
                'totalStudents' => $totalStudents,
                'totalGuardians' => $totalGuardians,
                'totalAdmissions' => $totalAdmissions,
                'subjectsCount' => $subjectsCount,
                'completeCount' => $complete,
                'passCount' => $pass,
                'failCount' => $fail,
            ],
            'recent' => $recent,
        ]);
    }
}
