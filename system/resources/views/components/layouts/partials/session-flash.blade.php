@if (session('success'))
    <div class="mb-4 rounded-xl border border-success/30 bg-success/10 p-3 text-sm text-success">
        {{ session('success') }}
    </div>
@endif

@if (session('error'))
    <div class="mb-4 rounded-xl border border-error/30 bg-error/10 p-3 text-sm text-error">
        {{ session('error') }}
    </div>
@endif

@if (session('status'))
    <div class="mb-4 rounded-xl border border-primary/30 bg-primary/10 p-3 text-sm text-primary">
        {{ session('status') }}
    </div>
@endif
