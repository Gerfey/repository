# Laravel Repositories

Laravel Repositories - это пакет для Laravel 7+, который используется для абстрагирования слоя базы данных.

## Установка

Выполнить команду в консоли:


 ```bash
 composer require gerfey/repository
 ```


## Использование

Начинаем работу с ввода artisan-команды:
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

### Добавляем метод для выборки всех неактивных сущностей
```php
<?php

namespace App\Repository;

use App\Test;
use Gerfey\Repository\Repository;
use Illuminate\Database\Eloquent\Collection;

class TestRepository extends Repository
{
    /**
     * @var string
     */
    protected $entity = Test::class;

    /**
     * @return Collection
     */
    public function getAllByNoActive(): Collection
    {
        return $this->createQueryBuilder()
            ->where('active', '=', false)
            ->get();
    }
}


```

теперь в любом **Controller** вызываем **TestRepository** и вызываем ранее созданный метод.

```php
<?php namespace App\Http\Controllers;

use App\Repository\TestRepository;

class TestController extends Controller {

    public function index(TestRepository $testRepository) {
        $result = $testRepository->getAllByNoActive();
        return \Response::json($result->toArray());
    }
}
```
