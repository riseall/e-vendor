<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Form menggantikan category_id sebagai sumber utama pertanyaan.
        // Set category_id nullable agar form-only rows (tanpa kategori) bisa disimpan.
        // Pakai raw SQL: Laravel 9 + aplikasi ini tidak membawa doctrine/dbal.
        DB::statement('ALTER TABLE vendor_audit_question_templates MODIFY COLUMN category_id TINYINT UNSIGNED NULL');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE vendor_audit_question_templates MODIFY COLUMN category_id TINYINT UNSIGNED NOT NULL');
    }
};
