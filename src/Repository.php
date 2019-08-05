<?php

namespace Gerfey\Repository;

use Gerfey\Repository\Contracts\RepositoryInterface;
use Gerfey\Repository\Exception\RepositoryException;
use Illuminate\Container\Container as App;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Repository
 *
 * @package App\Vendor\Repository
 */
abstract class Repository implements RepositoryInterface
{
    private $app;

    protected $entity;

    protected $model;

    /**
     * Repository constructor.
     * @param App $app
     * @throws BindingResolutionException
     * @throws RepositoryException
     */
    public function __construct(App $app)
    {
        $this->app = $app;
        $this->setModel();
    }

    /**
     * @param array $columns
     * @return mixed
     */
    public function all($columns = ['*'])
    {
        return $this->model->get($columns);
    }

    /**
     * @param $id
     * @param array $columns
     * @return mixed
     */
    public function find($id, $columns = ['*'])
    {
        return $this->model->find($id, $columns);
    }

    /**
     * @param string $attribute
     * @param $value
     * @param array $columns
     * @return mixed
     */
    public function findBy($attribute, $value, $columns = ['*'])
    {
        return $this->model->where($attribute, '=', $value)->first($columns);
    }

    /**
     * @param string $attribute
     * @param $value
     * @param array $columns
     * @return mixed
     */
    public function findAllBy($attribute, $value, $columns = ['*'])
    {
        return $this->model->where($attribute, '=', $value)->get($columns);
    }

    /**
     * @param array $data
     * @return mixed
     */
    public function create(array $data)
    {
        return $this->model->create($data);
    }

    /**
     * @param array $data
     * @param $id
     * @param string $attribute
     * @return mixed
     */
    public function update(array $data, $id, $attribute = "id")
    {
        return $this->model->where($attribute, '=', $id)->update($data);
    }

    /**
     * @param array $data
     * @return mixed
     */
    public function save(array $data)
    {
        foreach ($data as $k => $v) {
            $this->model->$k = $v;
        }
        return $this->model->save();
    }

    /**
     * @param int $perPage
     * @param array $columns
     * @return mixed
     */
    public function paginate($perPage = 20, $columns = ['*'])
    {
        return $this->model->paginate($perPage, $columns);
    }

    /**
     * @param $id
     * @return mixed
     */
    public function delete($id)
    {
        return $this->model->destroy($id);
    }

    /**
     * @return Builder
     */
    public function createQueryBuilder(): Builder
    {
        return $this->model->query();
    }

    /**
     * @throws BindingResolutionException
     * @throws RepositoryException
     */
    private function setModel()
    {
        $this->model = $this->app->make($this->entity);
        if (!$this->model instanceof Model)
            throw new RepositoryException("Class {$this->model} must be an instance of Illuminate\\Database\\Eloquent\\Model");
    }
}
