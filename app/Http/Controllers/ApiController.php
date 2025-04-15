<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ApiController extends Controller
{
    public function sendPrompt(Request $request)
    {
        $language = $request->input('language');
        $difficulty = $request->input('difficulty');
        $questionsCount = $request->input('questionsCount');
        $reponsesCount = $request->input('reponsesCount');

        $prompt = "Créer un questionnaire sur le langage de programmation $language avec un niveau de difficulté $difficulty. Le questionnaire doit contenir $questionsCount questions, et chaque question doit avoir $reponsesCount réponses possibles. Chaque question doit être suivie des réponses proposées.";

        $apiKey = 'oJFD7jduqNh2XyL2HLC1sfyejgn2DYoa';

        // Send the prompt to the Mistral API
        $response = Http::withOptions([
            'verify' => false,
        ])->withHeaders([
            'Authorization' => 'Bearer ' . $apiKey,
            'Content-Type' => 'application/json',
        ])->post('https://api.mistral.ai/v1/chat/completions', [
            'model' => 'mistral-small',
            'messages' => [
                ['role' => 'user', 'content' => $prompt]
            ]
        ]);

        
        
        if ($response->successful()) {
            $data = $response->json();


            // Process the data received from the API
            dd($data);

            return redirect()->back()->with('success', 'Prompt sent successfully!');
        } else {
            return redirect()->back()->with('error', 'Error while sending the prompt.');
        }
        
    
    }
}
