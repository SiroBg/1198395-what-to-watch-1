<?php

namespace App\Http\Responses;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

abstract class Base implements Responsable
{
    protected mixed $data;

    protected int $statusCode;

    /**
     * Создаёт ответ.
     *
     * @param  mixed  $data  Данные ответа.
     * @param  int  $statusCode  Статус-код ответа.
     */
    public function __construct(
        mixed $data = [],
        int $statusCode = Response::HTTP_OK,
    ) {
        $this->data = $data;
        $this->statusCode = $statusCode;
    }

    /**
     * {@inheritDoc}
     */
    #[\Override]
    public function toResponse($request): JsonResponse|Response
    {
        return response()->json($this->makeResponseData(), $this->statusCode);
    }

    /**
     * Подготавливает данные.
     */
    protected function prepareData(): array
    {
        if ($this->data instanceof Arrayable) {
            return $this->data->toArray();
        }

        return (array) $this->data;
    }

    /**
     * Создаёт данные ответа.
     */
    abstract protected function makeResponseData(): ?array;
}
