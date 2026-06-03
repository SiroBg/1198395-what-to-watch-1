<?php

namespace App\Http\Responses;

use Symfony\Component\HttpFoundation\Response;
use Throwable;

class Fail extends Base
{
    protected string $message;
    protected Throwable $exception;

    public function __construct(
        Throwable $exception,
        int $statusCode = Response::HTTP_INTERNAL_SERVER_ERROR,
    ) {
        parent::__construct([], $statusCode);

        $this->exception = $exception;

        $this->message = match ($statusCode) {
            400 => 'Некорректный запрос.',
            401 => 'Ошибка аутентификации.',
            403 => 'Доступ запрещён.',
            404 => 'Запрашиваемая страница не существует.',
            409 => 'Конфликт данных.',
            422 => 'Переданные данные не корректны.',
            500 => 'Внутренняя ошибка сервера.',
            default => 'Произошла ошибка.',
        };
    }

    public static function fromException(Throwable $e, int $status): self
    {
        return new self($e, $status);
    }

    protected function makeResponseData(): ?array
    {
        return [
            'message' => $this->message,
        ];
    }
}
