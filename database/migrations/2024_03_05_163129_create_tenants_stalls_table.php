<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tenants_stalls', function (Blueprint $table) {
            $table->unsignedBigInteger('tenant_id')->index();
            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->unsignedBigInteger('stall_id')->index();
            $table->foreign('stall_id')->references('id')->on('stalls')->onDelete('cascade');
            $table->primary(['tenant_id', 'stall_id']);
            $table->string('rental_start')->nullable();
            $table->string('rental_end')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tenants_stalls');
    }
};
