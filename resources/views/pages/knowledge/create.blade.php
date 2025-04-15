<x-app-layout>
    <x-slot name="header">
        <h1 class="text-lg font-semibold text-gray-800">Créer un Bilan</h1>
    </x-slot>

    <div class="py-6 max-w-2xl mx-auto">
        <p>Bienvenue sur la page de création du bilan ✍️</p>

        <form action="{{ route('send.prompt') }}" method="POST" class="mt-4">
            @csrf

            <!-- Language Selection -->
            <div class="mb-4">
                <label for="language" class="block text-sm font-medium text-gray-700">Langage de programmation</label>
                <select id="language" name="language" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" required>
                    <option value="Python">Python</option>
                    <option value="JavaScript">JavaScript</option>
                    <option value="Java">Java</option>
                    <option value="html">html</option>
                    <option value="css">css</option>
                    <option value="php">php</option>
                    <option value="sql">sql</option>
                    <option value="laravel">laravel</option>
                    
                </select>
            </div>

            <!-- Difficulty Level -->
            <div class="mb-4">
                <label for="difficulty" class="block text-sm font-medium text-gray-700">Niveau de difficulté</label>
                <select id="difficulty" name="difficulty" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" required>
                    <option value="facile">Facile</option>
                    <option value="intermédiaire">Intermédiaire</option>
                    <option value="difficile">Difficile</option>
                </select>
            </div>

            <!-- Number of Questions -->
            <div class="mb-4">
                <label for="questionsCount" class="block text-sm font-medium text-gray-700">Nombre de questions</label>
                <input type="number" id="questionsCount" name="questionsCount" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" value="5" min="1" required />
            </div>

            <!-- Number of Responses per Question -->
            <div class="mb-4">
                <label for="reponsesCount" class="block text-sm font-medium text-gray-700">Nombre de réponses par question</label>
                <input type="number" id="reponsesCount" name="reponsesCount" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" value="4" min="2" required />
            </div>

            <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 active:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                Envoyer
            </button>
        </form>
    </div>
</x-app-layout>
