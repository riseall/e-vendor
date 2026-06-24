<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRiskAssessmentDetailScores extends Migration
{
    public function up()
    {
        Schema::table('vendor_qualifications', function (Blueprint $table) {
            $columns = [
                'score_safety_efficacy_doc',
                'score_safety_efficacy_attr',
                'score_availability_trace',
                'score_availability_type',
                'score_detectability_country',
                'score_detectability_warning',
                'score_probability_function',
            ];

            foreach ($columns as $column) {
                if (!Schema::hasColumn('vendor_qualifications', $column)) {
                    $table->integer($column)->nullable();
                }
            }
        });
    }

    public function down()
    {
        Schema::table('vendor_qualifications', function (Blueprint $table) {
            foreach ([
                'score_probability_function',
                'score_detectability_warning',
                'score_detectability_country',
                'score_availability_type',
                'score_availability_trace',
                'score_safety_efficacy_attr',
                'score_safety_efficacy_doc',
            ] as $column) {
                if (Schema::hasColumn('vendor_qualifications', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
}
