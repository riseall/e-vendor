<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Master form: 1 row = 1 checklist (mis. "Bahan Baku - Pemasok")
        Schema::create('vendor_audit_questionnaire_forms', function (Blueprint $table) {
            $table->id();
            $table->string('code', 80)->unique();              // bahan_baku_pemasok, kemas_primer, ...
            $table->string('name');                            // "Daftar Periksa Pemasok Bahan"
            $table->enum('material_type', ['bahan_baku', 'bahan_kemas', 'produk_jadi', 'alkes']);
            $table->string('document_number', 80)->nullable(); // QA-FM-I2.02-017
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('order')->default(0);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->index(['material_type', 'is_active']);
        });

        // Tambah form_id ke template pertanyaan yang sudah ada
        // category_id di-set nullable di migration terpisah 2026_07_10_000002
        Schema::table('vendor_audit_question_templates', function (Blueprint $table) {
            $table->foreignId('form_id')
                ->nullable()
                ->after('id')
                ->constrained('vendor_audit_questionnaire_forms')
                ->cascadeOnDelete();
            $table->boolean('is_required')->default(false)->after('weight');
            $table->string('depends_on_question_code', 50)->nullable()->after('is_required');
            $table->index(['form_id', 'is_active', 'order']);
        });

        // Audit: simpan form mana yang dipakai
        Schema::table('vendor_audits', function (Blueprint $table) {
            $table->foreignId('questionnaire_form_id')
                ->nullable()
                ->after('audit_type')
                ->constrained('vendor_audit_questionnaire_forms')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('vendor_audits', function ($t) {
            $t->dropConstrainedForeignId('questionnaire_form_id');
        });

        Schema::table('vendor_audit_question_templates', function ($t) {
            $t->dropConstrainedForeignId('form_id');
            $t->dropColumn(['is_required', 'depends_on_question_code']);
        });

        Schema::dropIfExists('vendor_audit_questionnaire_forms');
    }
};
