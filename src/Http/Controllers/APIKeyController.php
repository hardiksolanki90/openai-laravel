<?php

namespace HardikSolanki\OpenAILaravel\Http\Controllers;

use HardikSolanki\OpenAILaravel\Http\Requests\StoreAPIKeyRequest;
use HardikSolanki\OpenAILaravel\Models\APIKey;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class APIKeyController extends Controller
{
    public function index(Request $request)
    {
        return response()->json(
            APIKey::where('team_id', $this->teamId($request))->get()
        );
    }

    public function store(StoreAPIKeyRequest $request)
    {
        $openaiKey = $request->validated('openai_key');

        $apiKey = APIKey::create([
            'team_id' => $this->teamId($request),
            'name' => $request->validated('name'),
            'key_encrypted' => Crypt::encryptString($openaiKey),
            'key_hash' => hash('sha256', $openaiKey),
            'is_active' => true,
            'created_by' => $request->user()->id,
        ]);

        return response()->json($apiKey, 201);
    }

    public function destroy(Request $request, int $id)
    {
        APIKey::where('team_id', $this->teamId($request))->findOrFail($id)->update(['is_active' => false]);

        return response()->json(['message' => 'API key revoked.']);
    }
}
