# Laravel Repositories

Laravel Repositories - это пакет для Laravel 7+, который используется для абстрагирования слоя базы данных.

## Установка
Используем artisan команду

 ```bash
 composer require gerfey/repository
 ```

## Использование
Используем artisan команду
 ```bash
 php artisan make:repository Test
 ```

### Автоматически создается модель

```php
<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Test extends Model
{

}
```

### Автоматически создается КлассРепозиторий

```php
<?php namespace App\Repository;

use Gerfey\Repository\Repository;
use App\Test;

class TestRepository extends Repository {
    protected $entity = Test::class;
}
```

### Создаем критерию
Используем artisan команду
 ```bash
 php artisan make:repository:criteria TestActive
 ```

```php
<?php

namespace App\Criteria;

use Gerfey\Repository\Contracts\Criteria\CriteriaInterface;
use Illuminate\Database\Eloquent\Builder;

class TestActiveCriteria implements CriteriaInterface
{
    public function apply($model): Builder
    {
        return $model->limit(10);
    }
}
```

теперь в любом **Controller** вызываем **TestRepository** и добавляем нашу критерию **TestActiveCriteria**.

```php
<?php namespace App\Http\Controllers;

use App\Repository\TestRepository;
use App\Criteria\TestActiveCriteria;

class TestController extends Controller {

    public function index(TestRepository $testRepository) {    
        $testRepository->addCriteria(new TestActiveCriteria());        
        $testRepository->addCriteria(TestActiveCriteria::class);        
        return \Response::json($testRepository->all());
    }
}
```
