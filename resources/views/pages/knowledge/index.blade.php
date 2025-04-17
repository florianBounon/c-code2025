<x-app-layout>
    <x-slot name="header">
        <h1 class="flex items-center gap-1 text-sm font-normal">
            <span class="text-gray-700">
                {{ __('Bilans de connaissances') }}
            </span>
        </h1>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto space-y-6">

            <p>Bonjour {{ $user->full_name }} !</p>

            @if ($user->userSchool && $user->userSchool->role === 'admin')
                <a href="{{ route('bilans.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded">
                    + Ajouter un bilan
                </a>
            @endif

            <div class="space-y-4">
                <h2 class="text-lg font-semibold">Liste des questionnaires</h2>
                @forelse ($questionnaires as $questionnaire)
                    <div class="p-4 bg-white shadow rounded">
                        <h3 class="font-bold">{{ $questionnaire->language }} ({{ $questionnaire->difficulty }})</h3>
                        <p>{{ $questionnaire->questions_count }} questions, {{ $questionnaire->answers_count }} réponses</p>
                        
                    </div>
                @empty
                    <p>Aucun questionnaire disponible.</p>
                @endforelse
            </div>

        </div>
    </div>
</x-app-layout>