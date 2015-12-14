<?php

namespace App\Services\Gremlin;

class Edge extends AbstractGremlin
{
    public function getType()
    {
        return 'E';
    }

    public function findOrCreate($edge, $vIn, $vOut)
    {
        if (!$edge || !$vIn || !$vOut) {
            throw new \Exception('Invalid arguments');
        }

        $vertex = new Vertex();

        // findOrCreate origin vertex
        $vertex->setSufix('in');
        $query = $vertex->findOrCreate($vIn->label, $vIn->properties, false);
        $vertex->prepareQuery($vIn->label, $vIn->properties);

        // findOrCreate target vertex
        $vertex->setSufix('out');
        $query .= $vertex->findOrCreate($vOut->label, $vOut->properties, false);
        $vertex->prepareQuery($vOut->label, $vOut->properties);

        //create edge betweeen vertices
        $edgeProperties = ParserProperties::parsePropertiesToInsert(false, $edge->properties);
        $query .= "vin.addEdge('{$edge->label}', vout";
        if (strlen($edgeProperties)) {
            $query .= ",{$edgeProperties});";
            $this->prepareQuery(false, $edge->properties);
        } else {
            $query .=  ');';
        }

        //die($query);

        return $this->executeQuery($query);
    }
}
