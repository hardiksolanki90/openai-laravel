<?php

namespace App\Http\Controllers;

use HardikSolanki\OpenAILaravel\Facades\OpenAI;
use HardikSolanki\OpenAILaravel\Services\ConversationService;
use Illuminate\Http\Request;

class ConversationController extends Controller
{
    public function __construct(protected ConversationService $conversations)
    {
    }

    public function index(Request $request)
    {
        $conversations = $this->conversations->getTeamConversations($request->user()->current_team_id);

        return view('conversations.index', compact('conversations'));
    }

    public function store(Request $request)
    {
        $request->validate(['title' => 'required|string|max:255']);

        $conversation = $this->conversations->create(
            $request->user()->current_team_id,
            $request->user()->id,
            $request->input('title'),
        );

        return redirect()->route('conversations.show', $conversation);
    }

    public function show(Request $request, int $conversation)
    {
        $conversation = $this->conversations->getConversation($conversation, $request->user()->current_team_id);

        return view('conversations.show', compact('conversation'));
    }

    public function sendMessage(Request $request, int $conversation)
    {
        $request->validate(['content' => 'required|string']);

        $conv = $this->conversations->getConversation($conversation, $request->user()->current_team_id);

        $history = collect($conv->messages)->map(fn ($m) => ['role' => $m['role'], 'content' => $m['content']])->all();

        OpenAI::text()
            ->model($conv->model)
            ->systemPrompt($conv->system_prompt ?? '')
            ->history($history)
            ->prompt($request->input('content'))
            ->team($conv->team_id)
            ->user($request->user()->id)
            ->saveToConversation($conv->id)
            ->generate();

        return redirect()->route('conversations.show', $conversation);
    }
}
