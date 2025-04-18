<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Questionnaire;
use App\Models\Promotion;
use App\Models\Answer;
use App\Models\Result;


class KnowledgeController extends Controller
{
    /**
     * Display the page
     *
     * @return Factory|View|Application|object
     */


    public function index()
    {
        $user = Auth::user();
        $questionnaires = Questionnaire::with('promotions')->get();

        $results = $user->results()->pluck('questionnaire_id')->toArray();

        if ($user->userSchool && $user->userSchool->role === 'admin') {
            $questionnaires = Questionnaire::all();
        } else {
            $promotions = $user->promotions;

            if ($promotions->isNotEmpty()) {
                $questionnaires = $promotions->flatMap(function ($promotion) {
                    return $promotion->questionnaires;
                })->unique('id');
            } else {
                $questionnaires = collect();
            }
        }

        return view('pages.knowledge.index', compact('user', 'questionnaires', 'results'));
    }


    public function create()
    {
        $promotions = Promotion::all();
        return view('pages.knowledge.create', compact('promotions'));
    }

    public function show($id)
    {
        $questionnaire = Questionnaire::with(['promotions', 'results.user.promotion'])->findOrFail($id);
        $user = Auth::user();

        $promotions = Promotion::all();

        return view('pages.knowledge.show', compact('questionnaire', 'promotions', 'user'));
    }


    public function store(Request $request)
    {
        $request->validate([
            'language' => 'required|string',
            'difficulty' => 'required|string',
            'questions_count' => 'required|integer',
            'answers_count' => 'required|integer',
            'promotions' => 'required|array',
            'promotions.*' => 'exists:promotions,id',
        ]);

        $questionnaire = Questionnaire::create($request->only([
            'language',
            'difficulty',
            'questions_count',
            'answers_count'
        ]));

        $questionnaire->promotions()->attach($request->input('promotions'));

        return redirect()->route('bilans.index')->with('success', 'Questionnaire créé avec succès.');
    }

    public function start(Questionnaire $questionnaire)
    {
        $user = Auth::user();

        $promotions = $user->promotions;

        if ($promotions->isEmpty() || !$questionnaire->promotions->intersect($promotions)->isNotEmpty()) {
            abort(403, 'Ce questionnaire ne vous est pas destiné.');
        }

        $questions = $questionnaire->questions()->with('answers')->get();

        return view('pages.knowledge.start', compact('questionnaire', 'questions'));
    }




    public function submit(Request $request, Questionnaire $questionnaire)
    {
        $user = Auth::user();
        $answers = $request->input('answers', []);

        $score = 0;
        $total = count($answers);

        foreach ($answers as $questionId => $answerId) {
            $correct = Answer::where('question_id', $questionId)
                ->where('id', $answerId)
                ->where('is_correct', true)
                ->exists();

            if ($correct) {
                $score++;
            }
        }

        // save 
        Result::create([
            'user_id' => $user->id,
            'questionnaire_id' => $questionnaire->id,
            'score' => $score,
            'total' => $total,
        ]);

        return view('pages.knowledge.result', [
            'score' => $score,
            'total' => $total,
        ]);
    }

    public function results()
    {
        return $this->hasMany(\App\Models\Result::class);
    }

}
