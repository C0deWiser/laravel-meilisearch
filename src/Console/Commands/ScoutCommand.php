<?php

namespace Codewiser\Meilisearch\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class ScoutCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'meilisearch:rebuild';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync index settings and re-import all models';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // https://laravel.com/docs/10.x/scout#modifying-the-import-query
        // The makeAllSearchableUsing method may not be applicable when using a queue to batch import models.
        // Relationships are not restored when model collections are processed by jobs.
        config()->set('scout.queue', false);

        $this->call('scout:delete-all-indexes');
        $this->call('scout:sync-index-settings');

        $searchable = config('scout.meilisearch.searchable', []);

        foreach ($searchable as $class) {
            $this->call('scout:import', ['model' => $class]);
        }
    }
}
