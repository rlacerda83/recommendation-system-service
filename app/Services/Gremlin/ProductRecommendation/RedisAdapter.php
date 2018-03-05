<?php

namespace App\Services\Gremlin\ProductRecommendation;

use App\Services\Gremlin\ProductRecommendationInterface;
use Illuminate\Support\Facades\Cache;

class RedisAdapter implements ProductRecommendationInterface
{

    const CACHE_PREFIX_CATEGORY = 'rec_wvav_%s';

    /**
     * @param $params
     * @return mixed
     */
    public function getWhoViewAlsoView($params)
    {
        $key = $this->getKey($params);

        return Cache::get($key);
    }

    /**
     * @param $params
     * @return string
     */
    public function getKey($params)
    {
        $key = sprintf(self::CACHE_PREFIX_CATEGORY, $params['product']);

        if (!empty($params['category'])) {
            $key .= '_' . $params['category'];
        }

        return $key;
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
