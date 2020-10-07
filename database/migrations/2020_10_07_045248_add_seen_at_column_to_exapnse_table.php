<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSeenAtColumnToExapnseTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('expanses', function (Blueprint $table) {
            $table->date('seen_at')->nullable()->after('seen');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('expanses', function (Blueprint $table) {
            $table->dropColumn('seen_at');
        });
    }
}
