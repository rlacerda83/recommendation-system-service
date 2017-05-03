<?php

namespace App\Http\Controllers\V1;

use App\Services\Gremlin\ProductRecommendation;
use Dingo\Api\Exception\StoreResourceFailedException;
use Dingo\Api\Routing\Helpers;
use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller as BaseController;

class RecommendationsController extends BaseController
{
    use Helpers;

    protected $recommendations;

    public function __construct(ProductRecommendation $recommendations)
    {
        $this->recommendations = $recommendations;
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getViewAlsoView(Request $request)
    {
        try {
            $objectRequest = json_decode($request->getContent());
            $result = $this->recommendations->getWhoViewAlsoView($objectRequest);

            return response()->json(['data' => array_shift($result)]);
        } catch (\Exception $e) {
            throw new StoreResourceFailedException($e->getMessage());
        }
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getViewBought(Request $request)
    {
        try {
            $objectRequest = json_decode($request->getContent());
            $result = $this->recommendations->getWhoViewBought($objectRequest);

            return response()->json(['data' => array_shift($result)]);
        } catch (\Exception $e) {
            throw new StoreResourceFailedException($e->getMessage());
        }
    }
}
