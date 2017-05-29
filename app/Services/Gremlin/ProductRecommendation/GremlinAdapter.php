<?php

namespace App\Services\Gremlin\ProductRecommendation;

use App\Services\Gremlin\AbstractGremlin;
use App\Services\Gremlin\ProductRecommendationInterface;
use App\Services\Gremlin\Vertex;

class GremlinAdapter extends AbstractGremlin implements ProductRecommendationInterface
{
    const DEFAULT_LIMIT = 5;

    const QUERY_GET_PRODUCTS = "g.V().hasLabel('product').range(%d, %d).values()";

    const QUERY_RECOMMENDATIONS = "g.V().has('productId', %d).as('p').in('view')
        .out('view').barrier().where(out('belong').has('categoryId', %d)).barrier()
        .where(neq('p')).groupCount().by('productId').order(local).by(values, decr).limit(local, %d)";

    const QUERY_GET_CATEGORIES_BY_PRODUCT = "g.V().has('productId', %d).out('belong').dedup().values()";

    const QUERY_GET_LAST_VIEW = "g.E().hasLabel('view').order().by('viewDate', Order.decr).limit(1).values()";

    public function getType()
    {
        return;
    }

    /**
     * @param $objRequest
     * @return mixed
     */
    public function getWhoViewAlsoView($objRequest)
    {
        $query = $this->buildBaseWhoAlsoQuery('view', $objRequest);
        $this->prepareQuery($objRequest->product->label, $objRequest->product->properties);

        return $this->executeQuery($query);
    }

    /**
     * @param $objRequest
     * @return mixed
     */
    public function getWhoViewBought($objRequest)
    {
        $query = $this->buildBaseWhoAlsoQuery('buy', $objRequest);
        $this->prepareQuery($objRequest->product->label, $objRequest->product->properties);

        return $this->executeQuery($query);
    }

    /**
     * @param $type
     * @param $objRequest
     * @return string
     */
    protected function buildBaseWhoAlsoQuery($type, $objRequest)
    {
        $vertex = new Vertex();
        $query = $vertex->findBy($objRequest->product->label, $objRequest->product->properties);

        $queryCategory = '';
        if (isset($objRequest->category) && strlen($objRequest->category)) {
            $queryCategory = "where(out('belong').has('categoryId',ID_CATEGORY)).";
            $this->connection->message->bindValue('ID_CATEGORY', "{$objRequest->category}");
        }

        $limit = isset($objRequest->limit) ? (int) $objRequest->limit : self::DEFAULT_LIMIT;

        $query .= sprintf(".as('p').in('view').
            out('view').barrier().where(neq('p')).
            %s
            groupCount().by('productId').order(local).by(values, decr).limit(local, %s);",
            $queryCategory,
            $limit
        );

        return $query;
    }

    /**
     * @return mixed
     */
    public function getLastView()
    {
        $query = self::QUERY_GET_LAST_VIEW;
        return $this->executeQuery($query);
    }
}
