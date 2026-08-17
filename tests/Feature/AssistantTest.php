<?php

namespace Tests\Feature;

use App\Ai\Agents\FinanceAssistant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AssistantTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_to_the_login_page()
    {
        $response = $this->get(route('assistant.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_users_can_visit_the_assistant_page()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get(route('assistant.index'));
        $response->assertOk();
        $response->assertInertia(fn ($page) => $page->component('assistant/Index'));
    }

    public function test_stream_requires_a_messages_payload()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->post(route('assistant.stream'), []);
        $response->assertStatus(422);
    }

    public function test_stream_rejects_an_invalid_last_message()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->post(route('assistant.stream'), [
            'messages' => [
                ['role' => 'assistant', 'content' => 'مرحباً'],
            ],
        ]);
        $response->assertStatus(422);
    }

    public function test_stream_rejects_invalid_roles()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->post(route('assistant.stream'), [
            'messages' => [
                ['role' => 'system', 'content' => 'أنت مساعد'],
            ],
        ]);
        $response->assertStatus(422);
        $response->assertJson(['message' => 'لا توجد رسائل صالحة.']);
    }

    public function test_stream_returns_an_event_stream_with_text_deltas()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        FinanceAssistant::fake(['مرحباً بك في مدبّر!']);

        $response = $this->post(route('assistant.stream'), [
            'messages' => [
                ['role' => 'user', 'content' => 'مرحباً'],
            ],
        ]);

        $response->assertOk();
        $response->assertHeader('Content-Type', 'text/event-stream; charset=utf-8');
        $content = $response->streamedContent();
        $this->assertStringContainsString('event: text_delta', $content);
        $this->assertStringContainsString('مرحباً بك في مدبّر!', $content);
        $this->assertStringContainsString('event: done', $content);
    }

    public function test_stream_includes_previous_messages_as_conversation_history()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        FinanceAssistant::fake(function (string $prompt, $attachments, $provider, $model) {
            return 'استلمت: '.$prompt;
        });

        $response = $this->post(route('assistant.stream'), [
            'messages' => [
                ['role' => 'user', 'content' => 'كم صرفت؟'],
                ['role' => 'assistant', 'content' => 'صرفت 500 ر.س.'],
                ['role' => 'user', 'content' => 'وكم المتبقي؟'],
            ],
        ]);

        $response->assertOk();
        $this->assertStringContainsString('وكم المتبقي؟', $response->streamedContent());
    }
}
