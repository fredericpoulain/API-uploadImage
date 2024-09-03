<?php

namespace App\Controller\API;


use App\Service\AwsBucketS3Service;
use App\Service\ImageService;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Attribute\Route;


class UploadController extends AbstractController
{
    //Adjust this string if the "MAX_FILE_SIZE" constant of the "ImageService" service is modified
    const MAX_FILE_SIZE_STRING = '10MB';
    const MAX_FILES = 5;


    /**
     * @param Request $request
     * @param ImageService $imageService
     * @param AwsBucketS3Service $awsBucketS3Service
     * @return JsonResponse
     */
    #[Route('/api/upload', name: 'app_upload', methods: ['POST'])]
    public function upload(
        Request $request,
        ImageService $imageService,
        AwsBucketS3Service $awsBucketS3Service,
    ): JsonResponse
    {

        $files = $request->files->get('images');
        if (count($files) > self::MAX_FILES) {
            throw new BadRequestHttpException('You can upload a maximum of ' . self::MAX_FILES . ' files at a time.');
        }
        if (count($files) === 0) {
            throw new BadRequestHttpException("No files found");
        }
        $errors = [];

        try {
            //I check all the pictures
            foreach ($files as $file) {
                try {
                    $this->verifImage($imageService, $file);
                    // Continuer le traitement si l'image est valide...
                } catch (Exception $e) {
                    $errors[] = $file->getClientOriginalName() . ': ' . $e->getMessage();
                }
            }
            // If errors are detected, the process is stopped and the errors are returned.
            if (count($errors) > 0) {
                return $this->json([
                    'error' => 'Image(s) failed validation',
                    'details' => $errors
                ], \Symfony\Component\HttpFoundation\Response::HTTP_BAD_REQUEST);
            }
            $uploadBucketErrors = [];
            $arrayImagesSuccessfullyUploaded = [];
            $numberImagesSuccessfullyUploaded = 0;
            foreach ($files as $file) {
                $imageUniqName = $imageService->generateImageName($file);
                //Store in bucket s3 :
                $result = $awsBucketS3Service->addImage($imageUniqName, $file->getRealPath());
                if ($result['status'] === 'success') {
                    $numberImagesSuccessfullyUploaded++;
                    $arrayImagesSuccessfullyUploaded[] = $file->getClientOriginalName();
                }else{
                    $uploadBucketErrors[] = [
                        'fileName' => $file->getClientOriginalName(),
                        'message' => $result['message'],
                        'errorCode' => 'UPLOAD_ERROR'
                    ];
                }

            }
            if (count($files) === $numberImagesSuccessfullyUploaded){
                $message = 'All images have been uploaded successfully';
            }else{
                $message = 'Some images could not be uploaded';
            }


            return $this->json([
                'message' => $message,
                'imagesUploaded' => $numberImagesSuccessfullyUploaded . '/' .count($files),
                'imagesNamesUploaded' => $arrayImagesSuccessfullyUploaded,
                'ImagesNotUploaded' => $uploadBucketErrors
            ]);
        } catch (Exception $e){
            return $this->json($e->getMessage(),\Symfony\Component\HttpFoundation\Response::HTTP_BAD_REQUEST);
        }

    }

    /**
     * @param $imageService
     * @param $imageUpload
     * @return void
     * @throws Exception
     */

    private function verifImage(ImageService $imageService, UploadedFile $imageUpload): void
    {
        $name = $imageUpload->getClientOriginalName();

        //we check its mime type, and extensions and size
        if (!$imageService->checkExtension($imageUpload) || !$imageService->checkTypeMime($imageUpload)) {
            throw new Exception("Unauthorized image format (jpg, jpeg, png or webp) for : " . $name);
        }
        if (!$imageService->checkSize($imageUpload)) {
            throw new Exception("Image " . $name .  " size must not exceed ". self::MAX_FILE_SIZE_STRING);
        }

    }

}
