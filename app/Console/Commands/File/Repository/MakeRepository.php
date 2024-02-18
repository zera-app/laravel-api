<?php

namespace App\Console\Commands\File\Repository;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Facades\Artisan;

class MakeRepository extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'app:make-repository';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Make a repository class.';

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
        // make the repository interface
        try {
            $this->call('app:make-repository-interface', [
                'name' => $this->argument('name') . "Interface",
            ]);
        } catch (\Throwable $th) {
            $this->error('Failed to make the Repository Interface');
            $this->error($th->getMessage());
        }

        // make the repository test
        try {
            $this->call('app:make-repository-test', [
                'name' => $this->argument('name') . "Test",
            ]);
        } catch (\Throwable $th) {
            $this->error('Failed to make the Repository Test');
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
        return $rootNamespace . '\Repositories';
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return base_path('stubs/repository/repository.stub');
    }
}
