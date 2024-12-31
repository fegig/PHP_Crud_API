<?php

declare(strict_types=1);
namespace App\Controllers;

use App\Queue\ImageQueue;
use Exception;
use InvalidArgumentException;
use Utility\Core\Response;
use Utility\Core\Validator;
use Utility\Image\ImageUtils;


class ImageController
{
    private const UPLOAD_PATH = __DIR__ . '/../uploads/images/';
    private const QUALITY = 75;

    private ImageQueue $imageModel;
    private ImageUtils $imageUtils;

    public function __construct()
    {
        $this->imageModel = new ImageQueue();
        $this->imageUtils = new ImageUtils();
    }

    public function uploadImage(array $data)
    {
      
        try {
            $uploadResult = $this->imageUtils->uploadImage($data, self::UPLOAD_PATH, self::QUALITY);
          
            if (!$uploadResult['success']) {
                return $uploadResult;
            }
            
            $imageData = [
                'filename' => $uploadResult['filename'],
                'filepath' => $uploadResult['path'],
                'uploaded_at' => date('Y-m-d H:i:s')
            ];
           
             $saved = $this->imageModel->createImage($imageData);
           
            if (!$saved) {
                @unlink($uploadResult['path']);
                Response::errorResponse('Failed to save image record', 400);
            }

            Response::successResponse($imageData, 200);

        } catch (Exception $e) {
            Response::errorResponse($e->getMessage(), 404);
        }
    }
    

    public function getImage(?string $imageId = null): void {
        try {
            if ($imageId === null) {
                $images = $this->imageModel->getAllImages();
                Response::successResponse($images, 200);
            } else {
                
                Validator::validate(['imageId' => $imageId], [
                    'imageId' => 'required|string',
                ]);

                $image = $this->imageModel->getImage($imageId);
                
                if ($image) {
                    Response::successResponse($image, 200);
                } else {
                    Response::errorResponse('Image not found', 404);
                }
            }
        } catch (InvalidArgumentException $e) {
            Response::errorResponse($e->getMessage(), 400);
        } catch (Exception $e) {
            Response::errorResponse('An error occurred while fetching images', 404);
        }
    }
}
?>