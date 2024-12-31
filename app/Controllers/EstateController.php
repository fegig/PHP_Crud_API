<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Seeds\EstateSeed;
use Exception;
use InvalidArgumentException;
use Utility\Core\Response;
use Utility\Core\Validator;
use Utility\Connection\Query;


class EstateController {

    private EstateSeed $estateModel;

    public function __construct() {
        $this->estateModel = new EstateSeed();
    }

    public function createEstate(): void
    {
            Validator::validate($_POST, [
                'propertyId' => 'required',
                'price' => 'required',
                'address' => 'required',
                'size' => 'required',
                'baths' => 'required',
                'rooms' => 'required',
                'park' => 'required',
                'type' => 'required',
                'mail' => 'required|email',
                'agent' => 'required',
                'option' => 'required',
            ]);

            $result = $this->estateModel->createEstate($_POST);

            if ($result) {
                Response::successResponse(['message' => 'Estate created successfully'], 200);
            } else {
                Response::errorResponse('Failed to create estate', 500);
            }
      
    }

    public function createImage(): void
    {
            Validator::validate($_POST, [
                'filename' => 'required|string',
                'propertyId' => 'required|string',
            ]);

            $result = $this->estateModel->createImage($_POST);

            if ($result) {
                Response::successResponse(['message' => 'Image created successfully'], 200);
            } else {
                Response::errorResponse('Failed to create image', 500);
            }
      
    }

    public function getEstate(?string $propertyId = null): void {
        try {
            if ($propertyId === null) {
                // Check if propertyId is provided in the query string
                $propertyId = $_GET['propertyId'] ?? null;
            }

            if ($propertyId === null) {
                // If no propertyId is provided, fetch all estates
                $estates = $this->estateModel->getAllEstates();
                if ($estates) {
                    $newEstates = [];
                    foreach ($estates as $estate) {
                        $payload = ["estate" => $estate, "images" => $this->estateModel->getImages($estate['property_id'])];
                        $newEstates[] = $payload;
                    }
                }

                    Response::successResponse($newEstates, 200);
            } else {
                // If propertyId is provided, fetch a single estate
                Validator::validate(['propertyId' => $propertyId], [
                    'propertyId' => 'required|string',
                ]);

                $estate = $this->estateModel->getEstate($propertyId);
                if ($estate) {
                    $payload = ["estate" => $estate, "images" => $this->estateModel->getImages($propertyId)];
                }
                if ($payload) {
                    Response::successResponse($payload, 200);
                } else {
                    Response::errorResponse('Estate not found', 404);
                }
            }
        } catch (InvalidArgumentException $e) {
            Response::errorResponse($e->getMessage(), 400);
        } catch (Exception $e) {
            Response::errorResponse('An error occurred while fetching estates', 500);
        }
    }

    public function updateEstate(string $propertyId): void
    {
        try {
            Validator::validate($_POST, [
                'price' => 'required|string',
                'address' => 'required|string',
                'size' => 'required',
                'baths' => 'required',
                'rooms' => 'required',
                'park' => 'required',
                'type' => 'required',
                'mail' => 'required',
                'agent' => 'required',
                'option' => 'required',
            ]);

            $result = $this->estateModel->updateEstate($propertyId, $_POST);

            if ($result) {
                Response::successResponse(['message' => 'Estate updated successfully'], 200);
            } else {
                Response::errorResponse('Estate not found or update failed', 404);
            }
        } catch (InvalidArgumentException $e) {
            Response::errorResponse($e->getMessage(), 400);
        }
    }

    public function deleteEstate(string $propertyId): void {
        $result = $this->estateModel->deleteEstate($propertyId);
        
        if ($result) {
            Response::successResponse(['message' => 'Estate deleted successfully'], 200);
        } else {
            Response::errorResponse('Estate not found or delete failed', 404);
        }
    }

    public function deleteImage(string $imageId): void {
        $result = $this->estateModel->deleteImage($imageId);

        if ($result) {
            Response::successResponse(['message' => 'Image deleted successfully'], 200);
        } else {
            Response::errorResponse('Image not found or delete failed', 404);
        }
    }
}
