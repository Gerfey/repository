# Laravel Repositories

Laravel Repositories - это пакет для Laravel 7+, который используется для абстрагирования слоя базы данных.

## Установка

Выполнить команду в консоли:


 ```bash
 composer require gerfey/repository
 ```


## Использование

Создаем модель

```php
<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Test extends Model
{
    protected $table = 'test';
}
```

Cоздаем КлассРепозиторий ```Gerfey\Repository\Repository``` и переопределяем свойство entity

```php
<?php namespace App\Repositories;

use Gerfey\Repository\Repository;
use App\Models\Test;

class TestRepository extends Repository {
    protected $entity = Test::class;
}
```

Используем репозиторий в вашем Controller

```php
<?php namespace App\Http\Controllers;

use App\Repositories\TestRepository;

class TestController extends Controller {

    public function index(TestRepository $testRepository) {
        $result = $testRepository->all();
        return \Response::json($result->toArray());
    }
}
```
