<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('bills', function (Blueprint $table) {
            $table->id();
            $table->string('subscriber_no');
            $table->integer('month');
            $table->integer('year');
            $table->decimal('total_amount', 10, 2);
            $table->decimal('phone_amount', 10, 2);
            $table->decimal('internet_amount', 10, 2);
            $table->decimal('paid_amount', 10, 2)->default(0);
            $table->boolean('is_paid')->default(false);
            $table->timestamps();
    
            $table->unique(['subscriber_no', 'month', 'year']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bills');
    }
};
