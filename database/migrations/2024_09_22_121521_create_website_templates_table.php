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
        Schema::create('website_templates', function (Blueprint $table) {
            $table->id();
            $table->string('about_title')->nullable();
            $table->text('about_description')->nullable();
            $table->unsignedBigInteger('about_media_id')->default(0);
            $table->string('contact_email')->nullable();
            $table->string('contact_phone', 15)->nullable();
            $table->string('contact_location')->nullable();
            $table->text('terms_description')->nullable();
            $table->text('privacy_description')->nullable();
            $table->unsignedBigInteger("user_id")->default(0);
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
        Schema::dropIfExists('website_templates');
    }
};
