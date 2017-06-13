<?php

namespace App\Services\Gremlin\ProductRecommendation\Processors;

use App\Services\Gremlin\Generic;
use App\Services\Gremlin\ProductRecommendation\GremlinAdapter;
use App\Services\Gremlin\ProductRecommendation\RedisAdapter;
use Brightzone\GremlinDriver\ServerException;
use Illuminate\Support\Facades\Cache;


class WhoViewAlsoViewProcessor
{

    const LIMIT_GENERATED_RECOMMENDATIONS = 20;

    /**
     * @var Generic
     */
    protected $gremlinConnection;

    /**
     * WhoViewAlsoViewProcessor constructor.
     * @param Generic $gremlinConnection
     */
    public function __construct(Generic $gremlinConnection)
    {
        $this->gremlinConnection = $gremlinConnection;
    }

    /**
     * @param $arrayProducts
     */
    public function getRecommendationsForProduct($arrayProducts)
    {
        foreach ($arrayProducts as $idProduct) {
            $categories = $this->getCategoriesByProduct($idProduct);
            foreach ($categories as $idCategory) {
                $recommendations = $this->getRecommendationsByProductAndCategory($idProduct, $idCategory);
                Cache::forever(
                    sprintf(RedisAdapter::CACHE_PREFIX_CATEGORY, $idProduct, $idCategory),
                    $recommendations
                );
            }
        }
    }

    /**
     * @param $idProduct
     * @return mixed
     */
    protected function getCategoriesByProduct($idProduct)
    {
        try {
            $categoriesQuery = sprintf(GremlinAdapter::QUERY_GET_CATEGORIES_BY_PRODUCT, $idProduct);
            return $this->gremlinConnection->executeQuery($categoriesQuery);
        } catch (ServerException $e) {
            return [];
        }
    }

    /**
     * @param $idProduct
     * @param $idCategory
     * @return mixed
     */
    protected function getRecommendationsByProductAndCategory($idProduct, $idCategory)
    {
        $query = sprintf(
            GremlinAdapter::QUERY_RECOMMENDATIONS,
            $idProduct,
            $idCategory,
            self::LIMIT_GENERATED_RECOMMENDATIONS
        );

        return $this->gremlinConnection->executeQuery($query);
    }
}