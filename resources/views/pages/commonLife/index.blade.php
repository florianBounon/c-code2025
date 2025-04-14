<x-app-layout>
    <x-slot name="header">
        <h1 class="text-xl font-semibold text-gray-800">Vie Commune</h1>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto space-y-8">
            <p>Bonjour {{ $user->full_name }} !</p>

            @if ($user->userSchool)
                <p>
                    Votre rôle est : <strong>{{ $user->userSchool->role }}</strong>
                </p>

                @if ($user->userSchool->role === 'admin')
                    <!-- Task addition section (only for admins) -->
                    <button id="openModalBtn" class="bg-blue-600 text-white px-4 py-2 rounded">
                        + Ajouter une tâche
                    </button>
                @endif
            @endif

            {{-- In-progress Tasks Section (common for both admins and students) --}}
            <div class="space-y-6">
                <h2 class="text-xl font-bold mt-6">Tâches en cours</h2>
                @foreach ($tasksInProgress as $task)
                    @php
                        $userPivot = $task->users->firstWhere('id', $user->id)?->pivot;
                        $isCompleted = $userPivot?->is_completed ?? false;
                    @endphp

                    @if (!$isCompleted) {{-- If the task is not completed --}}
                        <div class="p-4 border rounded bg-white shadow-sm">
                            <h3 class="text-lg font-bold text-blue-700">{{ $task->name }}</h3>
                            <p>{{ $task->description }}</p>
                            <p class="text-sm text-gray-500 mt-1">
                                Du {{ \Carbon\Carbon::parse($task->year_start)->format('d/m/Y') }}
                                au {{ \Carbon\Carbon::parse($task->year_end)->format('d/m/Y') }}
                            </p>

                            {{-- Admin buttons --}}
                            @if ($user->userSchool->role === 'admin')
                                <div class="mt-2 flex gap-3">
                                    <a href="{{ route('tasks.edit', $task->id) }}" class="text-blue-500 underline">Modifier</a>
                                    <form action="{{ route('tasks.destroy', $task->id) }}" method="POST" onsubmit="return confirm('Supprimer cette tâche ?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-500 underline">Supprimer</button>
                                    </form>
                                </div>
                            @endif

                            {{-- "Complete" button for students --}}
                            @if ($user->userSchool->role === 'student')
                                <div class="mt-4">
                                    <button
                                        onclick="document.getElementById('complete-form-{{ $task->id }}').classList.toggle('hidden')"
                                        class="bg-green-500 text-white px-4 py-1 rounded"
                                    >
                                        Terminer
                                    </button>

                                    <form
                                        id="complete-form-{{ $task->id }}"
                                        action="{{ route('tasks.complete', $task->id) }}"
                                        method="POST"
                                        class="mt-2 space-y-2 hidden"
                                    >
                                        @csrf
                                        <textarea name="comment" class="w-full border rounded p-2" placeholder="Ajoute ton retour ici..."></textarea>
                                        <button type="submit" class="bg-blue-600 text-white px-4 py-1 rounded">
                                            Marquer comme terminée
                                        </button>
                                    </form>
                                </div>
                            @endif
                        </div>
                    @endif
                @endforeach
            </div>

            {{-- Section for students: Completed tasks by the logged-in student --}}
            @if ($user->userSchool->role === 'student')
                <div class="mt-10">
                    <h2 class="text-xl font-bold">Mes tâches terminées</h2>
                    @foreach ($completedTasksByUser as $task)
                        <div class="mt-4 p-4 border rounded bg-gray-100">
                            <h3 class="text-blue-600 font-semibold">{{ $task->name }}</h3>
                            <p>{{ $task->description }}</p>
                            <p class="text-sm text-gray-500 mt-1">
                                Du {{ \Carbon\Carbon::parse($task->year_start)->format('d/m/Y') }}
                                au {{ \Carbon\Carbon::parse($task->year_end)->format('d/m/Y') }}
                            </p>
                            <p><em>"{{ $task->users->firstWhere('id', $user->id)->pivot->comment ?? 'Aucun commentaire' }}"</em></p>
                        </div>
                    @endforeach
                </div>
            @endif

            {{-- Admin section: Completed tasks by students --}}
            @if ($user->userSchool->role === 'admin')
                <div class="mt-10">
                    <h2 class="text-xl font-bold">Tâches terminées par les élèves</h2>
                    @foreach ($completedTasks as $task)
                        @foreach ($task->users as $student)
                            @if ($student->pivot->is_completed)
                                <div class="mt-4 p-4 border rounded bg-gray-100">
                                    <h3 class="text-blue-600 font-semibold">{{ $task->name }}</h3>
                                    <p>{{ $task->description }}</p>
                                    <p class="text-sm text-gray-500 mt-1">
                                        Du {{ \Carbon\Carbon::parse($task->year_start)->format('d/m/Y') }}
                                        au {{ \Carbon\Carbon::parse($task->year_end)->format('d/m/Y') }}
                                    </p>
                                    <p class="font-medium">{{ $student->full_name }}</p>
                                    <p><em>"{{ $student->pivot->comment ?? 'Aucun commentaire' }}"</em></p>
                                </div>
                            @endif
                        @endforeach
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <!-- Task addition modal -->
    <div id="taskModal" class="fixed inset-0 flex items-center justify-center z-50 hidden">
        <div id="modalBackground" class="absolute inset-0 bg-black opacity-50"></div>

        <div class="bg-white p-6 rounded-lg w-96 shadow-lg z-10">
            <h2 class="text-xl text-center mb-4">Ajouter une tâche</h2>

            <form method="POST" action="{{ route('tasks.store') }}">
                @csrf
                <div class="flex flex-col gap-4">
                    <input type="text" name="name" class="border p-2 rounded" placeholder="Nom" required>
                    <textarea name="description" class="border p-2 rounded" placeholder="Description"></textarea>
                    <input type="date" name="year_start" class="border p-2 rounded" required>
                    <input type="date" name="year_end" class="border p-2 rounded" required>

                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">
                        Valider
                    </button>
                </div>
            </form>

            <button id="closeModalBtn" class="mt-4 bg-red-500 text-white px-4 py-1 rounded w-full">
                Fermer
            </button>
        </div>
    </div>

    <!-- Script to handle modal open/close -->
    <script>
        const openModalBtn = document.getElementById('openModalBtn');
        const closeModalBtn = document.getElementById('closeModalBtn');
        const taskModal = document.getElementById('taskModal');
        const modalBackground = document.getElementById('modalBackground');

        if (openModalBtn) {
            openModalBtn.addEventListener('click', () => taskModal.classList.remove('hidden'));
        }
        closeModalBtn.addEventListener('click', () => taskModal.classList.add('hidden'));
        modalBackground.addEventListener('click', () => taskModal.classList.add('hidden'));
    </script>
</x-app-layout>
