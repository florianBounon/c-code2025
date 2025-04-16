<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use PhpParser\Lexer\TokenEmulator\EnumTokenEmulator;
use Psy\Command\EditCommand;

class ApiController extends Controller
{
    public function sendPrompt(Request $request)
    {
        $request->validate([
            'language' => 'required|string',
            'difficulty' => 'required|string|in:Easy,Intermediate,hard',
            'questionsCount' => 'required|integer|min:2',
            'reponsesCount' => 'required|integer|min:3',
        ], [
            'questionsCount.min' => 'Le nombre de questions doit être supérieur à 1.',
            'reponsesCount.min' => 'Le nombre de réponses doit être supérieur à 2.',
        ]);

        $language = $request->input('language');
        $difficulty = $request->input('difficulty');
        $questionsCount = $request->input('questionsCount');
        $reponsesCount = $request->input('reponsesCount');


        // Prevent overloading the questionnaire
        $complexite = ($request->input('questionsCount') * 1.25) * $request->input('reponsesCount');
        $complexiteMax = 125;
        if($difficulty == "Intermediate") {
            $complexite *= 1.20;
        }elseif($difficulty == "hard") {
            $complexite *= 1.40;
        }
        if ($complexite > $complexiteMax) {
            return redirect()->back()
                ->withInput()
                ->withErrors([
                    'questionsCount' => "La complexité de generation du questionnaire est trop élevée.",
                ]);
        }


        
        $prompt = <<<EOD
        Créer un questionnaire sur le langage de programmation $language avec un niveau de difficulté $difficulty.
        Le questionnaire doit contenir $questionsCount questions,
        et chaque question doit avoir $reponsesCount réponses possibles.

        Affiche les questions sous la forme suivante sans afiche les bonnes réponses:  
        question "numéro de la question" : "coeficien de dificulter (de 0 a 1)" : "intitulé de la question"  
        réponse "numéro de la réponse" : "contenu de la réponse"  
        réponse "numéro de la réponse" : "contenu de la réponse" 
        ...
        
        Une fois toutes les questions affichées, affiche les bonnes réponses à la fin, sous la forme suivante :
        réponse question "numéro de la question" : réponse "numéro de la bonne réponse" : "réponse"
        
        Enfin, ajoute un lien vers une image représentant le langage utilisé dans le questionnaire, sous la forme :  
        image : <"lien de l'image">
        
EOD;

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
