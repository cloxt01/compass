<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('application_ai_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('application_id')->nullable()->index();
            $table->string('job_id')->index();
            $table->string('job_title')->nullable();
            $table->enum('provider', ['glints', 'jobstreet']);
            $table->string('model')->nullable();

            // Normalized questionnaire yang dikirim ke AI
            $table->json('questionnaire')->nullable();
            // Profile pengguna yang dikirim ke AI (untuk transparansi)
            $table->json('profile')->nullable();

            // Prompt lengkap (system + user)
            $table->json('prompt')->nullable();
            // Raw response AI (parsed JSON)
            $table->json('raw_response')->nullable();
            // Jawaban akhir yang dipakai submit ke provider
            $table->json('final_answers')->nullable();
            // Per-question breakdown: question, answer, confidence, source, missing_info
            $table->json('per_question')->nullable();

            // Skor kecocokan (0-100)
            $table->unsignedTinyInteger('match_score')->default(0);
            // Jumlah pertanyaan yang tidak bisa dijawab (null)
            $table->unsignedSmallInteger('unanswered_count')->default(0);
            // Total pertanyaan
            $table->unsignedSmallInteger('total_questions')->default(0);

            // Usage
            $table->unsignedInteger('tokens_prompt')->nullable();
            $table->unsignedInteger('tokens_completion')->nullable();
            $table->unsignedInteger('tokens_total')->nullable();
            $table->unsignedInteger('duration_ms')->nullable();

            $table->enum('status', ['success', 'failed'])->default('success');
            $table->text('error_message')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('application_ai_answers');
    }
};
