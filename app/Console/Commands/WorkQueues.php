<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

/**
 * Class WorkQueues
 *
 * Starts the application queue worker while providing supervisor.d production configurations.
 */
class WorkQueues extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rpms:work-queues
                            {--queue=notifications,default : The names of the queues to work}
                            {--once : Only process the next job on the queue}
                            {--tries=3 : The number of times to attempt a job before logging it failed}
                            {--timeout=120 : The number of seconds a child process can run}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Start the RPMS background queue worker and display supervisor configuration snippets';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $queue   = $this->option('queue');
        $tries   = (int) $this->option('tries');
        $timeout = (int) $this->option('timeout');

        $this->info('===============================================================');
        $this->info('   GAMBELLA UNIVERSITY RPMS — QUEUE WORKER SUBSYSTEM           ');
        $this->info('===============================================================');
        $this->line('<fg=cyan>Queues listening :</> ' . $queue);
        $this->line('<fg=cyan>Max retries      :</> ' . $tries);
        $this->line('<fg=cyan>Process timeout  :</> ' . $timeout . 's');
        $this->newLine();

        $this->comment('Supervisor Production Configuration Template (/etc/supervisor/conf.d/rpms-worker.conf):');
        $this->line('---------------------------------------------------------------');
        $this->line('[program:rpms-worker]');
        $this->line('process_name=%(program_name)s_%(process_num)02d');
        $this->line('command=php ' . base_path('artisan') . " queue:work --queue={$queue} --sleep=3 --tries={$tries} --max-time=3600");
        $this->line('autostart=true');
        $this->line('autorestart=true');
        $this->line('stopasgroup=true');
        $this->line('killasgroup=true');
        $this->line('user=www-data');
        $this->line('numprocs=2');
        $this->line('redirect_stderr=true');
        $this->line('stdout_logfile=' . storage_path('logs/rpms-worker.log'));
        $this->line('---------------------------------------------------------------');
        $this->newLine();

        if ($this->option('once')) {
            $this->info('Processing single queued job...');
            return $this->call('queue:work', [
                '--queue'   => $queue,
                '--once'    => true,
                '--tries'   => $tries,
                '--timeout' => $timeout,
            ]);
        }

        $this->info('Starting interactive queue worker loop (Press Ctrl+C to stop)...');

        return $this->call('queue:work', [
            '--queue'   => $queue,
            '--tries'   => $tries,
            '--timeout' => $timeout,
        ]);
    }
}
