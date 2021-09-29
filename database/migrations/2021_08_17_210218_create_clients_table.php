<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->id('id');
            $table->string('name');
            $table->string('surname');
            $table->string('cif')->unique();
            $table->longText('image')->nullable();
            $table->string('idUser');
            $table->string('lastUserWhoModifiedTheField')->useCurrentOnUpdate(); // save the ID of the last user who modified the client.   mCIdUser
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
        Schema::dropIfExists('clients');
    }
}
