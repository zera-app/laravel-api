<?php

namespace App\Repositories\Base;

use App\Interfaces\Base\BaseRepositoryInterface;
use App\Support\Str;
use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\AllowedFilter;

class BaseRepository implements BaseRepositoryInterface
{
    /**
     * Allowable Fields That Can be Filtered
     *
     * @var array
     */
    private array $allowableFilters = [];

    /**
     * Allowable relations that can be included
     *
     * @var array
     */
    private array $allowableIncludes = [];

    /**
     * Allowable Fields That Can be Sorted
     *
     * @var array
     */
    private array $allowableSorts = [];

    /**
     * Allowable Fields That can be searched
     *
     * @var array
     */
    private array $allowableSearch = [];

    /**
     * Default Sort Field
     *
     * @var string
     */
    private string $defaultSort = '-created_at';

    /**
     * Set allowable filter.
     *
     * @param array<string> $allowableFilter
     * @return void
     */
    public function setAllowableFilter(array $allowableFilter): void
    {
        $this->allowableFilters = $allowableFilter;
    }

    /**
     * Set allowable sort.
     *
     * @param array<string> $allowableIncludes
     * @return void
     */
    public function setAllowableInclude(array $allowableIncludes): void
    {
        $this->allowableIncludes = $allowableIncludes;
    }

    /**
     * Set allowable search.
     *
     * @param array<string> $allowableSearch
     * @return void
     */
    public function setAllowableSearch(array $allowableSearch): void
    {
        $this->allowableSearch = $allowableSearch;
    }

    /**
     * Set allowable search.
     *
     * @param array<string> $allowableSort
     * @return void
     */
    public function setAllowableSort(array $allowableSort): void
    {
        $this->allowableSorts = $allowableSort;
    }

    /**
     * Set default sort.
     *
     * @param string $DefaultSort
     * @return void
     */
    public function setDefaultSort(string $DefaultSort): void
    {
        $this->defaultSort = $DefaultSort;
    }

    /**
     * Get default sort
     *
     * @return string
     */
    public function getDefaultSort(): string
    {
        return $this->defaultSort;
    }

    /**
     * Get allowable filter.
     *
     * @return array
     */
    public function getAllowableFilter(): array
    {
        return $this->allowableFilters;
    }

    /**
     * Get allowable include.
     *
     * @return array
     */
    public function getAllowableInclude(): array
    {
        return $this->allowableIncludes;
    }

    /**
     * Get allowable sort.
     *
     * @return array
     */
    public function getAllowableSort(): array
    {
        return $this->allowableSorts;
    }

    /**
     * Get allowable search.
     *
     * @return array
     */
    public function getAllowableSearch(): array
    {
        return $this->allowableSearch;
    }

    /**
     * Get allowable search query
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string|null $value
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function getAllowableSearchQuery(Builder $query, ?string $value): Builder
    {
        return $query->where(function (Builder $query) use ($value) {
            collect($this->getAllowableSearch())->each(function (string $filter) use ($query, $value) {
                [$lastIndex, $concatenated, $filterParts] = $this->getFilterParts($filter);

                if (count($filterParts) > 1) {
                    $this->orWhereHasConcatenated($query, $concatenated, $lastIndex, $value);
                } else {
                    $this->orWhere($query, $filter, $value);
                }
            });
        });
    }

    /**
     * Get allowable filter query
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param array $filter
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function getAllowableFilterQuery(Builder $query, array $filter): Builder
    {
        return $query->where(function (Builder $query) use ($filter) {
            collect($filter)->each(function ($value, $filter) use ($query) {
                [$lastIndex, $concatenated, $filterParts] = $this->getFilterParts($filter);

                // if the value is null, skip the filter
                if ($value === null) {
                    return;
                }

                // of check the key is in allowable filter
                if (!in_array($filter, $this->getAllowableFilter())) {
                    return;
                }

                if (count($filterParts) > 1) {
                    $query->orWhereHas($concatenated, function (Builder $query) use ($lastIndex, $value) {
                        $query->where($lastIndex, "%{$value}%");
                    });
                } else {
                    $query->where($filter, $value);
                }
            });
        });
    }

    /**
     * Get the filter parts from a given filter string.
     *
     * @param string $filter The filter string to parse.
     *
     * @return array An array containing the last index, concatenated string, and all filter parts.
     */
    private function getFilterParts(string $filter): array
    {
        $filterParts = explode('.', $filter);
        $lastIndex = end($filterParts);
        $otherIndices = array_slice($filterParts, 0, -1);
        $concatenated = implode('.', $otherIndices);

        return [$lastIndex, $concatenated, $filterParts];
    }

    /**
     * Add an orWhereHas clause to the given query for a concatenated relationship.
     *
     * @param Builder $query The query builder instance.
     * @param string $concatenated The concatenated relationship string.
     * @param string $lastIndex The last index of the relationship.
     * @param string|null $value The value to search for.
     *
     * @return void
     */
    private function orWhereHasConcatenated(Builder $query, string $concatenated, string $lastIndex, ?string $value): void
    {
        $query->orWhereHas($concatenated, function (Builder $query) use ($lastIndex, $value) {
            $query->where($lastIndex, 'ilike', "%{$value}%");
        });
    }

    /**
     * Add an orWhere clause to the given query for a given filter and value.
     *
     * @param Builder $query The query builder instance.
     * @param string $filter The filter string to search for.
     * @param string|null $value The value to search for.
     *
     * @return void
     */
    private function orWhere(Builder $query, string $filter, ?string $value): void
    {
        $query->orWhere($filter, 'ilike', "%{$value}%");
    }

    /**
     * Get sort column
     */
    public function getSortColumn(): string
    {
        // sort
        $sort = request()->order ?? $this->getDefaultSort();

        // remove '-' from sort
        $sort = Str::replaceFirst('-', '', $sort);

        // check if sort is allowable
        if (!in_array($sort, $this->getAllowableSort())) {
            return Str::replace('-', '', $this->getDefaultSort());
        }

        return $sort;
    }

    /**
     * Get sort direction
     */
    public function getSortDirection(): string
    {
        return Str::contains(request()->order ?? $this->getDefaultSort(), '-') ? 'desc' : 'asc';
    }

    // /**
    //  * Get set allowable search with global search.
    //  *
    //  * @return array
    //  */
    // public function getAllowableSearchWithGlobalSearch(): array
    // {
    //     $globalSearch = AllowedFilter::callback('global', function ($q, $value) {
    //         $q->where(function ($query) use ($value) {
    //             collect($this->getAllowableSearch())->each(function ($filter) use ($query, $value) {

    //                 $explode = explode('.', $filter);
    //                 $lastIndex = end($explode);
    //                 $otherIndices = array_slice($explode, 0, -1);
    //                 $concatenated = implode('.', $otherIndices);

    //                 if (count($explode) > 1) {
    //                     $query->orWhereHas($concatenated, function ($query) use ($lastIndex, $value) {
    //                         $query->where($lastIndex, 'like', "%{$value}%");
    //                     });
    //                 } else {
    //                     $query->orWhere($filter, 'like', "%{$value}%");
    //                 }
    //             });
    //         });
    //     });

    //     $allowableFilter = $this->getAllowableSearch();
    //     $allowableFilter[] = $globalSearch;

    //     return $allowableFilter;
    // }
}
