<?php

namespace App\Http\Controllers\V1;

use App\Services\Gremlin\Edge;
use Dingo\Api\Exception\StoreResourceFailedException;
use Dingo\Api\Routing\Helpers;
use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller as BaseController;

class EdgesController extends BaseController
{
    use Helpers;

    protected $edge;
    protected $itemsPerPage = 30;

    public function __construct(Edge $edge, Request $request)
    {
        $this->edge = $edge;
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index(Request $request)
    {
        try {
            $page = $request->input('page', 1);
            $result = $this->edge->paginate($page, 30);

            return response()->json(['data' => $result]);
        } catch (QueryParserException $e) {
            throw new StoreResourceFailedException($e->getMessage(), $e->getFields());
        }
    }

    public function create(Request $request)
    {
        try {
            $objectRequest = json_decode($request->getContent());

            $this->edge->findOrCreate(
                $objectRequest->edge,
                $objectRequest->vertexIn,
                $objectRequest->vertexOut
            );

            return $this->response->created();
        } catch (\Exception $e) {
            throw new StoreResourceFailedException($e->getMessage());
        }
    }

    /**
     * @param $id
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function get($id)
    {
        $result = $this->edge->findById($id);
        if (!$result) {
            throw new StoreResourceFailedException('Edge not found');
        }

        return response()->json(['data' => $result]);
    }

    public function update(Request $request, $id)
    {
        $result = $this->edge->findById($id);
        if (!$result) {
            throw new StoreResourceFailedException('Edge not found');
        }

        $objectRequest = json_decode($request->getContent());
        $result = $this->edge->updatePropertiesById($id, $objectRequest->edge->properties);

        return response()->json(['data' => $result]);
    }

    public function removeProperty(Request $request, $id)
    {
        $result = $this->edge->findById($id);
        if (!$result) {
            throw new StoreResourceFailedException('Edge not found');
        }

        $objectRequest = json_decode($request->getContent());
        $result = $this->edge->removePropertiesById($id, $objectRequest->edge->properties);

        return response()->json(['data' => $result]);
    }

    /**
     * @param $id
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function delete($id)
    {
        try {
            $result = $this->edge->deleteById($id);
            if (!$result) {
                return $this->response->noContent();
            }
        } catch (\Exception $e) {
            throw new StoreResourceFailedException($e->getMessage());
        }
    }

    private function validRequest($objectRequest)
    {
        if (!$objectRequest) {
            throw new StoreResourceFailedException('Invalid request');
        }
    }
}
