<?php

namespace HardikSolanki\OpenAILaravel\Tests\Feature;

use HardikSolanki\OpenAILaravel\Models\Conversation;
use HardikSolanki\OpenAILaravel\Models\Team;
use HardikSolanki\OpenAILaravel\Services\ConversationService;
use HardikSolanki\OpenAILaravel\Tests\TestCase;

class ConversationTest extends TestCase
{
    public function test_creates_a_conversation(): void
    {
        [$team, $userId] = $this->makeTeam();

        $conversation = app(ConversationService::class)->create(
            $team->id,
            $userId,
            'Meeting Summary',
            'You are a helpful assistant',
        );

        $this->assertDatabaseHas('conversations', [
            'id' => $conversation->id,
            'team_id' => $team->id,
            'title' => 'Meeting Summary',
        ]);
        $this->assertSame([], $conversation->messages);
    }

    public function test_adds_messages_to_a_conversation(): void
    {
        [$team, $userId] = $this->makeTeam();

        $conversation = app(ConversationService::class)->create($team->id, $userId, 'Chat');

        app(ConversationService::class)->addMessage($conversation->id, 'user', 'Hello');
        app(ConversationService::class)->addMessage($conversation->id, 'assistant', 'Hi there', ['tokens' => 10, 'cost' => 0.001]);

        $conversation->refresh();

        $this->assertCount(2, $conversation->messages);
        $this->assertSame('Hello', $conversation->messages[0]['content']);
        $this->assertSame('Hi there', $conversation->messages[1]['content']);
    }

    public function test_archives_a_conversation(): void
    {
        [$team, $userId] = $this->makeTeam();

        $conversation = app(ConversationService::class)->create($team->id, $userId, 'Chat');

        app(ConversationService::class)->archive($conversation->id);

        $this->assertTrue($conversation->fresh()->is_archived);
    }

    /**
     * @return array{0: Team, 1: int}
     */
    protected function makeTeam(): array
    {
        $userId = \DB::table('users')->insertGetId([
            'name' => 'Owner', 'email' => 'owner@example.com', 'password' => 'secret', 'created_at' => now(), 'updated_at' => now(),
        ]);

        $team = Team::create(['name' => 'Acme', 'slug' => 'acme', 'owner_id' => $userId]);

        return [$team, $userId];
    }
}
