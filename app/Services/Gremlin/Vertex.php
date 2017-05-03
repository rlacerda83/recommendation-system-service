<?php

namespace App\Services\Gremlin;

class Vertex extends AbstractGremlin
{
    public function getType()
    {
        return 'V';
    }

    public function insert($label = false, $properties, $sendCommand = false)
    {
        $query = 'g.addV(';
        $sufix = $this->getSufix();
        if ($label) {
            $query .= "T.label,BIND_LABEL{$sufix}";
        }

        $parserProperties = ParserProperties::parsePropertiesToInsert($label, $properties, $this->getSufix());
        $query .= "{$parserProperties})";

        if ($sendCommand) {
            $this->prepareQuery($label, $properties);

            return $this->executeQuery($query);
        }

        return $query;
    }

    public function findOrCreate($label = false, $properties, $sendCommand = false)
    {
        $commandFind = $this->findBy($label, $properties);
        $commandInsert = $this->insert($label, $properties);

        $sufix = $this->getSufix();
        $query = "v{$sufix} = {$commandFind}.tryNext().orElseGet{{$commandInsert}.next()};";

        if ($sendCommand) {
            $this->prepareQuery($label, $properties);

            return $this->executeQuery($query);
        }

        return $query;
    }

    public function findBy($label = false, $properties, $sendCommand = false)
    {
        $sufix = $this->getSufix();
        $query = 'g.V()';

        if ($label) {
            $query .= ".has(label,BIND_LABEL{$sufix})";
        }

        $parserProperties = ParserProperties::parsePropertiesToFindBy($properties, $sufix);
        $query .= "{$parserProperties}";

        if ($sendCommand) {
            $this->prepareQuery($label, $properties);

            return $this->executeQuery($query);
        }

        return $query;
    }
}
