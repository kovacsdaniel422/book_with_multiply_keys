<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reservations', function (Blueprint $table) {
            $table->primary(['book_id', 'user_id', 'start']);
            //létrehozza a mezőt és össze is köti a megf. tábla megf. mezőjével
            $table->foreignId('book_id')->references('book_id')->on('books');
            $table->foreignId('user_id')->references('id')->on('users');
            $table->date("start");
            $table->boolean('message')->default(0);
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
        Schema::dropIfExists('reservations');
    }
};
