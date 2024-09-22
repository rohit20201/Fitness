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
        Schema::create('banners', function (Blueprint $table) {
            $table->id();
            $table->string('name')->index();
            $table->text('description');
            $table->unsignedBigInteger("user_id")->default(0)->index()->comment("Refers to users.id");
            $table->unsignedBigInteger('media_id')->default(0)->comment('Refers to media_links.id');
            $table->tinyInteger("status")->default(1)->index()->comment("0=pending,1=approved,2=rejected");
            $table->unsignedTinyInteger("active")->default(1)->index();
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
        Schema::dropIfExists('banners');
    }
};
