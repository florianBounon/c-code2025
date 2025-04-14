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
            {{-- Message de bienvenue --}}
            <p>Bonjour {{ $user->full_name }} !</p>

            {{-- Bouton réservé aux admins --}}
            @if ($user->userSchool && $user->userSchool->role === 'admin')
                <a href="{{ route('bilans.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded">
                    + Ajouter un bilan
                </a>
            @endif



            {{-- Ici tu peux continuer avec la liste des bilans ou tout autre contenu --}}
        </div>
    </div>
</x-app-layout>