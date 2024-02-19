<?php

namespace App\Console\Commands\File\Service;

use Illuminate\Console\Command;
use Illuminate\Console\GeneratorCommand;

class MakeService extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'app:make-service';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a service class';

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
        // make the service interface
        try {
            $this->call('app:make-service-interface', [
                'name' => $this->argument('name') . "Interface",
            ]);
        } catch (\Throwable $th) {
            $this->error('Failed to make the Service Interface');
            $this->error($th->getMessage());
        }

        // make the service test
        try {
            $this->call('app:make-service-test', [
                'name' => $this->argument('name') . "Test",
            ]);
        } catch (\Throwable $th) {
            $this->error('Failed to make the Service Test');
            $this->error($th->getMessage());
        }

        return parent::buildClass($name);
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
        return base_path('stubs/service/service.stub');
    }
}
