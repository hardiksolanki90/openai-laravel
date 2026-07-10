<?php

namespace App\Http\Controllers;

use HardikSolanki\OpenAILaravel\Models\PromptTemplate;
use HardikSolanki\OpenAILaravel\Services\PromptTemplateService;
use Illuminate\Http\Request;

class TemplateController extends Controller
{
    public function __construct(protected PromptTemplateService $templates)
    {
    }

    public function index(Request $request)
    {
        $templates = PromptTemplate::where('team_id', $request->user()->current_team_id)->get();

        return view('templates.index', compact('templates'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $data['variables'] = [];

        $this->templates->create($request->user()->current_team_id, $request->user()->id, $data);

        return redirect()->route('templates.index');
    }
}
