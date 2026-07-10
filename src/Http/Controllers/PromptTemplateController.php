<?php

namespace HardikSolanki\OpenAILaravel\Http\Controllers;

use HardikSolanki\OpenAILaravel\Facades\OpenAI;
use HardikSolanki\OpenAILaravel\Http\Requests\StorePromptTemplateRequest;
use HardikSolanki\OpenAILaravel\Http\Requests\UseTemplateRequest;
use HardikSolanki\OpenAILaravel\Models\PromptTemplate;
use HardikSolanki\OpenAILaravel\Services\ConversationService;
use HardikSolanki\OpenAILaravel\Services\PromptTemplateService;
use Illuminate\Http\Request;

class PromptTemplateController extends Controller
{
    public function __construct(
        protected PromptTemplateService $templates,
        protected ConversationService $conversations,
    ) {
    }

    public function index(Request $request)
    {
        return response()->json(
            PromptTemplate::where('team_id', $this->teamId($request))->get()
        );
    }

    public function store(StorePromptTemplateRequest $request)
    {
        $template = $this->templates->create(
            $this->teamId($request),
            $request->user()->id,
            $request->validated()
        );

        return response()->json($template, 201);
    }

    public function show(Request $request, int $id)
    {
        return response()->json(
            PromptTemplate::where('team_id', $this->teamId($request))->findOrFail($id)
        );
    }

    public function use(UseTemplateRequest $request, int $id)
    {
        $template = PromptTemplate::where('team_id', $this->teamId($request))->findOrFail($id);

        $validation = $this->templates->validate($template, $request->validated('variables'));

        if ($validation->fails()) {
            return response()->json(['errors' => $validation->errors()], 422);
        }

        $conversation = $this->conversations->getConversation(
            $request->validated('conversation_id'),
            $this->teamId($request)
        );

        $content = $this->templates->interpolate($template, $request->validated('variables'));

        $response = OpenAI::text()
            ->model($template->model)
            ->prompt($content)
            ->team($this->teamId($request))
            ->user($request->user()->id)
            ->saveToConversation($conversation->id)
            ->generate();

        return response()->json([
            'content' => $response->content(),
            'tokens' => $response->tokensUsed(),
            'cost' => $response->costIncurred(),
        ]);
    }

    public function destroy(Request $request, int $id)
    {
        PromptTemplate::where('team_id', $this->teamId($request))->findOrFail($id)->delete();

        return response()->json(['message' => 'Template deleted.']);
    }
}
