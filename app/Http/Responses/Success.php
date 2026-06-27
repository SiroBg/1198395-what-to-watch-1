<?php

namespace App\Http\Responses;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Symfony\Component\HttpFoundation\Response;

final class Success extends Base
{
    /**
     * Создаёт экземпляр успешного ответа сервера.
     *
     * @param  mixed  $data  Данные ответа.
     * @param  int  $statusCode  Статус-код.
     */
    public function __construct(
        mixed $data = [],
        int $statusCode = Response::HTTP_OK,
    ) {
        parent::__construct($data, $statusCode);
    }

    /** {@inheritdoc} */
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
