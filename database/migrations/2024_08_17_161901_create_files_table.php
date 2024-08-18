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
        Schema::create('files', function (Blueprint $table) {
            $table->id();
           

            $table->string('name');
            $table->unsignedBigInteger('directory_id');
            $table->foreign('directory_id')->references('id')->on('directories')->onDelete('cascade');

            $table->unsignedBigInteger('syncItem_id');
            $table->foreign('syncItem_id')->references('id')->on('sync_items')->onDelete('cascade');

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
        Schema::dropIfExists('files');
    }
};
