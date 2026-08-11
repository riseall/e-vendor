<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVendorEvaluationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vendor_evaluations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('vendor_id');
            $table->integer('month'); // 1 - 12
            $table->integer('year');  // e.g. 2026
            
            // Skor Akhir 6 Aspek (0.00 - 100.00)
            $table->decimal('delivery_score', 5, 2)->default(0.00);            // 20% (QAD)
            $table->decimal('quality_score', 5, 2)->default(0.00);             // 20% (QAD)
            $table->decimal('quantity_score', 5, 2)->default(0.00);            // 20% (QAD)
            $table->decimal('complain_score', 5, 2)->default(100.00);          // 15% (QA Input)
            $table->decimal('incoming_material_score', 5, 2)->default(100.00); // 15% (QA Input)
            $table->decimal('safety_environment_score', 5, 2)->default(100.00);// 10% (QA Input)

            // Skor Total Terbobot & Kategori
            $table->decimal('total_score', 5, 2)->default(0.00);
            $table->enum('category', ['BAIK', 'CUKUP', 'KURANG'])->default('BAIK');
            
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();

            $table->unique(['vendor_id', 'month', 'year']);
            $table->foreign('vendor_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vendor_evaluations');
    }
}
