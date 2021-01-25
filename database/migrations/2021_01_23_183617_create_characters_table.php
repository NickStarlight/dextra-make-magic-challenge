<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCharactersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('characters', function (Blueprint $table) {
            $table->uuid('id')->primary(); // Let's use a UUID so we can keep consistency with Potter API.
            $table->string('name', 255);
            $table->string('role', 255);
            $table->string('school', 255);
            $table->uuid('house'); // Potter API uses a 32 digit UUID that probably implements RFC 4211 (https://tools.ietf.org/html/rfc4211)
            $table->string('patronus', 255);
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
        Schema::dropIfExists('characters');
    }
}
