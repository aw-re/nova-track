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
        Schema::table('task_updates', function (Blueprint $table) {
            $table->text('comment')->nullable()->after('user_id');
        });
    }
    
    public function down()
    {
        Schema::table('task_updates', function (Blueprint $table) {
            $table->dropColumn('comment');
        });
    }
};
