<?php

use App\Models\Lending;
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
        Schema::create('lendings', function (Blueprint $table) {
            $table->primary(['user_id', 'copy_id', 'start']);
            //létrehozza a mezőt és össze is köti a megf. tábla megf. mezőjével
            $table->foreignId('user_id')->references('id')->on('users');
            $table->foreignId('copy_id')->references('copy_id')->on('copies');
            $table->date("start");
            //default null az értéke
            $table->date("end")->nullable();
            $table->boolean('extension')->default(0);
            $table->integer('notice')->default(0);
            $table->timestamps();
        });

        Lending::create(['user_id'=> 2, 'copy_id' => 1, 'start'=> '2022-10-06']);
        Lending::create(['user_id'=> 2, 'copy_id' => 2, 'start'=> '2022-11-06']);
        Lending::create(['user_id'=> 2, 'copy_id' => 1, 'start'=> '2022-10-07', 'end' => '2022-12-06', 'notice' => 1]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('lendings');
    }
};
