@props([
    'title',
    'description',
    'name',
    'default' => 'false'
])

<div class="flex items-center justify-between gap-4">
    <div>
        <h3 class="text-sm font-medium text-gray-900">{{ $title }}</h3>
        <p class="text-sm text-gray-500">{{ $description }}</p>
    </div>
    <x-ui.toggle :default="$default" :name="$name" />
</div>
