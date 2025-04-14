<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ApiController extends Controller
{
    public function sendPrompt(Request $request)
    {
        $prompt = $request->input('prompt');
        $apiKey = 'oJFD7jduqNh2XyL2HLC1sfyejgn2DYoa';

        // Send the prompt to the Mistral API
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $apiKey,
            'Content-Type' => 'application/json',
        ])->post('https://api.mistral.ai/v1/prompts', [
                    'prompt' => $prompt,
                ]);


        // Handle the API response
        if ($response->successful()) {
            $data = $response->json();


            // Process the data received from the API
            return redirect()->back()->with('success', 'Prompt sent successfully!');
        } else {
            return redirect()->back()->with('error', 'Error while sending the prompt.');
        }

    }
}
