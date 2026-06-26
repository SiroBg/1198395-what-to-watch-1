<?php

namespace App\Http\Requests;

use App\Models\Comment;
use Illuminate\Foundation\Http\FormRequest;

class UpdateCommentRequest extends FormRequest
{
    public function rules()
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
            ],
        ];
    }

    public function withValidator($validator)
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
