<?php

namespace App\Console\Commands\File\Dto;

use Illuminate\Console\GeneratorCommand;
use Symfony\Component\Console\Input\InputOption;

class MakeDto extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'app:make-dto';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Make a Data Transfer Object class';

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
        // make the data transfer object class interface
        try {
            $this->call('app:make-dto-interface', [
                'name' => $this->argument('name') . 'Interface',
            ]);
        } catch (\Throwable $th) {
            $this->error('Failed to make the Data Transfer Object Interface');
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
        return $rootNamespace . '\DataTransferObjects';
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return base_path('stubs/dto/dto.stub');
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['model', null, InputOption::VALUE_REQUIRED, 'Specify the model that the ...'],
        ];
    }
}
