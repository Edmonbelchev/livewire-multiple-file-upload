<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Validate;
use App\Traits\WithMultipleFileUploads;

class FileUpload extends Component
{
    use WithMultipleFileUploads;

    public $images;
    public $userUploads;
    public $loadingImagesUpload = false;
    public $uploadProgress = 0;

    // Add rules for the images array
    #[Validate(['images.*' => 'image|max:1024'])]

    // Update the images array with the new images
    public function updatedUserUploads()
    {
        foreach ($this->userUploads as $file) {
            $this->images[] = $file; // Append uploaded files to the images array
        }
        
        $this->userUploads = []; // Reset the userUploads array
    }

    public function save()

    {
        foreach ($this->images as $image) {
            $image->store('multiple-s3-uploads', 's3');
        }
    }

    public function render()
    {
        return view('livewire.file-upload');
    }
}
