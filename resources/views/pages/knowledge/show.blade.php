<x-app-layout>
    <x-slot name="header">
        <h1 class="text-lg font-semibold">Détails du questionnaire</h1>
    </x-slot>

    <div class="max-w-3xl mx-auto py-6 space-y-6">
        <div class="p-6 bg-white rounded shadow">
            <h2 class="text-xl font-bold mb-2">Langue : {{ $questionnaire->language }}</h2>
            <p><strong>Difficulté :</strong> {{ $questionnaire->difficulty }}</p>
            <p><strong>Nombre de questions :</strong> {{ $questionnaire->questions_count }}</p>
            <p><strong>Nombre de réponses :</strong> {{ $questionnaire->answers_count }}</p>

            <div class="mt-4">
                <h3 class="font-semibold mb-1">Promotions concernées :</h3>
                @if($promotions->isEmpty())
                    <p class="text-sm text-gray-500">Aucune promotion associée</p>
                @else
                    <ul class="list-disc list-inside">
                        @foreach ($promotions as $promotion)
                            <li>{{ $promotion->name }} - {{ $promotion->year }}</li>
                        @endforeach
                    </ul>
                @endif
            </div>

            @if ($user->userSchool && $user->userSchool->role === 'admin')
                <div class="mt-6">
                    <h3 class="font-semibold mb-1">Modifier les promotions :</h3>
                    <form action="{{ route('bilans.update', $questionnaire->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <!-- Sélection des promotions -->
                        <div class="mb-4">
                            <label for="promotions" class="block text-sm font-medium text-gray-700">Promotions
                                concernées</label>
                            <select id="promotions" name="promotions[]" multiple
                                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                required>
                                @foreach($promotions as $promotion)
                                    <option value="{{ $promotion->id }}" @if($questionnaire->promotions->contains($promotion))
                                    selected @endif>
                                        {{ $promotion->name }} - {{ $promotion->year }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-gray-500">Utilisez Ctrl ou Cmd pour sélectionner plusieurs promotions</small>
                        </div>

                        <button type="submit"
                            class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 active:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                            Mettre à jour
                        </button>
                    </form>
                </div>
            @endif
        </div>

        @if($questionnaire->results->count() > 0)
                <div class="mt-8">
                    <h3 class="text-lg font-semibold mb-2">Résultats par promotion :</h3>

                    @php
                        $resultsByPromotion = $questionnaire->results->groupBy(function ($result) {
                            return $result->user->promotion->name . ' - ' . $result->user->promotion->year;
                        });
                    @endphp

                    @foreach ($resultsByPromotion as $promotionName => $results)
                        <div class="mb-4">
                            <h4 class="font-semibold text-gray-700">{{ $promotionName }}</h4>
                            <table class="w-full text-sm border border-gray-200 mt-2">
                                <thead class="bg-gray-100">
                                    <tr>
                                        <th class="p-2 text-left">Étudiant</th>
                                        <th class="p-2 text-left">Note</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($results as $result)
                                        <tr class="border-t">
                                            <td class="p-2">{{ $result->user->full_name }}</td>
                                            <td class="p-2">{{ $result->score }}/{{ $result->total }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endforeach
                </div>
        @endif


        <a href="{{ route('bilans.index') }}" class="inline-block text-blue-600 hover:underline">
            ← Retour à la liste
        </a>
    </div>
</x-app-layout>