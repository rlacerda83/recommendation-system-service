<?php

namespace App\Services\Gremlin\ProductRecommendation;

use App\Services\Gremlin\ProductRecommendationInterface;
use Illuminate\Support\Facades\Cache;

class RedisAdapter implements ProductRecommendationInterface
{

    const CACHE_PREFIX_WHO_VIEW_ALSO_VIEW = 'rec_wvav_%s';

    const CACHE_PREFIX_WHO_VIEW_BOUGHT = 'rec_wvb_%s';

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

    public function getlastView()
    {

    }
}
