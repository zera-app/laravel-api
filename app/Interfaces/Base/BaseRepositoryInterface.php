<?php

namespace App\Interfaces\Base;

use Illuminate\Database\Eloquent\Builder;

interface BaseRepositoryInterface
{
    /**
     * Set allowable filter.
     *
     * @param array<string> $allowableFilter
     * @return void
     */
    public function setAllowableFilter(array $allowableFilter): void;

    /**
     * Set allowable sort.
     *
     * @param array<string> $allowableInclude
     * @return void
     */
    public function setAllowableInclude(array $allowableInclude): void;

    /**
     * Set allowable search.
     *
     * @param array<string> $allowableSearch
     * @return void
     */
    public function setAllowableSearch(array $allowableSearch): void;

    /**
     * Set allowable sort.
     *
     * @param array<string> $allowableSort
     * @return void
     */
    public function setAllowableSort(array $allowableSort): void;

    /**
     * Set default sort.
     *
     * @param string $DefaultSort
     * @return void
     */
    public function setDefaultSort(string $DefaultSort): void;

    /**
     * Get default sort
     *
     * @return string
     */
    public function getDefaultSort(): string;

    /**
     * Get allowable filter.
     *
     * @return array
     */
    public function getAllowableFilter(): array;

    /**
     * Get allowable include.
     *
     * @return array
     */
    public function getAllowableInclude(): array;

    /**
     * Get allowable sort.
     *
     * @return array
     */
    public function getAllowableSort(): array;

    /**
     * Get allowable search.
     *
     * @return array
     */
    public function getAllowableSearch(): array;

    /**
     * Get allowable search query
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string|null $value
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function getAllowableSearchQuery(Builder $query, string|null $value): Builder;

    // /**
    //  * Get set allowable search with global search.
    //  *
    //  * @return array
    //  */
    // public function getAllowableSearchWithGlobalSearch(): array;
}
