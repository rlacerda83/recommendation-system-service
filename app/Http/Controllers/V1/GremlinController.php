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
        } catch (Exception $e) {
            throw new StoreResourceFailedException($e->getMessage());
        }
    }

    public function runQuery(Request $request)
    {
        try {
            $objectRequest = json_decode($request->getContent());

            if (!$objectRequest->query) {
                throw new StoreResourceFailedException('Invalid query');
            }

//              $objectRequest->query = "
// p1 = graph.addVertex(label, 'product', 'name', 'Product 1', 'id', '1')
// p2 = graph.addVertex(label, 'product', 'name', 'Product 2', 'id', '2')
// p3 = graph.addVertex(label, 'product', 'name', 'Product 3', 'id', '3')
// p4 = graph.addVertex(label, 'product', 'name', 'Product 4', 'id', '4')
// p5 = graph.addVertex(label, 'product', 'name', 'Product 5', 'id', '5')

// user1 = graph.addVertex(label, 'user', 'name', 'User 1', 'id', '1')
// user2 = graph.addVertex(label, 'user', 'name', 'User 2', 'id', '2')
// user3 = graph.addVertex(label, 'user', 'name', 'User 3', 'id', '3')
// user4 = graph.addVertex(label, 'user', 'name', 'User 4', 'id', '4')
// user5 = graph.addVertex(label, 'user', 'name', 'User 5', 'id', '5')

// c1 = graph.addVertex(label, 'category', 'name', 'Category 1', 'id', '1')
// c2 = graph.addVertex(label, 'category', 'name', 'Category 2', 'id', '2')

// user1.addEdge('view', p1, 'date', '2015-11-17')
// user1.addEdge('view', p2, 'date', '2015-11-17')
// user1.addEdge('view', p3, 'date', '2015-11-16')
// user2.addEdge('view', p1, 'date', '2015-11-16')
// user2.addEdge('view', p5, 'date', '2015-11-17')
// user3.addEdge('view', p3, 'date', '2015-11-16')
// user3.addEdge('view', p5, 'date', '2015-11-17')
// user3.addEdge('view', p2, 'date', '2015-11-17')
// user3.addEdge('view', p1, 'date', '2015-11-17')
// user4.addEdge('view', p1, 'date', '2015-11-13')

// p1.addEdge('belong', c1)
// p2.addEdge('belong', c1)
// p3.addEdge('belong', c2)
// p4.addEdge('belong', c2)
// p5.addEdge('belong', c2)
// ";
            $result = $this->gremlin->executeQuery($objectRequest->query);

            return response()->json(['data' => $result]);
        } catch (\Exception $e) {
            throw new StoreResourceFailedException($e->getMessage());
        }
    }
}
