<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Questionnaire;

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
        $questionnaires = Questionnaire::all(); 
        return view('pages.knowledge.index', compact('user', 'questionnaires'));
    }

    public function create()
    {
        return view('pages.knowledge.create');
    }


}
