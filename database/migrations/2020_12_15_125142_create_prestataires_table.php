<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePrestatairesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('prestataires', function (Blueprint $table) {
            $table->id();
            $table->string('type');
            $table->string('domaine');
            $table->string('nom');
            $table->string('telephone');
            $table->string('email');
            $table->string('adresse');
            $table->string('pays');
            $table->string('logo');
            $table->text('description');
            $table->Integer('isavant');
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
        Schema::dropIfExists('prestataires');
    }
}
