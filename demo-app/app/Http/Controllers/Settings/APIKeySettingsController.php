<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use HardikSolanki\OpenAILaravel\Models\APIKey;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class APIKeySettingsController extends Controller
{
    public function index(Request $request)
    {
        $keys = APIKey::where('team_id', $request->user()->current_team_id)->get();

        return view('settings.api-keys', compact('keys'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'openai_key' => 'required|string|starts_with:sk-',
        ]);

        APIKey::create([
            'team_id' => $request->user()->current_team_id,
            'name' => $data['name'],
            'key_encrypted' => Crypt::encryptString($data['openai_key']),
            'key_hash' => hash('sha256', $data['openai_key']),
            'is_active' => true,
            'created_by' => $request->user()->id,
        ]);

        return redirect()->route('settings.api-keys');
    }

    public function destroy(Request $request, int $id)
    {
        APIKey::where('team_id', $request->user()->current_team_id)->findOrFail($id)->update(['is_active' => false]);

        return redirect()->route('settings.api-keys');
    }
}
