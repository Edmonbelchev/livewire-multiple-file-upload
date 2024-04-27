@props([
    'modelName' => null,
    'componentLoader' => null,
    'inputName' => null,
])

<script>
    document.addEventListener('livewire:load', function() {
        console.log('test')
        const component = Livewire.find(@this.__instance.id);
        const fileInput = document.querySelector(`input[name="${@json($inputName)}"]`);
        const filesArray = []
        const modelName = @json($modelName)

        // Create a new FormData object
        const formData = new FormData();

        fileInput.addEventListener('change', function(event) {
            const files = event.target.files;

            for (let i = 0; i < files.length; i++) {
                formData.append('files[]', files[i]);
                filesArray.push(files[i]);
            }

            // If we try to upload a second time we need to check if the filesArray's length is less than the formData's length
            if (filesArray.length != formData.getAll('files[]').length) {
                let fileInfos = filesArray.map(file => {
                    return {
                        name: file.name,
                        size: file.size,
                        type: file.type
                    }
                })

                // Run the start upload function from WithMultipleFileUploads trait
                component.startUpload(modelName, fileInfos, false)
            }
        });

        component.on("generatedSignedUrlForS3Bucket", (name = modelName, file) => {
            if (name === modelName) {
                handleSignedUrl(name, file);
            }
        });

        const handleSignedUrl = async (name, payload) => {
            @this.set(@json($componentLoader), true);

            // Retrieve next file in line for upload
            const file = getNextFileInLine()

            // Ensure file object has headers property
            if (!payload.headers) {
                console.error("File object does not have headers property.");
                return;
            }

            let headers = payload.headers;

            if ("Host" in headers) delete headers.Host;

            let url = payload.url;

            makeRequest(name, file, "put", url, headers, (response) => {
                return [payload.path];
            });
        };

        const makeRequest = async (name, file, method, url, headers, retrievePaths) => {
            try {
                const xhr = new XMLHttpRequest();
                xhr.open(method, url);

                // Set headers
                for (const [key, value] of Object.entries(headers)) {
                    xhr.setRequestHeader(key, value);
                }

                // Set progress event
                xhr.upload.onprogress = function(event) {
                    if (event.lengthComputable) {
                        const percentComplete = Number((event.loaded / event.total) * 100).toFixed(2);

                        @this.set("uploadProgress", percentComplete)
                    }
                };

                xhr.onload = function() {
                    if (xhr.status >= 200 && xhr.status < 300) {
                        const paths = retrievePaths(xhr);

                        // Call next method
                        component.finishUpload(name, paths, false);
                        @this.set(@json($componentLoader), false);
                        @this.set("uploadProgress", 0)

                        // console.log(`File ${name} uploaded successfully. Paths:`, paths);
                    } else {
                        console.error(`Failed to upload file ${name} to S3.`);
                    }
                };

                xhr.onerror = function() {
                    console.error('Error occurred during the request.');
                };

                xhr.send(file);
            } catch (error) {
                console.error('Error:', error);
            }
        };

        // Function to extract file by its name
        const getNextFileInLine = () => {
            // Get the first file in filesArray
            const fileFromFilesArray = filesArray[0];

            // Iterate over FormData entries
            for (const [key, value] of formData.entries()) {
                // Check if the key is 'files[]' and the file matches
                if (key === 'files[]' && value === fileFromFilesArray) {
                    // Remove the file from filesArray
                    filesArray.shift();

                    // Return the file
                    return value;
                }
            }

            return null
        };
    })
</script>