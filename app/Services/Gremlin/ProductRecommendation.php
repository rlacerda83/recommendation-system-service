<?php

namespace App\Services\Gremlin;

class ProductRecommendation extends AbstractGremlin
{
    const DEFAULT_LIMIT = 5;

    public function getType()
    {
        return;
    }

    public function getWhoViewAlsoView($objRequest)
    {
        $query = $this->buildBaseWhoAlsoQuery('view', $objRequest);
        $this->prepareQuery($objRequest->product->label, $objRequest->product->properties);

        return $this->executeQuery($query);
    }

    public function getWhoViewBought($objRequest)
    {
        $query = $this->buildBaseWhoAlsoQuery('bougth', $objRequest);
        $this->prepareQuery($objRequest->product->label, $objRequest->product->properties);

        return $this->executeQuery($query);
    }

    protected function buildBaseWhoAlsoQuery($type, $objRequest)
    {
        $vertex = new Vertex();
        $query = $vertex->findBy($objRequest->product->label, $objRequest->product->properties);

        $queryCategory = ').';
        if (isset($objRequest->category) && strlen($objRequest->category)) {
            $queryCategory = "filter(out('belong').has('id',ID_CATEGORY)).";
            $this->connection->message->bindValue('ID_CATEGORY', "{$objRequest->category}");
        }

        $limit = isset($objRequest->limit) ? (int) $objRequest->limit : self::DEFAULT_LIMIT;

        $query .= ".as('p').in('view').
            out('view').where(neq('p')).
            {$queryCategory}
            groupCount().by('id').order(local).by(valueDecr).limit(local,{$limit});";

        return $query;
    }
}
