<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAbonnementUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('abonnement_users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('idabonnement')->constrained('abonnements');
            $table->foreignId('iduser')->constrained('users');
            $table->date('datedeb');
            $table->date('datefin');
            $table->date('montant');
            $table->Integer('etat');
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
        Schema::dropIfExists('abonnement_users');
    }
}
