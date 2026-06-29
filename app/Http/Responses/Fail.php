<?php

namespace App\Http\Responses;

use Symfony\Component\HttpFoundation\Response;
use Throwable;

/**
 * @psalm-api
 */
class Fail extends Base
{
    protected string $message;

    protected Throwable $exception;

    protected array $errors;

    /**
     * Создаёт экземпляр ответа ошибки сервера.
     *
     * @param  Throwable  $exception  Исключение.
     * @param  int  $statusCode  Статус-код.
     * @param  array  $errors  Ошибки (при валидации).
     */
    public function __construct(
        Throwable $exception,
        int $statusCode = Response::HTTP_INTERNAL_SERVER_ERROR,
        array $errors = [],
    ) {
        parent::__construct([], $statusCode);

        $this->exception = $exception;
        $this->errors = $errors;

        $this->message = match ($statusCode) {
            400 => 'Некорректный запрос.',
            401 => 'Ошибка аутентификации.',
            403 => 'Доступ запрещён.',
            404 => 'Запрашиваемая страница не существует.',
            409 => 'Конфликт данных.',
            422 => 'Переданные данные некорректны.',
            500 => 'Внутренняя ошибка сервера.',
            default => 'Произошла ошибка.',
        };
    }

    /**
     * Создаёт ответ-ошибку из исключения.
     *
     * @param  Throwable  $e  Исключение.
     * @param  int  $status  Статус-код.
     * @param  array  $errors  Ошибки.
     */
    public static function fromException(
        Throwable $e,
        int $status,
        array $errors = []
    ): self {
        return new self($e, $status, $errors);
    }

    /** {@inheritdoc} */
    #[\Override]
    protected function makeResponseData(): ?array
    {
        $response['message'] = $this->message;
        if (! empty($this->errors)) {
            $response['errors'] = $this->errors;
        }

        return $response;
    }
}
