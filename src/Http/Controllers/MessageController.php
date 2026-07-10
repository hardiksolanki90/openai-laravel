<?php

namespace HardikSolanki\OpenAILaravel\Http\Controllers;

use HardikSolanki\OpenAILaravel\Facades\OpenAI;
use HardikSolanki\OpenAILaravel\Http\Requests\StoreMessageRequest;
use HardikSolanki\OpenAILaravel\Services\ConversationService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class MessageController extends Controller
{
    public function __construct(protected ConversationService $conversations)
    {
    }

    public function store(StoreMessageRequest $request, int $id)
    {
        $conversation = $this->conversations->getConversation($id, $this->teamId($request));

        $builder = OpenAI::text()
            ->model($conversation->model)
            ->systemPrompt($conversation->system_prompt ?? '')
            ->history($this->historyFrom($conversation))
            ->prompt($request->validated('content'))
            ->team($conversation->team_id)
            ->user($request->user()->id)
            ->saveToConversation($conversation->id);

        if ($conversation->api_key_id) {
            $builder->apiKey($conversation->api_key_id);
        }

        $response = $builder->generate();

        return response()->json([
            'id' => $conversation->id,
            'role' => 'assistant',
            'content' => $response->content(),
            'tokens' => $response->tokensUsed(),
            'cost' => $response->costIncurred(),
        ]);
    }

    public function stream(Request $request, int $id)
    {
        $conversation = $this->conversations->getConversation($id, $this->teamId($request));

        $builder = OpenAI::text()
            ->model($conversation->model)
            ->systemPrompt($conversation->system_prompt ?? '')
            ->history($this->historyFrom($conversation))
            ->prompt((string) $request->input('content'))
            ->team($conversation->team_id)
            ->user($request->user()->id)
            ->stream()
            ->saveToConversation($conversation->id);

        if ($conversation->api_key_id) {
            $builder->apiKey($conversation->api_key_id);
        }

        return new StreamedResponse(function () use ($builder) {
            foreach ($builder->streamGenerate() as $chunk) {
                echo 'data: '.json_encode(['content' => $chunk->content()])."\n\n";
                ob_flush();
                flush();
            }
        }, 200, [
            'Content-Type' => 'text/event-stream',
            'Cache-Control' => 'no-cache',
            'X-Accel-Buffering' => 'no',
        ]);
    }

    protected function historyFrom($conversation): array
    {
        return collect($conversation->messages)
            ->map(fn ($m) => ['role' => $m['role'], 'content' => $m['content']])
            ->all();
    }
}
