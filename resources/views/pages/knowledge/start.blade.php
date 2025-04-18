<x-app-layout>
    <x-slot name="header">
        <h1 class="text-lg font-semibold">Répondre au questionnaire</h1>
    </x-slot>

    <div class="max-w-4xl mx-auto py-6 space-y-6">
        <form action="{{ route('bilans.submit', $questionnaire->id) }}" method="POST">
            @csrf

            <h2 class="font-bold text-xl mb-4">{{ $questionnaire->language }} ({{ $questionnaire->difficulty }})</h2>

            @foreach($questions as $index => $question)
                <div class="mb-6">
                    <p class="font-semibold">{{ $index + 1 }}. {{ $question->question }}</p>
                    @foreach($question->answers as $answer)
                        <label class="block">
                            <input type="radio" name="answers[{{ $question->id }}]" value="{{ $answer->id }}" required>
                            {{ $answer->answer }}
                        </label>
                    @endforeach
                </div>
            @endforeach

            <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded">
                Soumettre mes réponses
            </button>
        </form>
    </div>
</x-app-layout>
