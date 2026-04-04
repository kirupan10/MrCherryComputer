<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Edit Unit: {{ $unit->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm rounded-lg p-6">
                <form action="{{ route('units.update', $unit) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="space-y-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Unit Name *</label>
                            <input type="text" name="name" value="{{ old('name', $unit->name) }}" required
                                class="w-full border-gray-300 rounded-lg">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Abbreviation *</label>
                            <input type="text" name="short_name" value="{{ old('short_name', $unit->short_name) }}"
                                required class="w-full border-gray-300 rounded-lg">
                        </div>
                    </div>

                    <div class="mt-6 flex gap-4">
                        <button type="submit"
                            class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-lg">
                            Update Unit
                        </button>
                        <a href="{{ route('units.index') }}"
                            class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium py-2 px-6 rounded-lg">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>