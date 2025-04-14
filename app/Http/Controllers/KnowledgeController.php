<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        return view('pages.knowledge.index', compact('user'));
    }

    public function create()
    {
        return view('pages.knowledge.create');
    }


}
