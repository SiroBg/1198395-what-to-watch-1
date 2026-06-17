<?php

namespace App\Http\Responses;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Responsable;
use Symfony\Component\HttpFoundation\Response;

abstract class Base implements Responsable
{
    protected mixed $data;
    protected int $statusCode;

    public function __construct(
        mixed $data = [],
        int $statusCode = Response::HTTP_OK,
    ) {
        $this->data = $data;
        $this->statusCode = $statusCode;
    }

    public function toResponse($request)
    {
        return response()->json($this->makeResponseData(), $this->statusCode);
    }

    protected function prepareData(): array
    {
        if ($this->data instanceof Arrayable) {
            return $this->data->toArray();
        }

        return (array) $this->data;
    }

    abstract protected function makeResponseData(): ?array;
}
