<h1 class="text-4xl">{{ $name }}</h1>
<div>{{ $authors->pluck('full_name')->join(', ') }}</div>

