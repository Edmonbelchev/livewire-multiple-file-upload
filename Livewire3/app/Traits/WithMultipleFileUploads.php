<?php

namespace App\Traits;

use Illuminate\Http\UploadedFile;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\Features\SupportFileUploads\FileUploadConfiguration;
use Facades\Livewire\Features\SupportFileUploads\GenerateSignedUploadUrl;
use Livewire\Features\SupportFileUploads\WithFileUploads;

trait WithMultipleFileUploads
{
    use WithFileUploads;

    // Create fileArray to store the file information
    public $fileArray = [];

    public function _startUpload($name, $fileInfo, $isMultiple)
    {
        if (FileUploadConfiguration::isUsingS3()) {
            // Fill the file array with the file(s) information
            if ($isMultiple) {
                $this->fileArray = $fileInfo;

                $this->generateS3SignedUploadUrl($name);

                return;
            } else {
                $file = UploadedFile::fake()->create($fileInfo[0]['name'], $fileInfo[0]['size'] / 1024, $fileInfo[0]['type']);

                $this->dispatch('upload:generatedSignedUrlForS3', name: $name, payload: GenerateSignedUploadUrl::forS3($file))->self();
    
                return;
            }
        }

        $this->dispatch('upload:generatedSignedUrl', name: $name, url: GenerateSignedUploadUrl::forLocal())->self();
    }

    public function generateS3SignedUploadUrl($name)
    {
        $file = UploadedFile::fake()->create($this->fileArray[0]['name'], $this->fileArray[0]['size'] / 1024, $this->fileArray[0]['type']);

        $this->dispatch('generatedSignedUrlForS3Bucket', $name, GenerateSignedUploadUrl::forS3($file));
    }

    public function _finishUpload($name, $tmpPath, $isMultiple)
    {
        if (FileUploadConfiguration::shouldCleanupOldUploads()) {
            $this->cleanupOldUploads();
        }

        if ($isMultiple) {
            // Check if we are using S3
            if (FileUploadConfiguration::isUsingS3()) {
                // Create a new collection with the TemporaryUploadedFile
                $file = collect($tmpPath)->map(function ($i) {
                    return TemporaryUploadedFile::createFromLivewire($i);
                })->toArray();

                // Dispatch the finishUpload event from our javascript
                $this->dispatch('finishUpload', $name, collect($file)->map->getFilename()->toArray());

                // Remove the uploaded file from the array
                array_shift($this->fileArray);

                // Check if there are still files to upload 
                // Repeat the process with next file in line
                if (count($this->fileArray) > 0) {
                    $this->generateS3SignedUploadUrl($name);
                }
            } else {
                $file = collect($tmpPath)->map(function ($i) {
                    return TemporaryUploadedFile::createFromLivewire($i);
                })->toArray();
                $this->dispatch('upload:finished', name: $name, tmpFilenames: collect($file)->map->getFilename()->toArray())->self();
            }
        } else {
            $file = TemporaryUploadedFile::createFromLivewire($tmpPath[0]);
            $this->dispatch('upload:finished', name: $name, tmpFilenames: [$file->getFilename()])->self();

            // If the property is an array, but the upload ISNT set to "multiple"
            // then APPEND the upload to the array, rather than replacing it.
            if (is_array($value = $this->getPropertyValue($name))) {
                $file = array_merge($value, [$file]);
            }
        }

        app('livewire')->updateProperty($this, $name, $file);
    }
}
