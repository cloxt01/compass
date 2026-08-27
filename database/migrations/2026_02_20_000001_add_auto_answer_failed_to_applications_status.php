<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tambah 'auto_answer_failed' ke enum status pada applications
        if (Schema::hasTable('applications')) {
            DB::statement("ALTER TABLE applications MODIFY status ENUM('success','applied','linkout','questionnaire','expired','auto_answer_failed') NOT NULL");
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('applications')) {
            DB::statement("ALTER TABLE applications MODIFY status ENUM('success','applied','linkout','questionnaire','expired') NOT NULL");
        }
    }
};
