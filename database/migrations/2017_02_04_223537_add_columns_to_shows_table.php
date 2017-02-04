<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsToShowsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('shows', function (Blueprint $table) {
            $table->enum('updatePeriod', ['hourly','daily','weekly','monthly','yearly'])->default('daily');
            $table->integer('updateFrequency')->default(1);
            $table->string('category')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('shows', function (Blueprint $table) {
            $table->dropColumn('updatePeriod');
            $table->dropColumn('updateFrequency');
            $table->dropColumn('category');
        });
    }
}
