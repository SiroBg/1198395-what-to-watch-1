<?php

namespace App\Http\Responses;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Symfony\Component\HttpFoundation\Response;

class Success extends Base
{
    public function __construct(
        mixed $data = [],
        int $statusCode = Response::HTTP_OK,
    ) {
        parent::__construct($data, $statusCode);
    }

    #[\Override]
    protected function makeResponseData(): ?array
    {
        if ($this->data instanceof ResourceCollection) {
            $resourceData = $this->data->toResponse(request())->getData(true);

            $meta = $resourceData['meta'] ?? [];
            $links = $resourceData['links'] ?? [];

            return array_merge(
                ['data' => $resourceData['data'] ?? []],
                $meta,
                $links,
            );
        }

        if ($this->data instanceof JsonResource) {
            $resourceData = $this->data->toResponse(request())->getData(true);
            return [
                'data' => $resourceData['data'] ?? $resourceData,
            ];
        }

        return [
            'data' => $this->prepareData(),
        ];
    }
}
