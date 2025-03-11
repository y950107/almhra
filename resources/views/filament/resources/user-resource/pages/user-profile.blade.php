<x-filament::page>
    <form wire:submit.prevent="save" class="space-y-6 max-w-lg mx-auto">
        {{ $this->form }}

        <x-filament::button type="submit" class="w-full">
            حفظ التعديلات
        </x-filament::button>

        @if (session()->has('success'))
            <div class="text-green-500 text-center mt-4">
                {{ session('success') }}
            </div>
        @endif
    </form>
</x-filament::page>
