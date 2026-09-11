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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId("clint_id")->constract("clints")->onDelete();
            $table->foreignId("webhook_id")->constract("webhooks")->onDelete();
            $table->string("reference")->nullable();
            $table->integer("amount")->nullable();
            $table->string("transaction_date")->nullable();
            $table->string("metadata")->nullable();
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
