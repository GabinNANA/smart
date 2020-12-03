<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOutputsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('outputs', function (Blueprint $table) {
            $table->id();
            $table->text('classement');
            $table->text('standard');
            $table->text('livrable')->nullable();
            $table->text('validite')->nullable();
            $table->text('delai')->nullable();
            $table->text('cout_etude')->nullable();
            $table->text('frais_admin')->nullable();
            $table->text('penalite')->nullable();
            $table->text('ispayer');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('outputs');
    }
}
