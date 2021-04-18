<?php

namespace Gerfey\Repository;

use Gerfey\Repository\Contracts\Criteria\CriteriaInterface;
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
     * @var Collection
     */
    private $criterias;

    /**
     * @throws RepositoryException
     * @throws BindingResolutionException
     */
    public function __construct()
    {
        $this->criterias = new Collection();

        $this->model = Container::getInstance()->make($this->entity);

        if (!$this->model instanceof Model) {
            throw new RepositoryException(
                "Class {$this->model} must be an instance of Illuminate\\Database\\Eloquent\\Model"
            );
        }
    }

    /**
     * @param mixed $criteria
     *
     * @return Repository
     *
     * @throws BindingResolutionException
     */
    public function addCriteria($criteria): self
    {
        if (!$criteria instanceof CriteriaInterface) {
            $criteria = Container::getInstance()->make($criteria);
        }

        $this->criterias->push($criteria);

        return $this;
    }

    /**
     * @return $this
     */
    public function removeCriterias(): self
    {
        $this->criterias = [];

        return $this;
    }

    /**
     * @param array $columns
     *
     * @return Collection
     */
    public function all(array $columns = ['*']): Collection
    {
        $this->applyCriterias();

        return $this->model->get($columns);
    }

    /**
     * @param mixed $id
     * @param array $columns
     *
     * @return Model|null
     */
    public function find($id, array $columns = ['*']): ?Model
    {
        $this->applyCriterias();

        return $this->model->find($id, $columns);
    }

    /**
     * @param mixed $id
     * @param array $columns
     *
     * @return Model
     *
     * @throws ModelNotFoundException
     */
    public function findOrFail($id, array $columns = ['*']): Model
    {
        $this->applyCriterias();

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
        $this->applyCriterias();

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
        $this->applyCriterias();

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
        $this->applyCriterias();

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
        $this->applyCriterias();

        return $this->model->paginate($perPage, $columns);
    }

    /**
     * @param mixed $ids
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
        $this->model = $this->model->query();

        $this->applyCriterias();

        return $this->model;
    }

    /**
     * @return $this
     */
    protected function applyCriterias(): self
    {
        if ($this->getCriterias()->isNotEmpty()) {
            $this->getCriterias()->each(
                function (CriteriaInterface $criteria) {
                    $this->model = $criteria->apply($this->model);
                }
            );
        }

        return $this;
    }

    /**
     * @return Collection
     */
    protected function getCriterias(): Collection
    {
        return $this->criterias;
    }
}
