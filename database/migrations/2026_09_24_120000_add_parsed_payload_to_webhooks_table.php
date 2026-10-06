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
        Schema::table('webhooks', function (Blueprint $table) {
            $table->string('reference')->nullable()->after('payload');
            $table->decimal('amount', 15, 2)->nullable()->after('reference');
            $table->timestamp('transaction_date')->nullable()->after('amount');
            $table->json('metadata')->nullable()->after('transaction_date');
            $table->text('notes')->nullable()->after('metadata');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('webhooks', function (Blueprint $table) {
            $table->dropColumn(['reference', 'amount', 'transaction_date', 'metadata', 'notes']);
        });
    }
};
