<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEvaluationSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('evaluation_settings', function (Blueprint $table) {
            $table->id();
            
            // Bobot Persentase Aspek (%)
            $table->decimal('weight_delivery', 5, 2)->default(20.00);
            $table->decimal('weight_quality', 5, 2)->default(20.00);
            $table->decimal('weight_quantity', 5, 2)->default(20.00);
            $table->decimal('weight_complain', 5, 2)->default(15.00);
            $table->decimal('weight_incoming_material', 5, 2)->default(15.00);
            $table->decimal('weight_safety_environment', 5, 2)->default(10.00);

            // Threshold Kategori Skor (Default: BAIK >= 80, CUKUP 60-79, KURANG < 60)
            $table->decimal('threshold_baik', 5, 2)->default(80.00);
            $table->decimal('threshold_cukup', 5, 2)->default(60.00);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('evaluation_settings');
    }
}
