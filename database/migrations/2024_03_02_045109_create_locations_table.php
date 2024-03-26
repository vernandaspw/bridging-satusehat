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
        Schema::create('locations', function (Blueprint $table) {
            $table->id();
            $table->string('location_id');
            $table->string('identifier_value', 20)->nullable();
            $table->string('name');
            $table->string('ServiceUnitID', 20)->nullable();
            $table->string('RoomID', 10)->nullable();
            $table->string('RoomCode', 20)->nullable();
            $table->string('status');
            $table->string('organization_id');
            $table->text('description')->nullable();
            $table->string('part_of')->nullable();
            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();
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
        Schema::dropIfExists('locations');
    }
};
