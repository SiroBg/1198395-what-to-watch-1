<?php

namespace App\Http\Requests;

use App\Models\Comment;
use App\Models\Film;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class CreateCommentRequest extends FormRequest
{
    /**
     * Проверяет авторизацию пользователя.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Правила валидации.
     */
    public function rules(): array
    {
        return [
            'text' => [
                'required',
                'string',
                'min:50',
                'max:400',
            ],
            'rating' => [
                'nullable',
                'integer',
                'min:1',
                'max:10',
                'required_without:comment_id',
                'prohibited_unless:comment_id,null',
            ],
            'comment_id' => [
                'sometimes',
                'nullable',
                'integer',
                Rule::exists('comments', 'id'),
            ],
        ];
    }

    /**
     * Валидатор отзывов и комментариев.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $commentId = $this->input('comment_id');
            if ($commentId) {
                $comment = Comment::find($commentId);

                if (! $comment) {
                    $validator->errors()->add(
                        'comment_id',
                        'Комментарий не найден.'
                    );

                    return;
                }

                /** @var Film|null $film */
                $film = $this->route('film');

                if ($comment->film_id !== $film->id) {
                    $validator->errors()->add(
                        'comment_id',
                        'Комментарий принадлежит другому фильму.',
                    );
                }
            }
        });
    }
}
