<?php

return [

    'required' => 'Поле :attribute обязательно для заполнения.',
    'email' => 'Поле :attribute должно быть корректным email.',
    'unique' => ':attribute уже используется.',

    'min' => [
        'string' => ':attribute должен содержать минимум :min символов.',
    ],

    'max' => [
        'string' => ':attribute не должен превышать :max символов.',
    ],

    'attributes' => [
        'name' => 'Имя',
        'email' => 'E-mail адрес',
        'password' => 'Пароль',
        'file' => 'Аватар',
    ],
];
