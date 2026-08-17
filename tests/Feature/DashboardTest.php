<?php

namespace Tests\Feature;

use App\Models\Budget;
use App\Models\Category;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_to_the_login_page()
    {
        $response = $this->get(route('dashboard'));
        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_users_can_visit_the_dashboard()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get(route('dashboard'));
        $response->assertOk();
    }

    public function test_dashboard_includes_monthly_expenses_for_last_six_months()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        Transaction::create([
            'user_id' => $user->id,
            'type' => 'expense',
            'amount' => 500,
            'transaction_date' => now(),
        ]);

        $response = $this->get(route('dashboard'));
        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->component('Dashboard')
            ->has('monthlyExpenses', 6)
            ->where('monthlyExpenses.5', 500)
        );
    }

    public function test_dashboard_remaining_budget_sums_all_budgets()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $category = Category::create([
            'name' => 'طعام',
            'icon' => 'utensils',
            'type' => 'expense',
        ]);

        Budget::create([
            'user_id' => $user->id,
            'category_id' => null,
            'amount' => 1000,
            'month' => now()->format('Y-m'),
        ]);

        Budget::create([
            'user_id' => $user->id,
            'category_id' => $category->id,
            'amount' => 500,
            'month' => now()->format('Y-m'),
        ]);

        Transaction::create([
            'user_id' => $user->id,
            'type' => 'expense',
            'amount' => 300,
            'transaction_date' => now(),
        ]);

        $response = $this->get(route('dashboard'));
        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->component('Dashboard')
            ->where('stats.total_budget', 1500)
            ->where('stats.balance', 1200)
        );
    }
}
