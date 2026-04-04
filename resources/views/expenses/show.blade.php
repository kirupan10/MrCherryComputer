<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Expense Details
            </h2>
            @can('expense-edit')
                <a href="{{ route('expenses.edit', $expense) }}"
                    class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg">
                    Edit Expense
                </a>
            @endcan
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm rounded-lg p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Expense Date</label>
                        <p class="text-base font-semibold text-gray-900">{{ $expense->expense_date->format('d M, Y') }}
                        </p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Category</label>
                        <p class="text-base text-gray-900">{{ $expense->category->name }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Amount</label>
                        <p class="text-base font-bold text-gray-900">LKR {{ number_format($expense->amount, 2) }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Reference Number</label>
                        <p class="text-base text-gray-900">{{ $expense->reference_number ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Status</label>
                        @php
                            $statusClass = [
                                'approved' => 'bg-green-100 text-green-800',
                                'pending' => 'bg-yellow-100 text-yellow-800',
                                'paid' => 'bg-blue-100 text-blue-800',
                            ][$expense->status] ?? 'bg-gray-100 text-gray-800';
                        @endphp
                        <span class="px-3 inline-flex text-sm leading-6 font-semibold rounded-full {{ $statusClass }}">
                            {{ ucfirst($expense->status) }}
                        </span>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Created By</label>
                        <p class="text-base text-gray-900">{{ optional($expense->creator)->name ?? 'N/A' }}</p>
                    </div>
                    @if($expense->approved_by)
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Approved By</label>
                            <p class="text-base text-gray-900">{{ optional($expense->approver)->name ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Approved At</label>
                            <p class="text-base text-gray-900">{{ optional($expense->updated_at)->format('d M, Y h:i A') }}
                            </p>
                        </div>
                    @endif
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-500 mb-1">Description</label>
                        <p class="text-base text-gray-900">{{ $expense->description }}</p>
                    </div>
                    @if($expense->receipt_image)
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-500 mb-2">Receipt/Document</label>
                            @if(Str::endsWith($expense->receipt_image, '.pdf'))
                                <a href="{{ asset('storage/' . $expense->receipt_image) }}" target="_blank"
                                    class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z">
                                        </path>
                                    </svg>
                                    View PDF Receipt
                                </a>
                            @else
                                <img src="{{ asset('storage/' . $expense->receipt_image) }}" alt="Receipt"
                                    class="max-w-md rounded-lg shadow-sm">
                            @endif
                        </div>
                    @endif
                </div>

                @role('admin')
                @if($expense->status === 'pending')
                    <div class="mt-8 pt-6 border-t border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Approval Actions</h3>
                        <div class="flex gap-4">
                            <form action="{{ route('expenses.approve', $expense) }}" method="POST">
                                @csrf
                                <button type="submit"
                                    class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-6 rounded-lg">
                                    Approve Expense
                                </button>
                            </form>
                            <form action="{{ route('expenses.reject', $expense) }}" method="POST"
                                onsubmit="return confirm('Are you sure you want to reject this expense?')">
                                @csrf
                                <button type="submit"
                                    class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-6 rounded-lg">
                                    Reject Expense
                                </button>
                            </form>
                        </div>
                    </div>
                @endif
                @endrole
            </div>
        </div>
    </div>
</x-app-layout>