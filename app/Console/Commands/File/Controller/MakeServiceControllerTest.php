<?php

namespace App\Console\Commands\File\Controller;

use App\Supports\Str;
use Illuminate\Console\GeneratorCommand;
use InvalidArgumentException;
use Symfony\Component\Console\Input\InputOption;

use function Laravel\Prompts\confirm;

class MakeServiceControllerTest extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'app:make-service-controller-test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Make service controller test class.';

    /**
     * Build the class with the given name.
     *
     * @param  string  $name
     * @return string
     *
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    protected function buildClass($name)
    {
        $replace = $this->buildModelReplacements([]);
        $replace = $this->buildRouteReplacements($replace);

        return str_replace(
            array_keys($replace),
            array_values($replace),
            parent::buildClass($name)
        );
    }

    /**
     * Build the model replacement values.
     *
     * @param  array  $replace
     * @return array
     */
    protected function buildModelReplacements(array $replace)
    {
        $modelClass = $this->parseModel($this->option('model'));
        $modelClass = Str::replace('Tests', 'App\\', $modelClass);

        if (!class_exists($modelClass) && confirm("A {$modelClass} model does not exist. Do you want to generate it?", default: true)) {
            $this->call('make:model', ['name' => $modelClass]);
        }

        $tableName = Str::plural(Str::snake(class_basename($modelClass)));

        $nameInput = $this->getNameInput();
        $controllerNameSpace = str_replace('Controller', '', $nameInput);
        $controllerNameSpace = str_replace('Api', '', $controllerNameSpace);
        $controllerNameSpace = str_replace('Web', '', $controllerNameSpace);

        $controllerName = explode('\\', $controllerNameSpace);
        $controllerName = end($controllerName);
        $controllerName = Str::replace('Controller', '', $controllerName);
        $controllerName = Str::replace('controller', '', $controllerName);

        $storeRequestClass = $controllerNameSpace . '\Store' . class_basename($controllerName) . 'Request';
        $updateRequestClass = $controllerNameSpace . '\Update' . class_basename($controllerName) . 'Request';

        return array_merge($replace, [
            'DummyFullModelClass' => $modelClass,
            '{{ namespacedModel }}' => $modelClass,
            '{{namespacedModel}}' => $modelClass,
            'DummyModelClass' => class_basename($modelClass),
            '{{ model }}' => class_basename($modelClass),
            '{{model}}' => class_basename($modelClass),
            'DummyModelVariable' => lcfirst(class_basename($modelClass)),
            '{{ modelVariable }}' => lcfirst(class_basename($modelClass)),
            '{{modelVariable}}' => lcfirst(class_basename($modelClass)),
            'DummyTable' => $tableName,
            '{{ table }}' => $tableName,
            '{{table}}' => $tableName,
            '{{ StoreRequest }}' => $storeRequestClass,
            '{{ storeRequest }}' => $storeRequestClass,
            '{{ StoreRequestClass }}' => $storeRequestClass,
            '{{ storeRequestClass }}' => $storeRequestClass,
            '{{ UpdateRequest }}' => $updateRequestClass,
            '{{ updateRequest }}' => $updateRequestClass,
            '{{ UpdateRequestClass }}' => $updateRequestClass,
            '{{ updateRequestClass }}' => $updateRequestClass,
        ]);
    }

    /**
     * Build the route definition replacement values.
     *
     * @param array $replace
     * @return array
     */
    protected function buildRouteReplacements(array $replace)
    {
        return array_merge($replace, [
            'DummyRoute' => $this->option('route'),
            '{{ route }}' => $this->option('route'),
            '{{route}}' => $this->option('route'),
        ]);
    }

    /**
     * Get the fully-qualified model class name.
     *
     * @param  string  $model
     * @return string
     *
     * @throws \InvalidArgumentException
     */
    protected function parseModel($model)
    {
        if (preg_match('([^A-Za-z0-9_/\\\\])', $model)) {
            throw new InvalidArgumentException('Model name contains invalid characters.');
        }

        return $this->qualifyModel($model);
    }

    /**
     * Get the destination class path.
     *
     * @param  string  $name
     * @return string
     */
    protected function getPath($name)
    {
        $name = Str::replaceFirst($this->rootNamespace(), '', $name);

        return base_path('tests') . str_replace('\\', '/', $name) . '.php';
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace . "\Feature";
    }

    /**
     * Get the root namespace for the class.
     *
     * @return string
     */
    protected function rootNamespace()
    {
        return 'Tests';
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return base_path('stubs/controller/controller-service-test.stub');
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['model', null, InputOption::VALUE_REQUIRED, 'Specify the model that the controller applies to the controller'],
            ['route', null, InputOption::VALUE_REQUIRED, 'Specify the route that the controller applies to the controller']
        ];
    }
}
