<?php

namespace App\Console\Commands\File\Repository;

use Illuminate\Console\GeneratorCommand;

class MakeRepositoryInterface extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'app:make-repository-interface';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Make a repository interface.';

    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace . '\Interfaces\Repositories';
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return base_path('stubs/repository/repository-interface.stub');
    }
}
