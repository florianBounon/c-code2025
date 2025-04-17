<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use PhpParser\Lexer\TokenEmulator\EnumTokenEmulator;
use Psy\Command\EditCommand;

use App\Models\Questionnaire;
use App\Models\Question;
use App\Models\Answer;

class ApiController extends Controller
{
    public function sendPrompt(Request $request)
    {
        $request->validate([
            'language' => 'required|string',
            'difficulty' => 'required|string|in:Facile,Intermédiaire,Difficile',
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
        if ($difficulty == "Intermédiaire") {
            $complexite *= 1.20;
        } elseif ($difficulty == "Difficile") {
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

        Affiche les questions sous la forme suivante :
        question "numéro de la question" : "coeficien de dificulter (de 0 a 1)" : "intitulé de la question"  
        réponse "numéro de la réponse" : "contenu de la réponse"  
        réponse "numéro de la réponse" : "contenu de la réponse" 
        ...
        
        Une fois toutes les questions affichées, affiche les bonnes réponses à la fin, sous la forme suivante :
        réponse:
        réponse question "numéro de la question" : réponse "numéro de la bonne réponse" : "contenu de la bonne réponse"
        réponse question "numéro de la question" : réponse "numéro de la bonne réponse" : "contenu de la bonne réponse"
        ...
        
        Enfin, ajoute un lien vers une image représentant le langage utilisé dans le questionnaire, sous la forme :
        image : <"lien de l'image">

        Important :
        - Ne mets pas d'espaces spéciaux ou insécables (pas de caractères Unicode comme \\u00A0)
        - Utilise uniquement des espaces classiques (code ASCII 32)
        - Garde exactement le format précisé (pas de variation de ponctuation ou d'espacement)
        
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
            $content = $data['choices'][0]['message']['content'];



            // Extracting raw content
            preg_match_all('/Question (\d+) : ([0-9.]+) : (.*?)\n(.*?)\n(?=(Question|\nRéponses|Image|---))/s', $content, $matches, PREG_SET_ORDER);
            preg_match_all('/réponse question (\d+) : réponse (\d+)/', $content, $correctAnswersMatches);

            $correctAnswers = [];
            foreach ($correctAnswersMatches[1] as $i => $qIndex) {
                $correctAnswers[$qIndex] = $correctAnswersMatches[2][$i];
            }

            // Creating the questionnaire
            $questionnaire = Questionnaire::create([
                'language' => $language,
                'difficulty' => $difficulty,
                'questions_count' => $questionsCount,
                'answers_count' => $reponsesCount,
            ]);

            // Processing the questions
            foreach ($matches as $match) {
                $questionNumber = $match[1];
                $difficultyScore = $match[2];
                $questionText = trim($match[3]);
                $answersBlock = trim($match[4]);

                // Creating the question
                $questionRecord = Question::create([
                    'questionnaire_id' => $questionnaire->id,
                    'question' => $questionText,
                    'difficulty' => $difficultyScore,
                ]);

                // Retrieving the answers
                preg_match_all('/réponse (\d+) : (.*)/', $answersBlock, $answerMatches, PREG_SET_ORDER);
                foreach ($answerMatches as $index => $answerMatch) {
                    $answerText = trim($answerMatch[2]);
                    $isCorrect = ($index + 1) == $correctAnswers[$questionNumber];

                    Answer::create([
                        'question_id' => $questionRecord->id,
                        'answer' => $answerText,
                        'is_correct' => $isCorrect,
                    ]);
                }
            }


            // Process the data received from the API debug
            //dd($content);

            return redirect()->back()->with('success', 'Prompt sent successfully!');
        } else {
            return redirect()->back()->with('error', 'Error while sending the prompt.');
        }


    }
}
