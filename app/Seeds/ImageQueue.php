<?php

declare(strict_types=1);

namespace App\Queue;

use Exception;
use Utility\Connection\Query;

class ImageQueue
{
    public function createImage(array $data): bool
    {
        try {
            $result = Query::table('images')
                ->insert([
                    'image' => $data['filename']
                ])
                ->execute();

            return $result !== false;
        } catch (Exception $e) {
            error_log("Error creating image: " . $e->getMessage());
            return false;
        }
    }
    public function getImage(string $imageId)
    {
        return Query::table('images')
            ->select(['*'])
            ->where('images.id', $imageId)
            ->limit(1)
            ->execute();
    }
    
    public function getAllImages()
    {
        return Query::table('images')
            ->select(['*'])
            ->groupBy('images.id')
            ->execute();
    }
}