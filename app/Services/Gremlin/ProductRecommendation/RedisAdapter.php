<?php

namespace App\Services\Gremlin\ProductRecommendation;

use App\Services\Gremlin\ProductRecommendationInterface;
use Illuminate\Support\Facades\Cache;

class RedisAdapter implements ProductRecommendationInterface
{

    const CACHE_PREFIX_CATEGORY = 'vav_p_%s_c_%s';

    /**
     * @param $params
     * @return mixed
     */
    public function getWhoViewAlsoView($params)
    {
        return Cache::get(
            sprintf(self::CACHE_PREFIX_CATEGORY, $params['product'], $params['category'])
        );
    }

    /**
     * @param $objRequest
     * @return mixed
     */
    public function getWhoViewBought($objRequest)
    {

    }

    public function getlastView()
    {

    }
}
