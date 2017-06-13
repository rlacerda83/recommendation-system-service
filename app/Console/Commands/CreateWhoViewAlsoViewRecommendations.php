<?php

namespace App\Console\Commands;

use App\Jobs\Recommendations\WhoViewAlsoView;
use App\Services\Gremlin\Generic;
use App\Services\Gremlin\ProductRecommendation\GremlinAdapter;
use App\Services\Gremlin\ProductRecommendation\Processors\WhoViewAlsoViewProcessor;
use Brightzone\GremlinDriver\ServerException;
use Illuminate\Console\Command;

class CreateWhoViewAlsoViewRecommendations extends Command
{
    const LIMIT = 100;


    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'recommendation:createWhoViewAlsoView {offset=0} {limit=100}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create whoViewAlsoView recommendations';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        set_time_limit(0);
        $this->comment(PHP_EOL. 'Start whoViewAlsoView recommendations' .PHP_EOL);

        $gremlin = new Generic();

        $limit = $this->argument('limit') ? $this->argument('limit') : self::LIMIT;
        $offset = $this->argument('offset') ? $this->argument('offset') : 0;
        $continue = true;

        do {
            try {
                $query = sprintf(GremlinAdapter::QUERY_GET_PRODUCTS, $offset, $limit);
                $result = $gremlin->executeQuery($query);
                $this->comment(PHP_EOL. 'Processing ' . count($result) . ' products' .PHP_EOL);

                if (!count($result)) {
                    $continue = false;
                    continue;
                }

                foreach ($result as $idProduct) {
                    dispatch(new WhoViewAlsoView($idProduct));
                }

                $limit += self::LIMIT;
                $offset += self::LIMIT;
            } catch (ServerException $e) {
                $this->error($e->getMessage() . PHP_EOL);
                $continue = false;
            }

        } while ($continue);
    }
}
