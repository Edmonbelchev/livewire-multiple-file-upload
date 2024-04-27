# Livewire File Upload Test

This repository contains a simple setup to test multiple file uploads to S3 bucket using Livewire in Laravel. Follow the instructions below to get started.

![Multiple File Upload GIF](https://raw.githubusercontent.com/Edmonbelchev/livewire-multiple-file-upload/main/multiple_file_uploads.gif)

## Prerequisites

- PHP installed on your machine (version 7.4 or later recommended)
- Composer installed globally
- AWS account with credentials (Access Key ID and Secret Access Key) for testing file uploads to S3

## Setup Instructions

1. Run composer install
   
```
composer install
```

2. Once the dependencies are installed, create a .env file in the root directory of the project. Populate the AWS fields with the following variables:

```
AWS_ACCESS_KEY_ID=your-access-key-id
AWS_SECRET_ACCESS_KEY=your-secret-access-key
AWS_DEFAULT_REGION=your-preferred-region
AWS_BUCKET=your-bucket-name
```

3. After configuring the .env file, generate an application key by running:

```
php artisan key:generate
```

4. Finally, start the Laravel development server by running:

```
php artisan serve
```

For more detailed information you can read the article I wrote on how to implement multiple file upload to S3 bucket in Livewire here: [LinkedIn Article](https://www.linkedin.com/pulse/implementing-multiple-file-upload-s3-bucket-edmon-belchev-4oagf/).
