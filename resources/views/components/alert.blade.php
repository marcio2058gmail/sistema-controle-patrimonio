@if(session('sucesso'))
    <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-800 rounded-lg flex items-center gap-2">
        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
        </svg>
        {{ session('sucesso') }}
    </div>
@endif

@if($errors->any())
    <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-800 rounded-lg">
        <ul class="list-disc list-inside space-y-1 text-sm">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
