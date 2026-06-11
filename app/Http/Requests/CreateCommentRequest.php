<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateCommentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
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

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if ($this->comment_id) {
                $comment = \App\Models\Comment::find($this->comment_id);

                if (!$comment) {
                    $validator->errors()->add('comment_id', 'Комментарий не найден.');
                    return;
                }

                if ($comment->film_id != $this->route('film')->id) {
                    $validator->errors()->add(
                        'comment_id',
                        'Комментарий принадлежит другому фильму.',
                    );
                }
            }

        });
    }
}
