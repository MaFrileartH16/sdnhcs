<?php

namespace App\Http\Controllers;

use App\Models\Admission;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Throwable;

class AdmissionController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'guardian_name' => ['required', 'string', 'max:100'],
            'guardian_relation' => ['required', 'in:father,mother,guardian'],
            'phone' => ['required', 'string', 'min:9', 'max:20'],
            'full_name' => ['required', 'string', 'max:120'],
            'gender' => ['required', 'in:male,female'],
            'admission_form_file' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:2048'],
            'family_card_file' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:2048'],
            'birth_certificate_file' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:2048'],
            'guardian_id_card_file' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:2048'],
            'kindergarten_certificate_file' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:2048'],
        ]);

        try {
            DB::transaction(function () use ($data, $request) {
                $user = User::firstOrCreate(
                    ['phone' => $data['phone']],
                    ['name' => $data['guardian_name'], 'role' => 'guardian', 'password' => Hash::make($data['phone'])]
                );

                if (!$user->wasRecentlyCreated) {
                    $user->fill(['name' => $data['guardian_name'], 'role' => 'guardian'])->save();
                }

                $student = Student::create([
                    'guardian_user_id' => $user->id,
                    'full_name' => $data['full_name'],
                    'gender' => $data['gender'],
                    'guardian_relation' => $data['guardian_relation'],
                ]);

                if (Admission::where('student_id', $student->id)->exists()) {
                    throw ValidationException::withMessages([
                        'full_name' => 'Pendaftaran untuk siswa ini sudah ada.',
                    ]);
                }

                $dir = 'admissions/' . $student->id;

                Admission::create([
                    'student_id' => $student->id,
                    'admission_form_file' => $request->file('admission_form_file')->store($dir, 'public'),
                    'family_card_file' => $request->file('family_card_file')->store($dir, 'public'),
                    'birth_certificate_file' => $request->file('birth_certificate_file')->store($dir, 'public'),
                    'guardian_id_card_file' => $request->file('guardian_id_card_file')->store($dir, 'public'),
                    'kindergarten_certificate_file' => $request->file('kindergarten_certificate_file')->store($dir, 'public'),
                    'average_score' => 0,
                ]);
            });

            // flash sukses
            return back()->with('success', 'Pendaftaran berhasil. Anda akan diarahkan ke halaman masuk.');
        } catch (Throwable $e) {
            report($e);

            // flash gagal
            return back()->with('error', 'Terjadi kesalahan saat menyimpan pendaftaran. Silakan coba lagi.');
        }
    }
}
