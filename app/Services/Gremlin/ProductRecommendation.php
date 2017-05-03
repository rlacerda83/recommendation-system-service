<?php

namespace App\Services\Gremlin;

class ProductRecommendation extends AbstractGremlin
{
    const DEFAULT_LIMIT = 5;

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
}
