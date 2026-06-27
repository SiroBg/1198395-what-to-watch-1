<?php

namespace App\Http\Requests;

use App\Models\Comment;
use Illuminate\Foundation\Http\FormRequest;

final class UpdateCommentRequest extends FormRequest
{
    /**
     * Правила валидации.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'text'   => [
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
            ],
        ];
    }

    /**
     * Валидатор отзывов и комментариев.
     *
     * @param $validator
     *
     * @return void
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            /** @var Comment|null $comment */
            $comment = $this->route('comment');

            if ($comment->comment_id && $this->filled('rating')) {
                $validator->errors()->add(
                    'rating',
                    'Нельзя оставлять рейтинг к комментариям',
                );
            }
        });
    }
}
