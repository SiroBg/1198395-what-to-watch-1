<?php

return [

    'required'          => 'Поле :attribute обязательно для заполнения.',
    'email'             => 'Поле :attribute должно быть корректным email.',
    'unique'            => 'Значение поля :attribute уже используется.',
    'confirmed'         => 'Поле :attribute не совпадает с полем подтверждения.',
    'string'            => 'Поле :attribute должно быть строкой.',
    'image'             => 'Поле :attribute должно содержать валидное изображение.',
    'integer'           => 'Поле :attribute должно быть числом.',
    'in'                => 'Выбранное значение для поля :attribute недействительно. Доступные варианты: :values.',
    'enum'              => 'Выбранное значение для поля :attribute недействительно.',
    'regex'             => 'Поле :attribute имеет неверный формат.',
    'array'             => 'Поле :attribute должно быть массивом',
    'required_without'  => 'Поле :attribute обязательно, когда отсутствует :values.',
    'prohibited_unless' => 'Поле :attribute запрещено, пока :other не имеет одно из значений: :values.',

    'min' => [
        'string' => ':attribute должен содержать минимум :min символов.',
    ],

    'max' => [
        'string' => ':attribute не должен превышать :max символов.',
    ],

    'attributes' => [
        'name'     => 'Имя',
        'email'    => 'E-mail адрес',
        'password' => 'Пароль',
        'file'     => 'Аватар',
    ],
];
