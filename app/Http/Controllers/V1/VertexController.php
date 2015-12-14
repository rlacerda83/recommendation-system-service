<?php

namespace App\Http\Controllers\V1;

use App\Services\Gremlin\Connection;
use App\Services\Gremlin\Vertex;
use Dingo\Api\Exception\StoreResourceFailedException;
use Dingo\Api\Routing\Helpers;
use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller as BaseController;

class VertexController extends BaseController
{
    use Helpers;

    protected $vertex;
    protected $itemsPerPage = 30;

    public function __construct(Vertex $vertex, Request $request)
    {
        $this->vertex = $vertex;
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index(Request $request)
    {
        try {
            $page = $request->input('page', 1);
            $skip = (int) ($this->itemsPerPage * ($page - 1));
            $limit = $this->itemsPerPage;

            $connection = Connection::getConnection();

            $result = $connection->send('g.V()');
            //$result = $connection->send('g.V().[0..2]');
            //g.addVertex(null,[name:"stephen"])

            //$teste = $db->send('g.addV(T.label, "user", "name","Lacerda", "idUser", "2")');

            //$originalCount = $db->send('g.V().count()');
            //$teste = $db->send('g.addV("label", "user", "name","Rodrigo", "idUser", "1")');
            $connection->close();
            print_r($result);
            die('--');

            $paginator = $this->repository->findAllPaginate($request);

            return $this->response->paginator($paginator, new EmailTransformer());
        } catch (QueryParserException $e) {
            throw new StoreResourceFailedException($e->getMessage(), $e->getFields());
        }
    }

    public function create(Request $request)
    {
        try {
            $objectRequest = json_decode($request->getContent());
            $this->validRequest($objectRequest);

            $this->vertex->findOrCreate($objectRequest->vertex->label, $objectRequest->vertex->properties, true);

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
        $result = $this->vertex->findById($id);
        if (!$result) {
            throw new StoreResourceFailedException('Vertex not found');
        }

        return response()->json(['data' => $result]);
    }

    public function update(Request $request, $id)
    {
        $result = $this->vertex->findById($id);
        if (!$result) {
            throw new StoreResourceFailedException('Vertex not found');
        }

        $objectRequest = json_decode($request->getContent());
        $this->validRequest($objectRequest);
        $result = $this->vertex->updatePropertiesById($id, $objectRequest->vertex->properties);

        return response()->json(['data' => $result]);
    }

    public function removeProperty(Request $request, $id)
    {
        $result = $this->vertex->findById($id);
        if (!$result) {
            throw new StoreResourceFailedException('Vertex not found');
        }

        $objectRequest = json_decode($request->getContent());
        $this->validRequest($objectRequest);

        $result = $this->vertex->removePropertiesById($id, $objectRequest->vertex->properties);

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
            $result = $this->vertex->deleteById($id);
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
