<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Import Preview') }}</h2>
            <p class="text-sm text-gray-500 mt-1">{{ $pending['filename'] }} — {{ ucfirst($pending['type']) }}</p>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if(session('error'))
                <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">{{ session('error') }}</div>
            @endif

            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="bg-white rounded-lg shadow-sm p-5"><div class="text-sm text-gray-500">Rows</div><div class="text-2xl font-bold">{{ $pending['total'] }}</div></div>
                <div class="bg-white rounded-lg shadow-sm p-5"><div class="text-sm text-gray-500">Valid</div><div class="text-2xl font-bold text-green-600">{{ count($pending['valid']) }}</div></div>
                <div class="bg-white rounded-lg shadow-sm p-5"><div class="text-sm text-gray-500">Errors</div><div class="text-2xl font-bold text-red-600">{{ count($pending['errors']) }}</div></div>
                <div class="bg-white rounded-lg shadow-sm p-5"><div class="text-sm text-gray-500">Status</div><div class="text-2xl font-bold">{{ empty($pending['errors']) ? 'Ready' : 'Fix file' }}</div></div>
            </div>

            @if($pending['errors'])
                <div class="bg-white rounded-lg shadow-sm border border-red-200 p-5">
                    <h3 class="font-semibold text-red-800 mb-3">Validation errors</h3>
                    <ul class="list-disc list-inside text-sm text-red-700 space-y-1 max-h-80 overflow-y-auto">
                        @foreach($pending['errors'] as $error)<li>{{ $error }}</li>@endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b font-semibold">Preview of uploaded records</div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                @foreach(array_keys($pending['rows'][0] ?? []) as $heading)
                                    <th class="px-4 py-3 text-left font-medium text-gray-600 whitespace-nowrap">{{ ucwords(str_replace('_', ' ', $heading)) }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach(array_slice($pending['rows'], 0, 25) as $row)
                                <tr>
                                    @foreach(array_keys($pending['rows'][0] ?? []) as $heading)
                                        <td class="px-4 py-3 whitespace-nowrap">{{ $row[$heading] ?? '' }}</td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="flex flex-wrap gap-3">
                @if(empty($pending['errors']) && !empty($pending['valid']))
                    <form action="{{ route('imports.confirm', $token) }}" method="POST">
                        @csrf
                        <button class="px-5 py-2.5 rounded-md bg-green-600 text-white font-semibold hover:bg-green-700">Confirm Import</button>
                    </form>
                @endif
                <form action="{{ route('imports.cancel', $token) }}" method="POST">
                    @csrf
                    <button class="px-5 py-2.5 rounded-md bg-gray-200 text-gray-800 font-semibold hover:bg-gray-300">Cancel</button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
