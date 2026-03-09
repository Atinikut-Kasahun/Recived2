<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('applicants', function (Blueprint $table) {
            // Track employment status for hired applicants
            // 'active' | 'resigned' | 'terminated'
            $table->string('employment_status')->default('active')->after('hired_at');
            $table->date('separation_date')->nullable()->after('employment_status');
            $table->string('separation_reason')->nullable()->after('separation_date');

            // Index for turnover queries
            $table->index(['tenant_id', 'employment_status', 'separation_date'], 'applicants_turnover_idx');
        });
    }

    public function down(): void
    {
        Schema::table('applicants', function (Blueprint $table) {
            $table->dropIndex('applicants_turnover_idx');
            $table->dropColumn(['employment_status', 'separation_date', 'separation_reason']);
        });
    }
};
