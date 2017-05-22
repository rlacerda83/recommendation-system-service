<?php

namespace App\Services\Gremlin;

interface ProductRecommendationInterface
{

    /**
     * @param $objRequest
     * @return mixed
     */
    public function getWhoViewAlsoView($objRequest);

    /**
     * @param $objRequest
     * @return mixed
     */
    public function getWhoViewBought($objRequest);
}
