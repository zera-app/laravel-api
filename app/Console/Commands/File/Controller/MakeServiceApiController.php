<?php

namespace App\Console\Commands\File\Controller;

use App\Supports\Str;
use Illuminate\Console\Command;
use Illuminate\Console\GeneratorCommand;
use InvalidArgumentException;
use Symfony\Component\Console\Input\InputOption;

use function Laravel\Prompts\confirm;

class MakeServiceApiController extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'app:make-service-api-controller';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create service api controller for the specified model.';

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
        $controllerNamespace = $this->getNamespace($name);

        $replace = $this->buildModelReplacements([]);

        $replace["use {$controllerNamespace}\Controller;\n"] = '';

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
        $route = Str::replace('\\', '.', $this->getNameInput());
        $route = Str::replace('/', '.', $route);
        $route = Str::replace('Controller', '', $route);

        $route = implode('.', collect(explode('.', $route))->map(function ($route) {
            return Str::lower(Str::kebab($route));
        })->toArray());

        try {
            $this->call('app:make-service-controller-test', [
                'name' => Str::replace('Api', '', Str::replace('Controller', '', $this->getNameInput())) . 'Test',
                '--model' => $this->option('model'),
                '--route' => $route,
            ]);
        } catch (\Throwable $th) {
            $this->error('Failed to make the Service Controller Test');
            $this->error($th->getMessage());
            dd('here');
        }

        $modelClass = $this->parseModel($this->option('model'));

        if (!class_exists($modelClass) && confirm("A {$modelClass} model does not exist. Do you want to generate it?", default: true)) {
            $this->call('make:model', ['name' => $modelClass]);
        }

        $replace = $this->buildFormRequestReplacements($replace, $modelClass);

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
        ]);
    }

    /**
     * Build the model replacement values.
     *
     * @param  array  $replace
     * @param  string  $modelClass
     * @return array
     */
    protected function buildFormRequestReplacements(array $replace, $modelClass)
    {
        [$namespace, $storeRequestClass, $updateRequestClass] = [
            'Illuminate\\Http', 'Request', 'Request',
        ];

        $namespace = 'App\\Http\\Requests';
        [$storeRequestClass, $updateRequestClass] = $this->generateFormRequests(
            $modelClass,
            $storeRequestClass,
            $updateRequestClass
        );

        if (Str::startsWith('\\', $storeRequestClass)) {
            $storeRequestClass =  '\\' . $storeRequestClass;
        }

        if (Str::startsWith('\\', $updateRequestClass)) {
            $updateRequestClass =  '\\' . $updateRequestClass;
        }

        $namespacedRequests = $namespace . $storeRequestClass . ';';

        if ($storeRequestClass !== $updateRequestClass) {
            $namespacedRequests .= PHP_EOL . 'use ' . $namespace . $updateRequestClass . ';';
        }

        $controllerName = $this->getNameInput();
        $controllerName = str_replace('Controller', '', $controllerName);
        $controllerName = str_replace('Api', '', $controllerName);
        $controllerName = str_replace('Web', '', $controllerName);

        $explodedStoreRequestClass = explode('\\', $storeRequestClass);
        $explodedUpdateRequestClass = explode('\\', $updateRequestClass);

        return array_merge($replace, [
            '{{ storeRequest }}' => $explodedStoreRequestClass[count($explodedStoreRequestClass) - 1],
            '{{storeRequest}}' => $explodedStoreRequestClass[count($explodedStoreRequestClass) - 1],
            '{{ updateRequest }}' => $explodedUpdateRequestClass[count($explodedUpdateRequestClass) - 1],
            '{{updateRequest}}' => $explodedUpdateRequestClass[count($explodedUpdateRequestClass) - 1],
            '{{ namespacedStoreRequest }}' => $namespace . $storeRequestClass,
            '{{namespacedStoreRequest}}' => $namespace . $storeRequestClass,
            '{{ namespacedUpdateRequest }}' => $namespace . $updateRequestClass,
            '{{namespacedUpdateRequest}}' => $namespace . $updateRequestClass,
            '{{ namespacedRequests }}' => $namespacedRequests,
            '{{namespacedRequests}}' => $namespacedRequests,
        ]);
    }

    /**
     * Generate the form requests for the given model and classes.
     *
     * @param  string  $modelClass
     * @param  string  $storeRequestClass
     * @param  string  $updateRequestClass
     * @return array
     */
    protected function generateFormRequests($modelClass, $storeRequestClass, $updateRequestClass)
    {
        $nameInput = $this->getNameInput();
        $controllerNameSpace = str_replace('Controller', '', $nameInput);
        $controllerNameSpace = str_replace('Api', '', $controllerNameSpace);
        $controllerNameSpace = str_replace('Web', '', $controllerNameSpace);

        $controllerName = explode('\\', $controllerNameSpace);
        $controllerName = end($controllerName);
        $controllerName = Str::replace('Controller', '', $controllerName);
        $controllerName = Str::replace('controller', '', $controllerName);

        $storeRequestClass = $controllerNameSpace . '\Store' . class_basename($controllerName) . 'Request';

        $this->call('make:request', [
            'name' => $storeRequestClass,
        ]);

        $updateRequestClass = $controllerNameSpace . '\Update' . class_basename($controllerName) . 'Request';

        $this->call('make:request', [
            'name' => $updateRequestClass,
        ]);

        return [$storeRequestClass, $updateRequestClass];
    }

    /**
     * Build the service replacement values.
     *
     * @param  array  $replace
     * @return array
     * @param string $modelClass
     * @return array
     *
     * @throws \LogicException
     */
    protected function buildServiceReplacements(array $replace, $modelClass)
    {
        $serviceNamespace = 'App\\Interfaces\\Service\\';
        $serviceClass = $modelClass . 'Service';

        if (
            !class_exists($serviceNamespace . '\\' . $serviceClass)
            && confirm("A {$serviceClass} service does not exist. Do you want to generate it?", default: true)
        ) {

            $this->call('app:make-crud-service-interface-command', [
                'name' => $this->option('model') . 'ServiceInterface',
                '--model' => $this->option('model'),
            ]);

            $this->call('app:make-service-crud', [
                'name' => $this->option('model') . 'Service',
                '--model' => $this->option('model'),
            ]);

            $serviceNamespace = $serviceNamespace;
        }

        return array_merge($replace, [
            '{{ ServiceClass }}' => $serviceClass,
            '{{ServiceClass}}' => $serviceClass,
            '{{ ServiceNamespace }}' => $serviceNamespace . '\\' . $serviceClass,
            '{{ServiceNamespace}}' => $serviceNamespace . '\\' . $serviceClass,
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
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace . '\Http\Controllers';
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return base_path('stubs/controller/controller-service-api.stub');
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
        ];
    }
}
