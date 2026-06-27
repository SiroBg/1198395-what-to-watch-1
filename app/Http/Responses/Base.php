<?php

namespace App\Http\Responses;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Responsable;
use Symfony\Component\HttpFoundation\Response;

abstract class Base implements Responsable
{
    protected mixed $data;

    protected int $statusCode;

    /**
     * Создаёт ответ.
     *
     * @param mixed $data Данные ответа.
     * @param int   $statusCode Статус-код ответа.
     */
    public function __construct(
        mixed $data = [],
        int $statusCode = Response::HTTP_OK,
    ) {
        $this->data = $data;
        $this->statusCode = $statusCode;
    }

    /**
     * @inheritDoc
     *
     * @param $request
     *
     * @return \Illuminate\Http\JsonResponse|Response
     */
    #[\Override]
    public function toResponse($request): \Illuminate\Http\JsonResponse|Response
    {
        return response()->json($this->makeResponseData(), $this->statusCode);
    }

    /**
     * Подготавливает данные.
     *
     * @return array
     */
    protected function prepareData(): array
    {
        if ($this->data instanceof Arrayable) {
            return $this->data->toArray();
        }

        return (array)$this->data;
    }

    /**
     * Создаёт данные ответа.
     *
     * @return array|null
     */
    abstract protected function makeResponseData(): ?array;
}
