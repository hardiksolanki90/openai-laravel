<?php

namespace HardikSolanki\OpenAILaravel\Http\Controllers;

use HardikSolanki\OpenAILaravel\Http\Requests\StoreConversationRequest;
use HardikSolanki\OpenAILaravel\Http\Requests\UpdateConversationRequest;
use HardikSolanki\OpenAILaravel\Services\ConversationService;
use Illuminate\Http\Request;

class ConversationController extends Controller
{
    public function __construct(protected ConversationService $conversations)
    {
    }

    public function index(Request $request)
    {
        $page = (int) $request->input('page', 1);

        return response()->json(
            $this->conversations->getTeamConversations($this->teamId($request), $page)
        );
    }

    public function store(StoreConversationRequest $request)
    {
        $conversation = $this->conversations->create(
            $this->teamId($request),
            $request->user()->id,
            $request->validated('title'),
            $request->validated('system_prompt'),
            $request->validated('api_key_id'),
            $request->validated('model') ?? config('openai.default_model'),
        );

        return response()->json($conversation, 201);
    }

    public function show(Request $request, int $id)
    {
        return response()->json(
            $this->conversations->getConversation($id, $this->teamId($request))
        );
    }

    public function update(UpdateConversationRequest $request, int $id)
    {
        $conversation = $this->conversations->getConversation($id, $this->teamId($request));
        $conversation->update($request->validated());

        return response()->json($conversation);
    }

    public function destroy(Request $request, int $id)
    {
        $this->conversations->getConversation($id, $this->teamId($request));
        $this->conversations->archive($id);

        return response()->json(['message' => 'Conversation archived.']);
    }
}
