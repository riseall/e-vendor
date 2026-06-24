<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class UpdateVendorQualificationsForRpnSchema extends Migration
{
    public function up()
    {
        Schema::table('vendor_qualifications', function (Blueprint $table) {
            if (!Schema::hasColumn('vendor_qualifications', 'score_safety_efficacy')) {
                $table->integer('score_safety_efficacy')->nullable()->after('vendor_application_id');
            }

            if (!Schema::hasColumn('vendor_qualifications', 'score_availability')) {
                $table->integer('score_availability')->nullable()->after('score_safety_efficacy');
            }

            if (!Schema::hasColumn('vendor_qualifications', 'score_detectability')) {
                $table->integer('score_detectability')->nullable()->after('score_availability');
            }

            if (!Schema::hasColumn('vendor_qualifications', 'score_probability')) {
                $table->integer('score_probability')->nullable()->after('score_detectability');
            }

            if (!Schema::hasColumn('vendor_qualifications', 'total_score')) {
                $table->integer('total_score')->nullable()->after('score_probability');
            }

            if (!Schema::hasColumn('vendor_qualifications', 'audit_type')) {
                $table->enum('audit_type', ['on_desk', 'on_site'])->nullable()->after('risk_level');
            }
        });

        foreach ([
            'score_safety_efficacy',
            'score_availability',
            'score_detectability',
            'score_probability',
            'total_score',
        ] as $column) {
            if (Schema::hasColumn('vendor_qualifications', $column)) {
                DB::statement("ALTER TABLE vendor_qualifications MODIFY {$column} INT NULL");
            }
        }

        foreach ([
            'impact_score',
            'probability_score',
            'detectability_score',
            'rpn',
        ] as $column) {
            if (Schema::hasColumn('vendor_qualifications', $column)) {
                DB::statement("ALTER TABLE vendor_qualifications MODIFY {$column} INT NULL");
            }
        }
    }

    public function down()
    {
        Schema::table('vendor_qualifications', function (Blueprint $table) {
            if (Schema::hasColumn('vendor_qualifications', 'audit_type')) {
                $table->dropColumn('audit_type');
            }
        });
    }
}
