<x-app-layout>
    <x-slot name="header">
        <h1 class="text-lg font-semibold text-gray-800">Créer un Bilan</h1>
    </x-slot>

    <div class="py-6 max-w-2xl mx-auto">
        <p>Bienvenue sur la page de création du bilan ✍️</p>

        <form action="{{ route('send.prompt') }}" method="POST" class="mt-4">
            @csrf
            <div class="mb-4">
                <label for="prompt" class="block text-sm font-medium text-gray-700">Prompt</label>
                <textarea id="prompt" name="prompt" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" rows="4" required></textarea>
            </div>
            <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 active:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                Envoyer
            </button>
        </form>
    </div>
</x-app-layout>
