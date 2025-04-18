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

            <div class="flex flex-wrap gap-4">
                @forelse ($questionnaires as $questionnaire)
                    <div class="w-64 p-4 bg-white shadow rounded">
                        <h3 class="font-bold">{{ $questionnaire->language }} ({{ $questionnaire->difficulty }})</h3>
                        <p>{{ $questionnaire->questions_count }} questions, {{ $questionnaire->answers_count }} réponses</p>

                        @if ($user->userSchool && $user->userSchool->role === 'admin')
                            <a href="{{ route('bilans.show', $questionnaire->id) }}"
                                class="mt-2 inline-block bg-gray-700 text-white text-sm px-3 py-1 rounded">
                                Informations
                            </a>
                        @endif

                        @if ($user->userSchool && $user->userSchool->role === 'student')
                            @if (!in_array($questionnaire->id, $results))
                                <a href="{{ route('bilans.start', $questionnaire->id) }}"
                                    class="mt-2 inline-block bg-green-600 text-white text-sm px-3 py-1 rounded">
                                    Commencer
                                </a>
                            @else
                                <span class="mt-2 inline-block text-sm text-gray-500">
                                    Déjà complété ✅
                                </span>
                            @endif
                        @endif
                    </div>
                @empty
                    <p>Aucun questionnaire disponible.</p>
                @endforelse
            </div>




        </div>
    </div>
</x-app-layout>