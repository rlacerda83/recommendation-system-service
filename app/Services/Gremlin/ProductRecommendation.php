<?php

namespace App\Services\Gremlin;

class ProductRecommendation extends AbstractGremlin
{

    public function getType() 
    {
        return  null;
    }

	public function getWhoViewAlsoView($objRequest) 
    {
        //g.V().has('userId', id).tryNext().orElseGet{ g.addV('userId', id).next() }
        //vincular categoria
        $vertex = new Vertex();

        $query = $vertex->findBy($objRequest->product->label, $objRequest->product->properties);
        $query .= ".as('p').in('view').out('view').where(neq('p')).groupCount().by('id').order(local).by(valueDecr);";
        $vertex->prepareQuery($objRequest->product->label, $objRequest->product->properties);
        
        return $this->executeQuery($query);
    }

    public function getWhoViewBuy($objRequest)
    {
        //vincular categoria

    }
}
