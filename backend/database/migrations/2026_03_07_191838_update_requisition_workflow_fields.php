<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('job_requisitions', function (Blueprint $table) {
            $table->text('amendment_comment')->nullable()->after('rejection_reason');
            $table->unsignedBigInteger('md_approved_by')->nullable()->after('amendment_comment');
            $table->timestamp('md_approved_at')->nullable()->after('md_approved_by');
            $table->unsignedBigInteger('hr_approved_by')->nullable()->after('md_approved_at');
            $table->timestamp('hr_approved_at')->nullable()->after('hr_approved_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('job_requisitions', function (Blueprint $table) {
            $table->dropColumn(['amendment_comment', 'md_approved_by', 'md_approved_at', 'hr_approved_by', 'hr_approved_at']);
        });
    }
};
