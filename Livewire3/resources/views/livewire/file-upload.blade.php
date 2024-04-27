<form wire:submit.prevent="save">
    <div class="shadow sm:rounded-md w-fit bg-white py-8 px-6 flex flex-col gap-2">
        <x-input.group for="images" text="Images" :error="$errors->first('images.*')">  
            <div class="inline-flex flex-col mt-1 gap-1">
                <x-input.images wire:model="userUploads" id="images" name="images">
                    <x-slot name="trigger">
                        <div class="hover:cursor-pointer">
                            <div class="w-24 h-24 p-4 rounded-xl bg-purple-200 flex items-center justify-center">
                                <svg width="20" height="20" viewBox="0 0 20 20" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M7.50008 18.3327H12.5001C16.6667 18.3327 18.3334 16.666 18.3334 12.4993V7.49935C18.3334 3.33268 16.6667 1.66602 12.5001 1.66602H7.50008C3.33341 1.66602 1.66675 3.33268 1.66675 7.49935V12.4993C1.66675 16.666 3.33341 18.3327 7.50008 18.3327Z"
                                        stroke="#730EC3" stroke-linecap="round" stroke-linejoin="round" />
                                    <path
                                        d="M7.49992 8.33333C8.42039 8.33333 9.16659 7.58714 9.16659 6.66667C9.16659 5.74619 8.42039 5 7.49992 5C6.57944 5 5.83325 5.74619 5.83325 6.66667C5.83325 7.58714 6.57944 8.33333 7.49992 8.33333Z"
                                        stroke="#730EC3" stroke-linecap="round" stroke-linejoin="round" />
                                    <path
                                        d="M2.2251 15.7918L6.33343 13.0335C6.99176 12.5918 7.94176 12.6418 8.53343 13.1501L8.80843 13.3918C9.45843 13.9501 10.5084 13.9501 11.1584 13.3918L14.6251 10.4168C15.2751 9.85846 16.3251 9.85846 16.9751 10.4168L18.3334 11.5835"
                                        stroke="#730EC3" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </div>
                        </div>
                    </x-slot>
                    @if ($images)
                        @foreach ($images as $image)
                            <div class="relative" wire:key="temp-image-{{ $image->getFilename() }}">
        
                                <a href="{{ $image->temporaryUrl() }}" target="_blank"
                                    class="relative flex justify-center items-center w-24 h-24">
        
                                    <img class="object-cover w-24 h-24 rounded-xl" src="{{ $image->temporaryUrl() }}"
                                        alt="">
        
                                    <div class="absolute">
                                        <svg class="stroke-current text-purple-lighter" width="24" height="24"
                                            viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M21 9V3H15" stroke-width="1.5" stroke-linecap="round"
                                                stroke-linejoin="round" />
                                            <path d="M3 15V21H9" stroke-width="1.5" stroke-linecap="round"
                                                stroke-linejoin="round" />
                                            <path d="M21 3L13.5 10.5" stroke-width="1.5" stroke-linecap="round"
                                                stroke-linejoin="round" />
                                            <path d="M10.5 13.5L3 21" stroke-width="1.5" stroke-linecap="round"
                                                stroke-linejoin="round" />
                                        </svg>
                                    </div>
                                </a>
                            </div>
                        @endforeach
                    @endif
        
                    @if ($loadingImagesUpload)
                        <div class="w-24 h-24 p-4 rounded-xl bg-purple-200 relative overflow-hidden">
                            <div class="relative z-10 h-full flex items-center justify-center flex-col text-xs">
                                <span>Uploading...</span>
                                <span>{{ $uploadProgress }}%</span>
                            </div>
        
                            <div class="absolute w-full bg-purple-300 bottom-0 left-0 transition-all z-1"
                                style="height: {{ $uploadProgress }}%"></div>
                        </div>
                    @endif
        
                </x-input.images>
            </div>
        </x-input.group>

        <button class="bg-purple-300 py-2 px-4 text-white rounded-md w-fit" type="submit">Upload</button>
    </div>
</form>
