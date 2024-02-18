<?php

namespace App\Console\Commands\File\Dto;

use Illuminate\Console\GeneratorCommand;

class MakeDtoInterface extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'app:make-dto-interface';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Make a Data Transfer Object Interface';

    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace . '\Interfaces\DataTransferObjects';
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return base_path('stubs/dto/dto-interface.stub');
    }
}
