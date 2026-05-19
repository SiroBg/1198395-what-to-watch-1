<?php

namespace App\Http\Responses;

use Symfony\Component\HttpFoundation\Response;

class Success extends Base
{
    public function __construct(
        array $data = [],
        int $statusCode = Response::HTTP_OK,
    ) {
        parent::__construct($data, $statusCode);
    }

    protected function makeResponseData(): ?array
    {
        return [
            'data' => $this->prepareData(),
        ];
    }
}
