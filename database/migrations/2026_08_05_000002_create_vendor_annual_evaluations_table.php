<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVendorAnnualEvaluationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vendor_annual_evaluations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('vendor_id');
            $table->integer('year'); // e.g. 2026

            // Rata-rata Skor Akhir 6 Aspek per Tahun
            $table->decimal('delivery_score_avg', 5, 2)->default(0.00);
            $table->decimal('quality_score_avg', 5, 2)->default(0.00);
            $table->decimal('quantity_score_avg', 5, 2)->default(0.00);
            $table->decimal('complain_score_avg', 5, 2)->default(100.00);
            $table->decimal('incoming_material_score_avg', 5, 2)->default(100.00);
            $table->decimal('safety_environment_score_avg', 5, 2)->default(100.00);

            // Skor Akhir & Status Kategori
            $table->decimal('final_score', 5, 2)->default(0.00);
            $table->enum('category', ['BAIK', 'CUKUP', 'KURANG'])->default('BAIK');
            $table->enum('status', ['draft', 'submitted', 'approved', 'rejected'])->default('draft');
            
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->text('notes')->nullable();

            // Flags Peringatan Alert
            $table->boolean('has_score_drop_alert')->default(false);        // Skor turun >= 20%
            $table->boolean('has_consecutive_low_alert')->default(false);    // 2 tahun berturut-turut KURANG
            $table->string('decision_status')->default('none');               // none, rekualifikasi_triggered, terminated, suspended, qualified_with_notes

            $table->timestamps();

            $table->unique(['vendor_id', 'year']);
            $table->foreign('vendor_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('approved_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vendor_annual_evaluations');
    }
}
