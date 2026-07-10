<?php

namespace HardikSolanki\OpenAILaravel\Services;

use HardikSolanki\OpenAILaravel\Events\ConversationCreated;
use HardikSolanki\OpenAILaravel\Events\MessageAdded;
use HardikSolanki\OpenAILaravel\Models\Conversation;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ConversationService
{
    public function create(
        int $teamId,
        int $userId,
        string $title,
        ?string $systemPrompt = null,
        ?int $apiKeyId = null,
        string $model = 'gpt-4',
    ): Conversation {
        $conversation = Conversation::create([
            'team_id' => $teamId,
            'user_id' => $userId,
            'api_key_id' => $apiKeyId,
            'title' => $title,
            'system_prompt' => $systemPrompt,
            'model' => $model,
            'messages' => [],
        ]);

        event(new ConversationCreated($conversation));

        return $conversation;
    }

    public function addMessage(int $conversationId, string $role, string $content, ?array $metadata = null): void
    {
        $conversation = Conversation::findOrFail($conversationId);
        $conversation->addMessage($role, $content, $metadata);

        if (($metadata['tokens'] ?? null) !== null) {
            $conversation->incrementCost((float) ($metadata['cost'] ?? 0), (int) $metadata['tokens']);
        }

        event(new MessageAdded($conversation, $role, $content));
    }

    public function getConversation(int $convId, int $teamId): Conversation
    {
        return Conversation::where('team_id', $teamId)->findOrFail($convId);
    }

    public function getTeamConversations(int $teamId, int $page = 1, int $perPage = 15): LengthAwarePaginator
    {
        return Conversation::where('team_id', $teamId)
            ->orderByDesc('created_at')
            ->paginate($perPage, ['*'], 'page', $page);
    }

    public function archive(int $convId): void
    {
        Conversation::findOrFail($convId)->update(['is_archived' => true]);
    }

    public function delete(int $convId): void
    {
        Conversation::findOrFail($convId)->delete();
    }
}
