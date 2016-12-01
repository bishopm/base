<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsToUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function($table) {
            $table->integer('individual_id')->nullable();
            $table->string('toodledo_id')->nullable();
            $table->string('toodledo_token')->nullable();
            $table->string('toodledo_refresh')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function($table) {
            $table->dropColumn('individual_id');
            $table->dropColumn('toodledo_id');
            $table->dropColumn('toodledo_token');
            $table->dropColumn('toodledo_refresh');
        });
    }
}