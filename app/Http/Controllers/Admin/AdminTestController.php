<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class AdminTestController extends Controller
{
    public function index(Request $request): Response
    {
        $passMark = (int)(DB::table('exam_settings')->value('pass_mark') ?? 70);

        $subjects = DB::table('exam_subjects')
            ->select('id', 'name', 'link_url')
            ->orderBy('name')
            ->get();

        return Inertia::render('admin/Tests', [
            'settings' => ['passMark' => $passMark],
            'subjects' => $subjects,
        ]);
    }

    public function updateSettings(Request $request)
    {
        $data = $request->validate([
            'pass_mark' => ['required', 'integer', 'min:0', 'max:100'],
        ]);

        if (DB::table('exam_settings')->exists()) {
            DB::table('exam_settings')->update([
                'pass_mark' => $data['pass_mark'],
                'updated_at' => now(),
            ]);
        } else {
            DB::table('exam_settings')->insert([
                'pass_mark' => $data['pass_mark'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // tanpa flash: kembali ke halaman yang sama
        return redirect()->route('admin.tests.index');
    }

    public function storeSubject(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:80', 'unique:exam_subjects,name'],
            'link_url' => ['required', 'url', 'max:255'],
        ]);

        DB::table('exam_subjects')->insert([
            'name' => $data['name'],
            'link_url' => $data['link_url'],
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('admin.tests.index');
    }

    public function updateSubject(Request $request, int $id)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:80', 'unique:exam_subjects,name,' . $id],
            'link_url' => ['required', 'url', 'max:255'],
        ]);

        DB::table('exam_subjects')->where('id', $id)->update([
            'name' => $data['name'],
            'link_url' => $data['link_url'],
            'updated_at' => now(),
        ]);

        return redirect()->route('admin.tests.index');
    }

    public function destroySubject(int $id)
    {
        DB::table('exam_subjects')->where('id', $id)->delete();
        return redirect()->route('admin.tests.index');
    }
}
