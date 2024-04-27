@props([
    'for',
    'text',
    'error',
    'description' => false,
])

<div {{$attributes->merge(['class' => ''])}}>
    <label for="{{$for}}" class="block text-xxs font-semibold">{{$text}}</label>

    {{ $slot }}

    @if($error)
        <div class="mt-1 text-red-500 text-sm">{{$error}}</div>
    @endif

    @if($description)
        <p class="mt-2 text-sm text-gray-500">
            {{$description}}
        </p>
    @endif
</div>
