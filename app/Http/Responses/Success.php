<?php

namespace App\Http\Responses;

use Symfony\Component\HttpFoundation\Response;

class Success extends Base
{
    public function __construct(
        array|object $data = [],
        int $statusCode = Response::HTTP_OK,
    ) {
        parent::__construct($data, $statusCode);
    }

    protected function makeResponseData(): ?array
    {
        if ($this->data instanceof ResourceCollection) {
            $paginatedData = $this->data->toResponse(request())->getData(true);

            return array_merge(
                ['data' => $paginatedData['data'] ?? []],
                $paginatedData['links'] ?? null,
                $paginatedData['meta'] ?? null,
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
