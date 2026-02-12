<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('questionnaire', function (Blueprint $table) {
            $table->increments('id'); // Ini otomatis int(11) NOT NULL AUTO_INCREMENT
            $table->string('question_id');
            $table->string('question_text');
            $table->string('answer_id')->nullable();
            $table->string('answer_text')->nullable();
            $table->string('last_answer')->nullable();
            $table->longText('options')->nullable();
        });
    }

    public function down(): void {
        Schema::dropIfExists('questionnaire');
    }
};