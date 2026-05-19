<?php

namespace App\Http\Responses;

use Symfony\Component\HttpFoundation\Response;
use Throwable;

class Fail extends Base
{
    protected string $message;

    public function __construct(
        Throwable $exception,
        int $statusCode = Response::HTTP_INTERNAL_SERVER_ERROR,
    ) {
        parent::__construct([], $statusCode);

        $this->message = $exception->getMessage();
    }

    protected function makeResponseData(): ?array
    {
        return [
            'message' => $this->message,
        ];
    }
}
