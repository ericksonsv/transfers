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
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->constrained()
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->foreignId('order_id')
                ->constrained()
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->foreignId('service_currency_id')->nullabel()->constrained();
            $table->foreignId('service_type_id')->nullabel()->constrained();
            $table->foreignId('service_status_id')->nullable()->constrained();
            $table->string('client');
            $table->date('pickup_date')->nullable();
            $table->time('pickup_time')->nullable();
            $table->string('pickup_place')->nullable();
            $table->string('dropoff_place')->nullable();
            $table->string('flight_number')->nullable();
            $table->time('flight_time')->nullable();
            $table->unsignedInteger('passengers')->nullable();
            $table->decimal('amount',8,2)->nullable();
            $table->text('note')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
