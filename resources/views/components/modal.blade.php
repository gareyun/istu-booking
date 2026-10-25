@props(['title', 'width' => '525px', 'error' => null, 'is_hidden_button' => false])

<div class="fixed inset-0 z-[1000] bg-[rgba(26,42,108,0.35)] backdrop-blur-[4px]">
    <div class="fixed top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 bg-white w-[500px] max-w-full max-h-[90vh]
        rounded-2xl shadow-[0_10px_40px_rgba(0,0,0,0.2)] p-[25px] pr-1 flex flex-col animate-modalFade overflow-hidden"
        style="width: {{ $width }};">

        <h2 class="text-xl font-bold text-[#1a2a6c] border-b-2 border-[#1a2a6c] pb-2 mb-5">{{ $title }}</h2>

        @if ($errors->any() || $error)
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4 mr-[20px]">
                <ul class="list-disc pl-5">
                    @foreach ($errors->all() as $message)
                        <li>{{ $message }}</li>
                    @endforeach
                    @if ($error)
                        <li>{{ $error }}</li>
                    @endif
                </ul>
            </div>
        @endif
        
        <div class="flex-1 overflow-y-auto pr-[20px]">
            {{ $slot }}

            @if ($is_hidden_button)
                <label class="flex items-center gap-2 cursor-pointer mt-1">
                    <input type="checkbox" wire:model="is_hidden" class="w-4 h-4 accent-primary">
                    <span class="text-lg font-semibold">Скрыть для бронирования</span>
                </label>
                <p class="text-sm text-gray-500 mt-1 mb-4">
                    Пространство не будет отображаться в форме заявки. Уже поданные и одобренные заявки не затрагиваются.
                </p>
            @endif
        </div>

        @if (isset($footer))
            <div class="flex justify-end gap-2.5 mt-2.5 pr-[20px]">
                {{ $footer }}
            </div>
        @endif
    </div>
</div>