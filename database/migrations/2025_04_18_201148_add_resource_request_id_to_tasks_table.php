<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::table('tasks', function (Blueprint $table) {
        $table->unsignedBigInteger('resource_request_id')->nullable()->after('project_id');
        $table->foreign('resource_request_id')->references('id')->on('resource_requests')->onDelete('set null');
    });
}

public function down()
{
    Schema::table('tasks', function (Blueprint $table) {
        $table->dropForeign(['resource_request_id']);
        $table->dropColumn('resource_request_id');
    });
}
};
