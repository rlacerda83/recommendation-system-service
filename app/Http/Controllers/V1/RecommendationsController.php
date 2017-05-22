<?php

namespace App\Http\Controllers\V1;

use App\Services\Gremlin\ProductRecommendation\RedisAdapter;
use Carbon\Carbon;
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
                throw new \Exception ('Product can be not empty');
            }

            if (empty($params['category'])) {
                throw new \Exception ('Category can be not empty');
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
            $objectRequest = json_decode($request->getContent());
            $result = $this->recommendations->getWhoViewBought($objectRequest);

            return response()->json(['data' => array_shift($result)]);
        } catch (\Exception $e) {
            throw new StoreResourceFailedException($e->getMessage());
        }
    }

    public function getLastView(Request $request)
    {
        try {
            $now = Carbon::now()->subMinutes(5);
            return response()->json(['data' => ['lastView' => $now->format('Y-m-d H:i:s')]]);
        } catch (\Exception $e) {
            throw new StoreResourceFailedException($e->getMessage());
        }
    }
}
