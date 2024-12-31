<?php

declare(strict_types=1);

namespace App\Models;

use Exception;
use Utility\Connection\Query;


class EstateModel
{
    public function createEstate(array $data): bool
    {
        try {
            $result = Query::table('estates')
                ->insert([
                    'property_id' => $data['propertyId'],
                    'price' => $data['price'],
                    'address' => $data['address'],
                    'size' => $data['size'],
                    'baths' => $data['baths'],
                    'rooms' => $data['rooms'],
                    'park' => $data['park'],
                    'type' => $data['type'],
                    'mail' => $data['mail'],
                    'agent' => $data['agent'],
                    'option' => $data['option']
                ])
                ->execute();

            return $result !== false && $result > 0;
        } catch (Exception $e) {
            error_log("Error creating estate: " . $e->getMessage());
            return false;
        }
    }

    public function getEstate(string $propertyId)
    {
        return Query::table('estates')
            ->select(['estates.*', 'agent.agent_name', 'agent.agent_email'])
            ->innerJoin('agent', 'estates.agent', '=', 'agent.agent_id')
            ->where('estates.property_id', $propertyId)
            ->limit(1)
            ->execute();
    }

    public function updateEstate(string $propertyId, array $data): bool
    {
        try {
            $allowedFields = ['price', 'address', 'size', 'baths', 'rooms', 'park', 'type', 'mail', 'agent', 'option'];
            $updateData = array_intersect_key($data, array_flip($allowedFields));

            $result = Query::table('estates')
                ->update($updateData)
                ->where('property_id', $propertyId)
                ->execute();

            return $result > 0;
        } catch (Exception $e) {
            error_log("Error updating estate: " . $e->getMessage());
            return false;
        }
    }

    public function deleteEstate(string $propertyId): bool
    {
        $result = Query::table('estates')
            ->delete()
            ->where('property_id', $propertyId)
            ->execute();

        return $result > 0;
    }

    public function getAllEstates()
    {
        return Query::table('estates')
            ->select(['estates.*', 'agent.agent_name', 'agent.agent_email'])
            ->groupBy('estates.id')
            ->innerJoin('agent', 'estates.agent', '=', 'agent.agent_id')
            ->orderBy('estates.created_at', 'DESC')
            ->execute();
    }

    public function createImage(array $data): bool
    {
        try {
            $result = Query::table('estate_images')
                ->insert([
                    'image' => $data['filename'],
                    'estate_id' => $data['propertyId']
                ])
                ->execute();

            return $result !== false;
        } catch (Exception $e) {
            error_log("Error creating image: " . $e->getMessage());
            return false;
        }
    }

    public function getImages(string $propertyId)
    {
        return Query::table('estate_images')
            ->select(['*'])
            ->where('estate_images.estate_id', $propertyId)
            ->execute();
    }

    public function deleteImage(string $imageId)
    {
        $result = Query::table('estate_images')
            ->delete()
            ->where('id', $imageId)
            ->execute();

        return $result > 0;
    }
}
