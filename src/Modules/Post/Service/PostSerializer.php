<?php

declare(strict_types=1);

namespace App\Modules\Post\Service;

class PostSerializer
{
    public function serialize(array $post): ?array
    {
        if (empty($post)) {
            return null;
        }

        return [
            'id'      => $post['id'],
            'userId'  => $post['user_id'],
            'text'    => $post['text'],
        ];
    }

    public function serializeItems(array $items): array
    {
        $result = [];

        /** @var array $item */
        foreach ($items as $item) {
            $result[] = $this->serialize($item);
        }

        return $result;
    }
}
