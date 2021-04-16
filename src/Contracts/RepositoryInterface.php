<?php

namespace Gerfey\Repository\Contracts;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Pagination\LengthAwarePaginator;

interface RepositoryInterface
{
    /**
     * @param array $columns
     *
     * @return Collection
     */
    public function all(array $columns = ['*']): Collection;

    /**
     * @param string $id
     * @param array $columns
     *
     * @return Model|null
     */
    public function find(string $id, array $columns = ['*']): ?Model;

    /**
     * @param string $id
     * @param array $columns
     *
     * @return Model
     *
     * @throws ModelNotFoundException
     */
    public function findOrFail(string $id, array $columns = ['*']): Model;

    /**
     * @param array $ids
     * @param array $columns
     *
     * @return Collection
     */
    public function findMany(array $ids, array $columns = ['*']): Collection;

    /**
     * @param string $attribute
     * @param mixed $value
     * @param array $columns
     *
     * @return Model|null
     */
    public function findBy(string $attribute, $value, array $columns = ['*']): ?Model;

    /**
     * @param string $attribute
     * @param mixed $value
     * @param array $columns
     *
     * @return Collection
     */
    public function findAllBy(string $attribute, $value, $columns = ['*']): Collection;

    /**
     * @param array $data
     *
     * @return Model
     */
    public function create(array $data): Model;

    /**
     * @param array $data
     *
     * @return bool
     */
    public function save(array $data): bool;

    /**
     * @param array $attributes
     * @param array $options
     *
     * @return bool
     */
    public function update(array $attributes = [], array $options = []): bool;

    /**
     * @param array $data
     * @param $value
     * @param string $attribute
     *
     * @return bool
     */
    public function updateBy(array $data, $value, string $attribute = 'id'): bool;

    /**
     * @param $id
     *
     * @return int
     */
    public function delete($id): int;

    /**
     * @param int $perPage
     * @param array $columns
     *
     * @return mixed
     */
    public function paginate(int $perPage = 1, array $columns = ['*']): LengthAwarePaginator;

    /**
     * @return Builder
     */
    public function createQueryBuilder(): Builder;
}
