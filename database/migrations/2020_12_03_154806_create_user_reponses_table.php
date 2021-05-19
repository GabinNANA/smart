<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserReponsesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_reponses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('idusers')->constrained('users')->nullable();
            $table->foreignId('idhabitation')->constrained('habitations')->nullable();
            $table->string('etat')->nullable();
            $table->foreignId('idquestion')->constrained('questions')->nullable();
            $table->Integer('idparent')->nullable();
            $table->Integer('isautre')->nullable();
            $table->text('response');
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
        Schema::dropIfExists('user_reponses');
    }
}
