<?php

namespace Gerfey\Repository\Console;

use Illuminate\Console\GeneratorCommand;

class RepositoryCriteriaMakeCommand extends GeneratorCommand
{
    /**
     * @var string
     */
    protected $signature = 'make:repository:criteria {name}';

    /**
     * @var string
     */
    protected $description = 'Creating a new Criteria Repositories class.';

    /**
     * @param string $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace): string
    {
        return $rootNamespace . '\Criteria';
    }

    /**
     * @return string
     */
    protected function getNameInput(): string
    {
        $repositoryName = $this->argument('name') . 'Criteria';
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

        return str_replace(['__CriteriaClassName__'], $class, $stub);
    }

    /**
     * @param string $stub
     * @param string $name
     *
     * @return $this
     */
    protected function replaceNamespace(&$stub, $name): self
    {
        $searches = [['__CriteriaClassNamespace__']];

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
        return $this->resolveStubPath('/../stubs/criteria.stub');
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
}
