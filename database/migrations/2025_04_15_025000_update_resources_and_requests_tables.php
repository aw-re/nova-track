<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add missing columns to resources table
        if (!Schema::hasColumn('resources', 'type')) {
            Schema::table('resources', function (Blueprint $table) {
                $table->string('type')->default('material')->after('name');
                $table->unsignedBigInteger('project_id')->nullable()->after('type');
                $table->unsignedBigInteger('user_id')->nullable()->after('project_id');
                $table->decimal('quantity', 10, 2)->default(0)->after('unit');
                $table->decimal('cost_per_unit', 10, 2)->default(0)->after('quantity');
                $table->string('image_path')->nullable()->after('cost_per_unit');
                
                // Add foreign key constraints
                $table->foreign('project_id')->references('id')->on('projects')->onDelete('set null');
                $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            });
        }

        // Update resource_requests table to match the model
        if (!Schema::hasColumn('resource_requests', 'resource_type')) {
            Schema::table('resource_requests', function (Blueprint $table) {
                $table->string('resource_type')->nullable()->after('resource_id');
                $table->string('resource_name')->nullable()->after('resource_type');
                $table->string('unit')->nullable()->after('quantity');
                $table->date('required_by')->nullable()->after('unit');
                $table->text('description')->nullable()->after('required_by');
                $table->string('document_path')->nullable()->after('description');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove added columns from resources table
        if (Schema::hasColumn('resources', 'type')) {
            Schema::table('resources', function (Blueprint $table) {
                $table->dropForeign(['project_id']);
                $table->dropForeign(['user_id']);
                $table->dropColumn(['type', 'project_id', 'user_id', 'quantity', 'cost_per_unit', 'image_path']);
            });
        }

        // Remove added columns from resource_requests table
        if (Schema::hasColumn('resource_requests', 'resource_type')) {
            Schema::table('resource_requests', function (Blueprint $table) {
                $table->dropColumn(['resource_type', 'resource_name', 'unit', 'required_by', 'description', 'document_path']);
            });
        }
    }
};
