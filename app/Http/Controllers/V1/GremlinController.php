<?php

namespace App\Http\Controllers\V1;

use App\Services\Gremlin\Generic;
use Dingo\Api\Exception\StoreResourceFailedException;
use Dingo\Api\Routing\Helpers;
use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller as BaseController;

class GremlinController extends BaseController
{
    use Helpers;

    protected $gremlin;

    public function __construct(Generic $gremlin)
    {
        $this->gremlin = $gremlin;
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function clearData()
    {
        try {
            $this->gremlin->clearData();

            return $this->response->noContent();
        } catch (\Exception $e) {
            throw new StoreResourceFailedException($e->getMessage());
        }
    }

    public function runQuery(Request $request)
    {
        try {
            set_time_limit(0);
            $objectRequest = json_decode($request->getContent());

            if (!$objectRequest->query) {
                throw new StoreResourceFailedException('Invalid query');
            }

            $result = $this->gremlin->executeQuery($objectRequest->query);

            return response()->json(['data' => $result]);
        } catch (\Exception $e) {
            throw new StoreResourceFailedException($e->getMessage());
        }
    }
}
