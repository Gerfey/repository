<?php

namespace Gerfey\Repository;

use Gerfey\Repository\Contracts\RepositoryInterface;
use Gerfey\Repository\Exception\RepositoryException;
use Illuminate\Container\Container;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Pagination\LengthAwarePaginator;

abstract class Repository implements RepositoryInterface
{
    /**
     * @var string
     */
    protected $entity;

    /**
     * @var Model
     */
    private $model;

    /**
     * @throws RepositoryException
     * @throws BindingResolutionException
     */
    public function __construct()
    {
        $this->model = Container::getInstance()->make($this->entity);

        if (!$this->model instanceof Model) {
            throw new RepositoryException(
                "Class {$this->model} must be an instance of Illuminate\\Database\\Eloquent\\Model"
            );
        }
    }

    /**
     * @param array $columns
     *
     * @return Collection
     */
    public function all(array $columns = ['*']): Collection
    {
        return $this->model->get($columns);
    }

    /**
     * @param string $id
     * @param array $columns
     *
     * @return Model|null
     */
    public function find(string $id, array $columns = ['*']): ?Model
    {
        return $this->model->find($id, $columns);
    }

    /**
     * @param string $id
     * @param array $columns
     *
     * @return Model
     *
     * @throws ModelNotFoundException
     */
    public function findOrFail(string $id, array $columns = ['*']): Model
    {
        return $this->model->findOrFail($id, $columns);
    }

    /**
     * @param array $ids
     * @param array $columns
     *
     * @return Collection
     */
    public function findMany(array $ids, array $columns = ['*']): Collection
    {
        return $this->model->findMany($ids, $columns);
    }

    /**
     * @param string $attribute
     * @param mixed $value
     * @param array $columns
     *
     * @return Model|null
     */
    public function findBy(string $attribute, $value, array $columns = ['*']): ?Model
    {
        return $this->model->where($attribute, '=', $value)->first($columns);
    }

    /**
     * @param string $attribute
     * @param mixed $value
     * @param array $columns
     *
     * @return Collection
     */
    public function findAllBy(string $attribute, $value, $columns = ['*']): Collection
    {
        return $this->model->where($attribute, '=', $value)->get($columns);
    }

    /**
     * @param array $data
     *
     * @return Model
     */
    public function create(array $data): Model
    {
        return $this->model->create($data);
    }

    /**
     * @param array $attributes
     * @param array $options
     *
     * @return bool
     */
    public function update(array $attributes = [], array $options = []): bool
    {
        return $this->model->fill($attributes)->save($options);
    }

    /**
     * @param array $data
     * @param mixed $value
     * @param string $attribute
     *
     * @return bool
     */
    public function updateBy(array $data, $value, string $attribute = 'id'): bool
    {
        return $this->model->where($attribute, '=', $value)->update($data);
    }

    /**
     * @param array $data
     *
     * @return bool
     */
    public function save(array $data): bool
    {
        foreach ($data as $k => $v) {
            $this->model->$k = $v;
        }
        return $this->model->save();
    }

    /**
     * @param int $perPage
     * @param array $columns
     *
     * @return LengthAwarePaginator
     */
    public function paginate(int $perPage = 20, array $columns = ['*']): LengthAwarePaginator
    {
        return $this->model->paginate($perPage, $columns);
    }

    /**
     * @param \Illuminate\Support\Collection|array|int|string $ids
     *
     * @return int
     */
    public function delete($ids): int
    {
        return $this->model->destroy($ids);
    }

    /**
     * @return Builder
     */
    public function createQueryBuilder(): Builder
    {
        return $this->model->query();
    }
}
