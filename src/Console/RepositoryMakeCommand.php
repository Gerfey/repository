<?php

namespace Gerfey\Repository\Console;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;
use InvalidArgumentException;

class RepositoryMakeCommand extends GeneratorCommand
{
    /**
     * @var string
     */
    protected $signature = 'make:repository {name}';

    /**
     * @var string
     */
    protected $description = 'Creating a new Laravel Repositories class.';

    /**
     * @var string
     */
    protected $type = 'Repository';

    /**
     * @param string $name
     * @return string
     *
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    protected function buildClass($name): string
    {
        $replace = $this->buildModelReplacements();

        return str_replace(
            array_keys($replace),
            array_values($replace),
            parent::buildClass($name)
        );
    }

    /**
     * @param string $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace): string
    {
        return $rootNamespace . '\Repository';
    }

    /**
     * @return string
     */
    protected function getNameInput(): string
    {
        $repositoryName = $this->argument('name') . 'Repository';
        return trim($repositoryName);
    }

    /**
     * @param string $stub
     * @param string $name
     *
     * @return string
     */
    protected function replaceClass($stub, $name): string
    {
        $class = str_replace($this->getNamespace($name) . '\\', '', $name);

        return str_replace(['__RepositoryClassName__'], $class, $stub);
    }

    /**
     * @param string $stub
     * @param string $name
     *
     * @return $this
     */
    protected function replaceNamespace(&$stub, $name): self
    {
        $searches = [['__RepositoryClassNamespace__']];

        foreach ($searches as $search) {
            $stub = str_replace(
                $search,
                [$this->getNamespace($name), $this->rootNamespace(), $this->userProviderModel()],
                $stub
            );
        }

        return $this;
    }

    /**
     * @return string
     */
    protected function getStub(): string
    {
        return $this->resolveStubPath('/../stubs/repository.stub');
    }

    /**
     * @param string $stub
     *
     * @return string
     */
    private function resolveStubPath(string $stub): string
    {
        return file_exists($customPath = $this->laravel->basePath(trim($stub, '/')))
            ? $customPath
            : __DIR__ . '/..' . $stub;
    }

    /**
     * @return array
     */
    private function buildModelReplacements(): array
    {
        $findModelClass = $this->parseModel($this->input->getArgument('name'));

        if (!class_exists($findModelClass)) {
            if ($this->confirm("A {$findModelClass} model does not exist. Do you want to generate it?", true)) {
                $this->call('make:model', ['name' => $findModelClass]);
            }
        }

        return [
            '__ModelClassName__' => class_basename($findModelClass),
            '__ModelClassNamespace__' => $findModelClass
        ];
    }

    /**
     * @param string $model
     * @return string
     *
     * @throws \InvalidArgumentException
     */
    private function parseModel(string $model): string
    {
        if (preg_match('([^A-Za-z0-9_/\\\\])', $model)) {
            throw new InvalidArgumentException('Model name contains invalid characters.');
        }

        $model = trim(str_replace('/', '\\', $model), '\\');

        if (!Str::startsWith($model, $rootNamespace = $this->laravel->getNamespace())) {
            $model = $rootNamespace . $model;
        }

        return $model;
    }
}
