<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Modifier la tâche</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-xl mx-auto bg-white p-6 rounded shadow">
            <form method="POST" action="{{ route('tasks.update', $task->id) }}">
                @csrf
                @method('PUT')

                <div class="mb-4">
                    <label for="name" class="block font-bold">Nom</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $task->name) }}" required
                        class="w-full border p-2 rounded">
                </div>

                <div class="mb-4">
                    <label for="description" class="block font-bold">Description</label>
                    <textarea name="description" id="description"
                        class="w-full border p-2 rounded">{{ old('description', $task->description) }}</textarea>
                </div>

                <div class="mb-4">
                    <label for="year_start" class="block font-bold">Début</label>
                    <input type="date" name="year_start" id="year_start"
                        value="{{ old('year_start', $task->year_start->format('Y-m-d')) }}"
                        class="w-full border p-2 rounded">
                </div>

                <div class="mb-4">
                    <label for="year_end" class="block font-bold">Fin</label>
                    <input type="date" name="year_end" id="year_end"
                        value="{{ old('year_end', $task->year_end->format('Y-m-d')) }}"
                        class="w-full border p-2 rounded">
                </div>

                <div>
                    <label for="promotions" class="font-semibold block mb-1">Promotions concernées</label>
                    <select name="promotions[]" multiple required class="border p-2 rounded w-full">
                        @foreach(\App\Models\Promotion::all() as $promotion)
                            <option value="{{ $promotion->id }}">{{ $promotion->name }} - {{ $promotion->year }}</option>
                        @endforeach
                    </select>
                    <small class="text-gray-500">Utilisez Ctrl ou Cmd pour sélectionner plusieurs promotions</small>
                </div>

                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Mettre à jour</button>
            </form>
        </div>
    </div>
</x-app-layout>