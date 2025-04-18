<?php

use App\Http\Controllers\CohortController;
use App\Http\Controllers\CommonLifeController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RetroController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\KnowledgeController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\TeacherController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\ApiController;

// Redirect the root path to /dashboard
Route::redirect('/', 'dashboard');

Route::middleware('auth')->group(function () {

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::middleware('verified')->group(function () {
        // Dashboard
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // Cohorts
        Route::get('/cohorts', [CohortController::class, 'index'])->name('cohort.index');
        Route::get('/cohort/{cohort}', [CohortController::class, 'show'])->name('cohort.show');

        // Teachers
        Route::get('/teachers', [TeacherController::class, 'index'])->name('teacher.index');

        // Students
        Route::get('students', [StudentController::class, 'index'])->name('student.index');

        // Knowledge
        Route::get('knowledge', [KnowledgeController::class, 'index'])->name('knowledge.index');
        Route::get('/bilans/create', [KnowledgeController::class, 'create'])->name('bilans.create');
        Route::post('/send-prompt', [ApiController::class, 'sendPrompt'])->name('send.prompt');
        Route::get('/bilans/{questionnaire}', [KnowledgeController::class, 'show'])->name('bilans.show');
        Route::get('/bilans/create', [KnowledgeController::class, 'create'])->name('bilans.create');
        Route::post('/bilans', [KnowledgeController::class, 'store'])->name('bilans.store');
        Route::get('/bilans', [KnowledgeController::class, 'index'])->name('bilans.index');
        Route::get('/bilans/create', [ApiController::class, 'create'])->name('bilans.create');
        Route::post('/bilans', [ApiController::class, 'sendPrompt'])->name('send.prompt');
        Route::get('/bilans/{questionnaire}/edit', [ApiController::class, 'edit'])->name('bilans.edit');
        Route::put('/bilans/{questionnaire}', [ApiController::class, 'update'])->name('bilans.update');
        Route::get('/bilans/{questionnaire}/start', [KnowledgeController::class, 'start'])
            ->name('bilans.start')
            ->middleware('auth');
        Route::post('/bilans/{questionnaire}/submit', [KnowledgeController::class, 'submit'])
            ->name('bilans.submit')
            ->middleware('auth');


        // Groups
        Route::get('groups', [GroupController::class, 'index'])->name('group.index');

        // Retro
        route::get('retros', [RetroController::class, 'index'])->name('retro.index');

        // Common life
        Route::get('common-life', [CommonLifeController::class, 'index'])->name('common-life.index');
        Route::post('/tasks/{task}/complete', [TaskController::class, 'complete'])->name('tasks.complete');
        Route::post('/tasks', [TaskController::class, 'store'])->name('tasks.store');
        Route::get('/vie-commune', [CommonLifeController::class, 'index'])->name('commonLife.index');

    });

});


// Tasks (admin only)
Route::middleware(['auth', 'verified'])->group(function () {

    Route::post('/tasks', [TaskController::class, 'store'])->name('tasks.store');

    Route::get('/tasks/{task}/edit', [TaskController::class, 'edit'])->name('tasks.edit');

    Route::put('/tasks/{task}', [TaskController::class, 'update'])->name('tasks.update');

    Route::delete('/tasks/{task}', [TaskController::class, 'destroy'])->name('tasks.destroy');
});

require __DIR__ . '/auth.php';

