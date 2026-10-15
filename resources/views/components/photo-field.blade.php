@props([
    'photo' => null,
    'currentPhoto' => null,
    'removePhoto' => false,
    'label' => 'Фотография',
])

@php
    $preview = null;

    if ($photo && $photo->isPreviewable()) {
        $preview = $photo->temporaryUrl();
    } elseif (!$removePhoto && !empty($currentPhoto)) {
        $preview = str_starts_with($currentPhoto, 'http') ? $currentPhoto : asset('storage/' . $currentPhoto);
    }

    $canRemove = !empty($currentPhoto) && !$photo && !$removePhoto;
    $canRestore = !empty($currentPhoto) && !$photo && $removePhoto;
@endphp

<div {{ $attributes->merge(['class' => 'mb-4']) }}>
    <p class="mb-[5px] text-lg font-semibold">{{ $label }}</p>

    <div class="flex items-center gap-4">
        <div class="relative flex h-20 w-28 min-h-20 min-w-28 shrink-0 items-center justify-center overflow-hidden rounded-lg border border-[#e3e6f0] bg-[#f8f9fa] text-2xl leading-none">
            @if($preview)
                <img src="{{ $preview }}" alt="Предпросмотр" class="h-full w-full object-cover">
            @else
                🏫
            @endif

            @if($canRemove)
                <button type="button" wire:click="$set('removePhoto', true)" title="Удалить фотографию"
                        class="absolute right-1 top-1 flex h-6 w-6 items-center justify-center rounded-full
                            bg-black/60 text-white hover:bg-red-600 transition-colors cursor-pointer">
                    <svg class="h-3 w-3" viewBox="0 0 12 12" fill="none" stroke="currentColor"
                        stroke-width="1.75" stroke-linecap="round" aria-hidden="true">
                        <path d="M2 2l8 8M10 2l-8 8"/>
                    </svg>
                </button>
            @endif
        </div>

        <div class="min-w-0 flex-1">
            <input
                type="file"
                wire:model="photo"
                accept="image/png,image/jpeg,image/webp"
                class="block w-full cursor-pointer text-sm text-gray-600
                    file:mr-3 file:cursor-pointer file:rounded-lg
                    file:border file:border-transparent
                    file:bg-[#eef2ff] file:px-3 file:py-2
                    file:text-sm file:font-semibold file:text-[#3456DB]
                    file:transition-colors file:duration-200
                    hover:file:bg-[#e0e7ff]">

            <div wire:loading wire:target="photo" class="mt-1 text-sm text-secondary">Загрузка фотографии...</div>

            @error('photo')
                <div class="mt-1 text-sm text-red-500">{{ $message }}</div>
            @enderror

            @if($canRestore)
                <div class="mt-2 flex flex-wrap items-center gap-2 text-sm">
                    <span class="text-red-600">Фотография будет удалена после сохранения.</span>
                    <button type="button" wire:click="$set('removePhoto', false)"
                            class="font-semibold text-primary hover:underline cursor-pointer">
                        Вернуть
                    </button>
                </div>
            @else
                <small class="mt-1 block text-xs text-gray-500">JPG, PNG или WEBP, до 5 МБ. Рекомендуется горизонтальное фото.</small>
            @endif
        </div>
    </div>
</div>