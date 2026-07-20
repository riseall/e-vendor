<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVendorAuditsTable extends Migration
{
    public function up()
    {
        Schema::create('vendor_audits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_application_id')
                ->constrained('vendor_applications')
                ->onDelete('cascade');
            $table->foreignId('vendor_qualification_id')
                ->nullable()
                ->constrained('vendor_qualifications')
                ->nullOnDelete();

            // Tipe audit: on_desk (Medium) atau on_site (High)
            $table->enum('audit_type', ['on_desk', 'on_site']);

            // Status keseluruhan audit
            $table->enum('status', [
                'scheduled',         // baru di-trigger, menunggu vendor
                'questionnaire_in_progress', // on_desk: vendor sedang mengisi
                'questionnaire_submitted',   // on_desk: vendor sudah submit
                'schedule_proposed', // on_site: QA kirim proposed date
                'schedule_confirmed', // on_site: vendor pilih tanggal
                'in_progress',       // on_site: pelaksanaan audit
                'findings_recorded', // on_site: QA sudah input temuan
                'capa_in_progress',  // vendor sedang isi CAPA
                'capa_submitted',    // vendor sudah submit CAPA
                'capa_revised',      // vendor revisi CAPA setelah reject
                'need_revision',     // questionnaire/CAPA perlu direvisi vendor
                'completed',         // audit selesai, hasil akhir approved
                'rejected',          // QA reject vendor
            ])->default('scheduled');

            // Audit on desk: tanggal submit questionnaire
            $table->timestamp('questionnaire_submitted_at')->nullable();
            $table->json('questionnaire_payload')->nullable();
            $table->json('questionnaire_revision_notes')->nullable();

            // Audit on site: proposed schedule (multiple), confirmed schedule
            $table->json('proposed_schedules')->nullable();
            $table->timestamp('confirmed_schedule_at')->nullable();
            $table->string('audit_location')->nullable();
            $table->json('auditor_team')->nullable();           // [{name, role}, ...]
            $table->string('audit_agenda')->nullable();

            // Surat pemberitahuan audit (PDF) - path relatif ke storage
            $table->string('audit_letter_path')->nullable();
            $table->timestamp('audit_letter_sent_at')->nullable();

            // Checklist persiapan vendor (on_site)
            $table->json('preparation_checklist')->nullable();
            $table->timestamp('preparation_submitted_at')->nullable();

            // Hasil & keputusan akhir
            $table->text('summary')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->string('audit_result_path')->nullable();
            $table->enum('audit_result_category', ['terekomendasi', 'tdk_rekomendasi', 'on_hold'])->nullable();

            // QA yang handle
            $table->foreignId('qa_lead_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();

            $table->index(['vendor_application_id', 'audit_type']);
            $table->index('status');
        });

        // Temuan audit (untuk on_site)
        Schema::create('vendor_audit_findings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_audit_id')
                ->constrained('vendor_audits')
                ->onDelete('cascade');
            $table->string('category', 80)->nullable(); // Critical / Major / Minor / Observasi
            $table->text('description');
            $table->text('evidence_reference')->nullable();
            $table->date('capa_deadline')->nullable();
            $table->enum('status', ['open', 'closed', 'accepted'])->default('open');
            $table->timestamps();
        });

        // CAPA per temuan
        Schema::create('vendor_audit_capas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_audit_finding_id')
                ->constrained('vendor_audit_findings')
                ->onDelete('cascade');
            $table->text('corrective_action')->nullable();
            $table->text('preventive_action')->nullable();
            $table->json('attachments')->nullable();           // path file bukti
            $table->timestamp('submitted_at')->nullable();
            $table->foreignId('submitted_by')->nullable()->constrained('users')->nullOnDelete();

            // Verifikasi per item CAPA oleh QA
            $table->enum('qa_verdict', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('qa_note')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('verified_at')->nullable();

            $table->timestamps();
        });

        // Template pertanyaan questionnaire (Master) — dipakai untuk generate kuesioner dinamis per kategori
        Schema::create('vendor_audit_question_templates', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('category_id'); // sesuai VendorApplication::CATEGORY_LABELS
            $table->string('section', 80);              // misal: "Dokumen Mutu", "Personel", dll
            $table->text('question');
            $table->enum('answer_type', ['yes_no', 'multiple_choice', 'text', 'document']);
            $table->json('options')->nullable();        // untuk multiple_choice
            $table->unsignedTinyInteger('weight')->default(1);
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('order')->default(0);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->index(['category_id', 'is_active', 'order'], 'vaqt_cat_act_ord_idx');
        });
    }

    public function down()
    {
        Schema::dropIfExists('vendor_audit_question_templates');
        Schema::dropIfExists('vendor_audit_capas');
        Schema::dropIfExists('vendor_audit_findings');
        Schema::dropIfExists('vendor_audits');
    }
}
