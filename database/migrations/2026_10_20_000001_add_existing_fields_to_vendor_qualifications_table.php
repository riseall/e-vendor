<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddExistingFieldsToVendorQualificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('vendor_qualifications', function (Blueprint $table) {
            $table->string('assessment_type', 20)->default('new')->after('vendor_application_id')->comment('new / existing');

            // Section A: Safety Efficacy
            $table->tinyInteger('score_complaint_side_effect')->nullable()->after('score_safety_efficacy_attr')->comment('A2 Berita Keluhan Efek Samping');

            // Section B: Availability
            $table->tinyInteger('score_pareto')->nullable()->after('score_availability_type')->comment('B1 Produsen Obat Pareto');
            $table->tinyInteger('score_purchase_volume')->nullable()->after('score_pareto')->comment('B2 Volume Pembelian');
            $table->tinyInteger('score_nie_status')->nullable()->after('score_purchase_volume')->comment('B4 Status NIE');

            // Section C: Detectability
            $table->tinyInteger('score_audit_year')->nullable()->after('score_detectability_warning')->comment('C1 Tahun Terakhir Audit');
            $table->tinyInteger('score_audit_result')->nullable()->after('score_audit_year')->comment('C2 Hasil Audit');
            $table->tinyInteger('score_audit_response')->nullable()->after('score_audit_result')->comment('C3 Respon Hasil Audit');
            $table->tinyInteger('score_complaint_count')->nullable()->after('score_audit_response')->comment('C4 Jumlah Complain');

            // Section D: Probability
            $table->tinyInteger('score_material_grade')->nullable()->after('score_probability_function')->comment('D1 Grade Bahan Baku');
            $table->tinyInteger('score_facility_variation')->nullable()->after('score_material_grade')->comment('D2 Variasi Produk Fasilitas');
            $table->tinyInteger('score_material_purpose')->nullable()->after('score_facility_variation')->comment('D3 Peruntukan Bahan Baku');

            // Metadata & Outcomes
            $table->tinyInteger('re_audit_period_years')->nullable()->after('audit_type')->comment('Jangka Re-audit dalam tahun: 7, 5, 3, 2');
            $table->boolean('needs_quality_agreement')->default(false)->after('re_audit_period_years')->comment('Flag kewajiban Quality Agreement');
        });

        // Ubah enum risk_level agar mendukung very_high
        DB::statement("ALTER TABLE vendor_qualifications MODIFY COLUMN risk_level ENUM('low', 'medium', 'high', 'very_high') NOT NULL DEFAULT 'low'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("ALTER TABLE vendor_qualifications MODIFY COLUMN risk_level ENUM('low', 'medium', 'high') NOT NULL DEFAULT 'low'");

        Schema::table('vendor_qualifications', function (Blueprint $table) {
            $table->dropColumn([
                'assessment_type',
                'score_complaint_side_effect',
                'score_pareto',
                'score_purchase_volume',
                'score_nie_status',
                'score_audit_year',
                'score_audit_result',
                'score_audit_response',
                'score_complaint_count',
                'score_material_grade',
                'score_facility_variation',
                'score_material_purpose',
                're_audit_period_years',
                'needs_quality_agreement',
            ]);
        });
    }
}
