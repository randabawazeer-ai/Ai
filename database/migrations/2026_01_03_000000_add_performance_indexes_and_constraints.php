<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->index(['user_id', 'transaction_date'], 'transactions_user_date_idx');
            $table->index(['user_id', 'type'], 'transactions_user_type_idx');
            $table->index(['user_id', 'category_id'], 'transactions_user_category_idx');
            $table->index(['user_id', 'payment_method'], 'transactions_user_payment_idx');
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->index('is_default', 'categories_is_default_idx');
            $table->index('user_id', 'categories_user_id_idx');
            $table->unique(['user_id', 'name'], 'categories_user_name_unique');
        });

        Schema::table('budgets', function (Blueprint $table) {
            $table->index(['user_id', 'month'], 'budgets_user_month_idx');
        });
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropIndex('transactions_user_date_idx');
            $table->dropIndex('transactions_user_type_idx');
            $table->dropIndex('transactions_user_category_idx');
            $table->dropIndex('transactions_user_payment_idx');
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->dropIndex('categories_is_default_idx');
            $table->dropIndex('categories_user_id_idx');
            $table->dropUnique('categories_user_name_unique');
        });

        Schema::table('budgets', function (Blueprint $table) {
            $table->dropIndex('budgets_user_month_idx');
        });
    }
};
