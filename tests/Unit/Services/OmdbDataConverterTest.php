<?php

use App\Services\OmdbDataConverter;

beforeEach(function () {
    $this->converter = new OmdbDataConverter;
});

describe('OmdbDataConverter: Базовая конвертация', function () {
    it('успешно конвертирует полный массив данных от OMDB API', function () {
        $omdbData = [
            'Title' => 'Inception',
            'Plot' => 'A thief who steals corporate secrets...',
            'Released' => '16 Jul 2010',
            'Poster' => 'https://example.com',
            'Actors' => 'Leonardo DiCaprio, Joseph Gordon-Levitt, Elliot Page',
            'Director' => 'Christopher Nolan',
            'Genre' => 'Action, Sci-Fi, Thriller',
            'Runtime' => '148 min',
        ];

        $result = $this->converter->convert($omdbData);

        expect($result)->toBe([
            'name' => 'Inception',
            'description' => 'A thief who steals corporate secrets...',
            'released' => 2010,
            'poster_image' => 'https://example.com',
            'starring' => [
                'Leonardo DiCaprio',
                'Joseph Gordon-Levitt',
                'Elliot Page',
            ],
            'directors' => ['Christopher Nolan'],
            'genres' => ['Action', 'Sci-Fi', 'Thriller'],
            'run_time' => 148,
        ]);
    });

    it(
        'возвращает null или пустые массивы, если ключи отсутствуют',
        function () {
            $result = $this->converter->convert([]);

            expect($result)->toBe([
                'name' => null,
                'description' => null,
                'released' => null,
                'poster_image' => null,
                'starring' => [],
                'directors' => [],
                'genres' => [],
                'run_time' => null,
            ]);
        }
    );
});

describe(
    'OmdbDataConverter: Парсинг строк (Actors, Directors, Genres)',
    function () {
        it(
            'игнорирует лишние пробелы и пустые элементы при разделении запятыми',
            function () {
                $omdbData = [
                    'Actors' => '  Leo  ,   Tom , , Brad ',
                ];

                $result = $this->converter->convert($omdbData);

                expect($result['starring'])->toEqual(['Leo', 'Tom', 'Brad']);
            }
        );

        it(
            'возвращает пустой массив, если в OMDB пришло значение N/A',
            function () {
                $omdbData = [
                    'Actors' => 'N/A',
                    'Director' => 'N/A',
                    'Genre' => 'N/A',
                ];

                $result = $this->converter->convert($omdbData);

                expect($result['starring'])->toBeEmpty()
                    ->and($result['directors'])->toBeEmpty()
                    ->and($result['genres'])->toBeEmpty();
            }
        );
    }
);

describe('OmdbDataConverter: Парсинг года выпуска (Released)', function () {
    it('успешно парсит полную валидную дату релиза', function () {
        $omdbData = ['Released' => '24 Feb 2026'];
        expect($this->converter->convert($omdbData)['released'])->toBe(2026);
    });

    it('использует поле Year, если поле Released отсутствует', function () {
        $omdbData = ['Year' => '2019-01-01'];
        expect($this->converter->convert($omdbData)['released'])->toBe(2019);
    });

    it(
        'извлекает четырехзначный год регулярным выражением, если Carbon падает на сложной строке',
        function () {
            $omdbData = ['Released' => 'Фильм вышел в 1998 году'];
            expect($this->converter->convert($omdbData)['released'])->toBe(
                1998
            );
        }
    );

    it(
        'возвращает null, если дата равна N/A или не содержит год вообще',
        function () {
            expect($this->converter->convert(['Released' => 'N/A'])['released'])
                ->toBeNull()
                ->and(
                    $this->converter->convert(['Released' => 'Not Available']
                    )['released']
                )->toBeNull();
        }
    );
});

describe('OmdbDataConverter: Парсинг хронометража (Runtime)', function () {
    it('отсекает текст "min" и оставляет только число', function () {
        $omdbData = ['Runtime' => '120 min'];
        expect($this->converter->convert($omdbData)['run_time'])->toBe(120);
    });

    it('корректно обрабатывает нестандартный формат времени', function () {
        $omdbData = ['Runtime' => '95-min'];
        expect($this->converter->convert($omdbData)['run_time'])->toBe(95);
    });
});
