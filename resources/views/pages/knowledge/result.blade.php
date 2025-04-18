<x-app-layout>
    <x-slot name="header">
        <h1 class="text-lg font-semibold">Résultat du questionnaire</h1>
    </x-slot>

    <div class="max-w-xl mx-auto py-6">
        <div class="bg-white p-6 rounded shadow">
            <h2 class="text-xl font-bold mb-2">Votre score</h2>
            <p class="text-lg">Vous avez obtenu <strong>{{ $score }}</strong> / {{ $total }} bonnes réponses.</p>
        </div>
        <a href="{{ route('bilans.index') }}" class="inline-block mt-4 text-blue-600 hover:underline">
            ← Retour aux bilans
        </a>
    </div>
</x-app-layout>
