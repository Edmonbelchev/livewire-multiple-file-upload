<?php
namespace App\Traits;

use Illuminate\Http\UploadedFile;
use Livewire\FileUploadConfiguration;

use Livewire\TemporaryUploadedFile;
use Facades\Livewire\GenerateSignedUploadUrl;
use Livewire\WithFileUploads as BaseWithFileUploads;

trait WithMultipleFileUploads
{
    use BaseWithFileUploads;

    // Create fileArray to store the file information
    public $fileArray = [];

    public function startUpload($name, $fileInfo, $isMultiple)
    {
        if (FileUploadConfiguration::isUsingS3()) {
            // Fill the file array with the file(s) information
            $this->fileArray = $fileInfo;

            $this->generateS3SignedUploadUrl($name);
        } else {
            $this->emit('upload:generatedSignedUrl', $name, GenerateSignedUploadUrl::forLocal())->self();
        }
    }

    public function generateS3SignedUploadUrl($name)
    {
        $file = UploadedFile::fake()->create($this->fileArray[0]['name'], $this->fileArray[0]['size'] / 1024, $this->fileArray[0]['type']);

        $this->emit('generatedSignedUrlForS3Bucket', $name, GenerateSignedUploadUrl::forS3($file));
    }

    public function finishUpload($name, $tmpPath, $isMultiple)
    {
        $this->cleanupOldUploads();
        
        // Check if we are using S3
        if (FileUploadConfiguration::isUsingS3()) {
            // Create a new collection with the TemporaryUploadedFile
            $file = collect($tmpPath)->map(function ($i) {
                return TemporaryUploadedFile::createFromLivewire($i);
            })->toArray();

            // Emit the finishUpload event from our javascript
            $this->emit('finishUpload', $name, collect($file)->map->getFilename()->toArray());

            // Remove the uploaded file from the array
            array_shift($this->fileArray);

            // Check if there are still files to upload 
            // Repeat the process with next file in line
            if (count($this->fileArray) > 0) {
                $this->generateS3SignedUploadUrl($name);
            }
        } else {
            // Use the original finishUpload method
            if ($isMultiple) {
                $file = collect($tmpPath)->map(function ($i) {
                    return TemporaryUploadedFile::createFromLivewire($i);
                })->toArray();
                $this->emit('upload:finished', $name, collect($file)->map->getFilename()->toArray())->self();
            } else {
                $file = TemporaryUploadedFile::createFromLivewire($tmpPath[0]);
                $this->emit('upload:finished', $name, [$file->getFilename()])->self();

                // If the property is an array, but the upload ISNT set to "multiple"
                // then APPEND the upload to the array, rather than replacing it.
                if (is_array($value = $this->getPropertyValue($name))) {
                    $file = array_merge($value, [$file]);
                }
            }
        }

        // Sync the input
        $this->syncInput($name, $file);
    }
}
