<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class OutputEquipement extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('output_equipement', function (Blueprint $table) {
            $table->id();
            $table->foreignId('idcategorie')->constrained('categories')->nullable();
            $table->text('objet');
            $table->text('type');
            $table->text('moment_frequence');
            $table->text('personne_organisme');
            $table->text('document_completer');
            $table->text('reference');
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
        //
    }
}
