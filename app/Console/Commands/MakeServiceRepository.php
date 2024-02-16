<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;

class MakeServiceRepository extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:make-service-repository 
                            {--all : Generate all service and repository}
                            {--repository-interface : Generate the repository interface}
                            {--repository : Generate the repository}
                            {--repository-test : Generate the repository test}
                            {--service-interface : Generate the service interface}
                            {--service : Generate the service}
                            {--service-test : Generate the service test}
                            {--model= : The model field}
    ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Make service and repository';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if ($this->option('all')) {
            $this->input->setOption('repository-interface', true);
            $this->input->setOption('repository', true);
            $this->input->setOption('repository-test', true);

            $this->input->setOption('service-interface', true);
            $this->input->setOption('service', true);
            $this->input->setOption('service-test', true);
        }

        if ($this->option('repository-interface')) {
            $this->call('app:make-crud-repository-interface-command', [
                'name' => $this->option('model') . 'RepositoryInterface',
                '--model' => $this->option('model'),
            ]);
        }

        if ($this->option('repository')) {
            $this->call('app:make-crud-repository-command', [
                'name' => $this->option('model') . 'Repository',
                '--model' => $this->option('model'),
            ]);
        }

        if ($this->option('repository-test')) {
            $this->call('app:make-repository-test-command', [
                'name' => $this->option('model') . 'RepositoryTest',
                '--model' => $this->option('model'),
            ]);
        }

        if ($this->option('service-interface')) {
            $this->call('app:make-crud-service-interface-command', [
                'name' => $this->option('model') . 'ServiceInterface',
                '--model' => $this->option('model'),
            ]);
        }

        if ($this->option('service')) {
            $this->call('app:make-crud-service-command', [
                'name' => $this->option('model') . 'Service',
                '--model' => $this->option('model'),
            ]);
        }

        if ($this->option('service-test')) {
            $this->call('app:make-service-test-command', [
                'name' => $this->option('model') . 'ServiceTest',
                '--model' => $this->option('model'),
            ]);
        }

        $this->info('Service and repository created successfully.');
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions(): array
    {
        return [
            ['all', '-a', InputOption::VALUE_REQUIRED, 'Generate all service and repository'],
            ['repository-interface', "-ri", InputOption::VALUE_OPTIONAL, 'Generate the repository interface'],
            ['repository', "-r", InputOption::VALUE_OPTIONAL, 'Generate the repository'],
            ['repository-test', '-rt', InputOption::VALUE_OPTIONAL, 'Generate the repository test'],
            ['service-interface', '-si', InputOption::VALUE_OPTIONAL, 'Generate the service interface'],
            ['service', '-s', InputOption::VALUE_OPTIONAL, 'Generate the service'],
            ['service-test', '-st', InputOption::VALUE_OPTIONAL, 'Generate the service test'],
            ['model', '--model', InputOption::VALUE_REQUIRED, 'The model field'],
        ];
    }
}
