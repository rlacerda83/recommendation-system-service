<?php

namespace App\Http\Controllers\V1;

use App\Services\Gremlin\ProductRecommendation\GremlinAdapter;
use App\Services\Gremlin\ProductRecommendation\RedisAdapter;
use Dingo\Api\Exception\StoreResourceFailedException;
use Dingo\Api\Routing\Helpers;
use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller as BaseController;

class RecommendationsController extends BaseController
{
    use Helpers;

    /**
     * @var RedisAdapter
     */
    protected $recommendations;

    public function __construct(RedisAdapter $recommendations)
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
            $params = $request->all();
            if (empty($params['product'])) {
                throw new \Exception ('Product can not be empty');
            }

            $result = $this->recommendations->getWhoViewAlsoView($params);

            return response()->json(['data' => $result]);
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
            $params = $request->all();
            if (empty($params['product'])) {
                throw new \Exception ('Product can not be empty');
            }

            $result = $this->recommendations->getWhoViewBought($params);

            return response()->json(['data' => $result]);
        } catch (\Exception $e) {
            throw new StoreResourceFailedException($e->getMessage());
        }
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getTopSellerCategory(Request $request)
    {
        try {
            $params = $request->all();
            if (empty($params['category'])) {
                throw new \Exception ('Category can not be empty');
            }

            $result = $this->recommendations->getTopSellerCategory($params);

            return response()->json(['data' => $result]);
        } catch (\Exception $e) {
            throw new StoreResourceFailedException($e->getMessage());
        }
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function getLastView()
    {
        try {
            $gremlinAdapter = new GremlinAdapter();

            $result = $gremlinAdapter->getLastView();
            return response()->json(['data' => array_shift($result)]);
        } catch (\Exception $e) {
            throw new StoreResourceFailedException($e->getMessage());
        }
    }
}
