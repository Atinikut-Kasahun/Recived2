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
        Schema::table('applicants', function (Blueprint $table) {
            $table->decimal('written_exam_score', 5, 2)->nullable()->after('status');
            $table->decimal('technical_interview_score', 5, 2)->nullable()->after('written_exam_score');
            $table->text('interviewer_feedback')->nullable()->after('technical_interview_score');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('applicants', function (Blueprint $table) {
            $table->dropColumn(['written_exam_score', 'technical_interview_score', 'interviewer_feedback']);
        });
    }
};
