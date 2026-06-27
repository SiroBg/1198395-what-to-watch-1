<?php

namespace App\Queries;

use App\Models\Film;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;

final class FetchFilmsQuery
{
    /**
     * Выполняет гибкую фильтрацию и пагинацию фильмов.
     *
     * @param  array  $filters  Данные из Request
     * @param  Builder|Relation|null  $baseQuery  Базовый запрос. Например,
     *                                            связь favoriteFilms.
     */
    public function execute(
        array $filters,
        Builder|Relation|null $baseQuery = null
    ): LengthAwarePaginator {
        $query = $baseQuery ?? Film::query();

        return $query
            ->when(isset($filters['genre']), function ($q) use ($filters) {
                $q->genre($filters['genre']);
            })
            ->when(isset($filters['status']), function ($q) use ($filters) {
                $q->status($filters['status']);
            })
            ->orderBy(
                $filters['order_by'] ?? 'released',
                $filters['order_to'] ?? 'desc',
            )
            ->paginate(8);
    }
}
