<?php

namespace App\Jobs\Recommendations;

use App\Jobs\Job;
use App\Services\Gremlin\Generic;
use App\Services\Gremlin\ProductRecommendation\Processors\WhoViewAlsoViewProcessor;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class WhoViewAlsoView extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    /**
     * @var int
     */
    public $tries = 3;

    /**
     * @var int
     */
    protected $idProduct;

    /**
     * TaskProcess constructor.
     * @param $idProduct
     */
    public function __construct($idProduct)
    {
        $this->idProduct = $idProduct;
    }

    /**
     * @return bool
     */
    public function handle()
    {
        $gremlinConnection = new Generic();
        $processor = new WhoViewAlsoViewProcessor($gremlinConnection);
        $processor->getRecommendationsForProduct([$this->idProduct]);
    }
}
