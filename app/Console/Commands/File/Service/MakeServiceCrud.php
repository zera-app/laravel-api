<?php

namespace App\Console\Commands\File\Service;

use App\Supports\Str;
use Illuminate\Console\GeneratorCommand;
use InvalidArgumentException;
use Symfony\Component\Console\Input\InputOption;

use function Laravel\Prompts\confirm;

class MakeServiceCrud extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'app:make-service-crud';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Make service crud class.';

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
        // determine if the model is exist
        $modelClass = $this->parseModel($this->option('model'));

        // if the model is not exist and user want to create call artisan make model.
        if (!class_exists($modelClass) && confirm("A {$modelClass} model does not exist. Do you want to generate it?", default: true)) {
            $this->call('make:model', ['name' => $modelClass]);
        }

        // make repository crud.
        try {
            $this->call('app:make-repository-crud', [
                'name' => $this->option('model') . 'Repository',
                '--model' => $this->option('model'),
            ]);
        } catch (\Throwable $th) {
            $this->error('Failed to make the Repository CRUD');
            $this->error($th->getMessage());
        }

        // make service crud interface
        try {
            $this->call('app:make-service-crud-interface', [
                'name' => $this->option('model') . "ServiceInterface",
                '--model' => $this->option('model'),
            ]);
        } catch (\Throwable $th) {
            $this->error('Failed to make the Service CRUD Interface');
            $this->error($th->getMessage());
        }

        // make service crud test
        try {
            $this->call('app:make-service-crud-test', [
                'name' => $this->option('model') . "ServiceTest",
                '--model' => $this->option('model'),
            ]);
        } catch (\Throwable $th) {
            $this->error('Failed to make the Service CRUD Test');
            $this->error($th->getMessage());
        }

        $stub = $this->replaceModel(parent::buildClass($name), $this->option('model'));
        return $stub;
    }

    /**
     * Replace the model for the given stub.
     *
     * @param  string  $stub
     * @param  string  $model
     * @return string
     */
    protected function replaceModel($stub, $model)
    {
        $model = str_replace('/', '\\', $model);

        if (str_starts_with($model, '\\')) {
            $namespacedModel = trim($model, '\\');
        } else {
            $namespacedModel = $this->qualifyModel($model);
        }

        $model = class_basename(trim($model, '\\'));

        $dummyUser = class_basename($this->userProviderModel());

        $dummyModel = Str::camel($model) === 'user' ? 'model' : $model;

        $replace = [
            'NamespacedDummyModel' => $namespacedModel,
            '{{ namespacedModel }}' => $namespacedModel,
            '{{namespacedModel}}' => $namespacedModel,
            'DummyModel' => $model,
            '{{ model }}' => $model,
            '{{model}}' => $model,
            'dummyModel' => Str::camel($dummyModel),
            '{{ modelVariable }}' => Str::camel($dummyModel),
            '{{modelVariable}}' => Str::camel($dummyModel),
            'DummyUser' => $dummyUser,
            '{{ user }}' => $dummyUser,
            '{{user}}' => $dummyUser,
            '$user' => '$' . Str::camel($dummyUser),
        ];

        $stub = str_replace(
            array_keys($replace),
            array_values($replace),
            $stub
        );

        return preg_replace(
            vsprintf('/use %s;[\r\n]+use %s;/', [
                preg_quote($namespacedModel, '/'),
                preg_quote($namespacedModel, '/'),
            ]),
            "use {$namespacedModel};",
            $stub
        );
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace . '\Services';
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return base_path('stubs/service/service-crud.stub');
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
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['model', null, InputOption::VALUE_REQUIRED, 'Specify the model that the own the service.'],
        ];
    }
}
