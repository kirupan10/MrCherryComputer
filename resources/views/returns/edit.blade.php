<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Edit Return - {{ $return->return_number }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm rounded-lg p-6">
                <form method="POST" action="{{ route('returns.update', $return) }}" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Return Date</label>
                        <input type="date" name="return_date" value="{{ old('return_date', optional($return->return_date)->format('Y-m-d')) }}" class="w-full border-gray-300 rounded-lg" required>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Refund Method</label>
                        <select name="refund_method" class="w-full border-gray-300 rounded-lg" required>
                            @foreach(['cash','card','store_credit'] as $method)
                            <option value="{{ $method }}" {{ old('refund_method', $return->refund_method) === $method ? 'selected' : '' }}>
                                {{ ucfirst(str_replace('_', ' ', $method)) }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Reason</label>
                        <textarea name="reason" rows="4" class="w-full border-gray-300 rounded-lg" required>{{ old('reason', $return->reason) }}</textarea>
                    </div>

                    <div class="flex gap-2">
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg">Update Return</button>
                        <a href="{{ route('returns.show', $return) }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 py-2 px-4 rounded-lg">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
