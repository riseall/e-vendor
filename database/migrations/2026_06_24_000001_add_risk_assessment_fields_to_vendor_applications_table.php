<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddRiskAssessmentFieldsToVendorApplicationsTable extends Migration
{
    public function up()
    {
        DB::statement("ALTER TABLE vendor_applications MODIFY status ENUM('draft','submitted','need_revision','verified','risk_assessed','audit_required','on_hold','approved','rejected') NOT NULL DEFAULT 'draft'");

        Schema::table('vendor_applications', function (Blueprint $table) {
            if (!Schema::hasColumn('vendor_applications', 'risk_level')) {
                $table->enum('risk_level', ['low', 'medium', 'high'])->nullable()->after('auto_verified');
            }

            if (!Schema::hasColumn('vendor_applications', 'risk_rpn')) {
                $table->unsignedInteger('risk_rpn')->nullable()->after('risk_level');
            }

            if (!Schema::hasColumn('vendor_applications', 'approved_by')) {
                $table->foreignId('approved_by')->nullable()->after('risk_rpn')->constrained('users')->nullOnDelete();
            }

            if (!Schema::hasColumn('vendor_applications', 'approved_at')) {
                $table->timestamp('approved_at')->nullable()->after('approved_by');
            }

            if (!Schema::hasColumn('vendor_applications', 'valid_until')) {
                $table->date('valid_until')->nullable()->after('approved_at');
            }
        });
    }

    public function down()
    {
        Schema::table('vendor_applications', function (Blueprint $table) {
            if (Schema::hasColumn('vendor_applications', 'valid_until')) {
                $table->dropColumn('valid_until');
            }

            if (Schema::hasColumn('vendor_applications', 'approved_at')) {
                $table->dropColumn('approved_at');
            }

            if (Schema::hasColumn('vendor_applications', 'approved_by')) {
                $table->dropConstrainedForeignId('approved_by');
            }

            if (Schema::hasColumn('vendor_applications', 'risk_rpn')) {
                $table->dropColumn('risk_rpn');
            }

            if (Schema::hasColumn('vendor_applications', 'risk_level')) {
                $table->dropColumn('risk_level');
            }
        });

        DB::statement("ALTER TABLE vendor_applications MODIFY status ENUM('draft','submitted','need_revision','verified','approved','rejected') NOT NULL DEFAULT 'draft'");
    }
}
