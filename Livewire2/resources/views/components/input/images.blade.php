<div class="flex flex-col">
    <input
        class="bg-purple-light border-0 hidden" 
        type="file"
        accept=".png,.jpg,.jpeg,.webp"
        multiple
        {{ $attributes }}
    />

    <div class="w-full grid grid-cols-5 gap-3">
        {{ $slot }}

        <label for="{{ $attributes->get('name') }}">
            {{ $trigger }}
        </label>
    </div>
</div>

<x-scripts.file-upload 
  modelName="{{ $attributes->get('wire:model') }}" 
  componentLoader="loadingImagesUpload" 
  inputName="{{ $attributes->get('name') }}" 
/>