<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->foreignId('family_id')->nullable()->after('user_id')->constrained()->nullOnDelete();
            $table->index(['family_id', 'transaction_date']);
        });

        Schema::table('budgets', function (Blueprint $table) {
            $table->foreignId('family_id')->nullable()->after('user_id')->constrained()->nullOnDelete();
            $table->index(['family_id', 'month']);
        });
    }

    public function down(): void
    {
        Schema::table('budgets', function (Blueprint $table) {
            $table->dropIndex(['family_id', 'month']);
            $table->dropConstrainedForeignId('family_id');
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->dropIndex(['family_id', 'transaction_date']);
            $table->dropConstrainedForeignId('family_id');
        });
    }
};
