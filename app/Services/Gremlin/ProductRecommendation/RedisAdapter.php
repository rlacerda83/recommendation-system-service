<?php

namespace App\Services\Gremlin\ProductRecommendation;

use App\Services\Gremlin\ProductRecommendationInterface;
use Illuminate\Support\Facades\Cache;

class RedisAdapter implements ProductRecommendationInterface
{

    const CACHE_PREFIX_WHO_VIEW_ALSO_VIEW = 'rec_recommendation_wvav_%s';

    const CACHE_PREFIX_WHO_VIEW_BOUGHT = 'rec_recommendation_wvb_%s';

    const CACHE_PREFIX_TOP_SELLER_CATEGORY = 'rec_recommendation_tsc_%s';

    /**
     * @param $params
     * @return mixed
     */
    public function getWhoViewAlsoView($params)
    {
        $key = $this->getKey(self::CACHE_PREFIX_WHO_VIEW_ALSO_VIEW, $params);

        return Cache::get($key);
    }

    /**
     * @param $prefix
     * @param $params
     * @return string
     */
    public function getKey($prefix, $params)
    {
        $key = sprintf($prefix, $params['product']);

        if (!empty($params['category'])) {
            $key .= '_' . $params['category'];
        }

        return $key;
    }

    /**
     * @param $params
     * @return mixed
     */
    public function getWhoViewBought($params)
    {
        $key = $this->getKey(self::CACHE_PREFIX_WHO_VIEW_BOUGHT, $params);

        return Cache::get($key);
    }

    /**
     * @param $params
     * @return mixed
     */
    public function getTopSellerCategory($params)
    {
        $key = sprintf(self::CACHE_PREFIX_TOP_SELLER_CATEGORY, $params['category']);

        return Cache::get($key);
    }
}
