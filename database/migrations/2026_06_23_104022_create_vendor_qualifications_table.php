<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVendorQualificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vendor_qualifications', function (Blueprint $blueprint) {
            $blueprint->id();

            // Relasi ke tabel header permohonan vendor
            $blueprint->foreignId('vendor_application_id')
                ->unique()
                ->constrained('vendor_applications')
                ->onDelete('cascade');

            // Komponen formulir fisik risk assessment supplier.
            $blueprint->integer('score_safety_efficacy_doc')->nullable()->comment('A1 Kelengkapan Dokumen');
            $blueprint->integer('score_safety_efficacy_attr')->nullable()->comment('A2 Critical Attribute');
            $blueprint->integer('score_safety_efficacy')->comment('Total Nilai Safety Efficacy');
            $blueprint->integer('score_availability_trace')->nullable()->comment('B1 Traceability Supply Chain');
            $blueprint->integer('score_availability_type')->nullable()->comment('B2 Jenis Pemasok');
            $blueprint->integer('score_availability')->comment('Total Nilai Availability');
            $blueprint->integer('score_detectability_country')->nullable()->comment('C1 Country / Regulatory Risk');
            $blueprint->integer('score_detectability_warning')->nullable()->comment('C2 Warning Letter / Hasil Audit');
            $blueprint->integer('score_detectability')->comment('Total Nilai Detectability');
            $blueprint->integer('score_probability_function')->nullable()->comment('D1 Fungsi Bahan');
            $blueprint->integer('score_probability')->comment('Total Nilai Probability');

            // Rumus: (Safety Efficacy + Availability) x (Detectability + Probability)
            $blueprint->integer('total_score')->comment('Total Nilai Pemasok');

            // Kategori Tingkat Risiko berdasarkan threshold konfigurasi.
            $blueprint->enum('risk_level', ['low', 'medium', 'high'])->comment('Kategori: Low, Medium, High');
            $blueprint->enum('audit_type', ['on_desk', 'on_site'])->nullable()->comment('Medium: on desk, High: on site');

            // Tim QA Penilai (Apoteker QA & Manager QA) sesuai Form Fisik
            $blueprint->unsignedBigInteger('qa_pharmacist_id')->nullable()->comment('Apoteker QA yang menilai');
            $blueprint->unsignedBigInteger('qa_manager_id')->nullable()->comment('Manager QA yang menyetujui');

            // Catatan tambahan atau Keterangan Form
            $blueprint->text('notes')->nullable()->comment('Keterangan / Catatan QA');

            $blueprint->timestamps();

            // Definisi Foreign Key untuk Tim QA (Asumsi merujuk ke tabel users)
            $blueprint->foreign('qa_pharmacist_id')->references('id')->on('users')->onDelete('set null');
            $blueprint->foreign('qa_manager_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vendor_qualifications');
    }
}
