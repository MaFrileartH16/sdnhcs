<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('admissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete()->unique('uniq_admission_per_student');
            $table->string('admission_form_file');
            $table->string('family_card_file');
            $table->string('birth_certificate_file');
            $table->string('guardian_id_card_file');
            $table->string('kindergarten_certificate_file');
            $table->unsignedTinyInteger('average_score')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admissions');
    }
};
