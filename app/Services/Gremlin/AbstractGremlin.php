<?php

namespace App\Services\Gremlin;

use Brightzone\GremlinDriver\ServerException;

abstract class AbstractGremlin
{
    const OBJECT_TYPE_VERTEX = 'V';
    const OBJECT_TYPE_EDGE = 'E';

    protected $connection = null;
    protected $type = null;
    protected $sufix = null;

    public function __construct()
    {
        $this->connection = Connection::getConnection();
        $this->type = $this->handleType($this->getType());
    }

    public function setSufix($value)
    {
        $this->sufix = (string) $value;
    }

    public function getSufix()
    {
        return $this->sufix;
    }

    public function paginate($page = 1, $itemsPage = 30)
    {
        $start = $page == 1 ? 0 : ($itemsPage * $page) - $itemsPage;
        $end = $page * $itemsPage;
        $query = "g.{$this->type}().range({$start},{$end})";

        $itens = $this->executeQuery($query);

        $queryTotal = "g.{$this->type}().count()";
        $resultTotal = $this->executeQuery($queryTotal);

        $itens['metadata'] = [
            'total' => $resultTotal[0],
            'page'  => $page,
        ];

        return $itens;
    }

    public function findById($id)
    {
        try {
            return $this->connection->send("g.{$this->type}('{$id}')");
        } catch (ServerException $e) {
            $this->handleException($e);
        }
    }

    public function deleteById($id)
    {
        try {
            $vertex = $this->findById($id);
            if (!$vertex) {
                throw new \Exception('Register not found');
            }

            return $this->connection->send("g.{$this->type}('{$id}').drop()");
        } catch (ServerException $e) {
            $this->handleException($e);
        }
    }

    public function removePropertiesById($id, $properties)
    {
        try {
            $query = "g.{$this->type}('{$id}').sideEffect{";

            $parserProperties = ParserProperties::parsePropertiesToRemove($properties);
            $query .= "{$parserProperties} };";

            $this->prepareQuery(false, $properties);

            return $this->executeQuery($query);
        } catch (ServerException $e) {
            $this->handleException($e);
        }
    }

    public function updatePropertiesById($id, $properties)
    {
        try {
            $query = "g.{$this->type}('{$id}')";

            $updateProperties = ParserProperties::parsePropertiesToUpdate($properties);
            $query .= $updateProperties;

            $this->prepareQuery(false, $properties);

            return $this->executeQuery($query);
        } catch (ServerException $e) {
            $this->handleException($e);
        }
    }

    public function clearData()
    {
        try {
            $this->connection->send('g.V().drop()');
        } catch (ServerException $e) {
            $this->handleException($e);
        }
    }

    protected function handleException(ServerException $e)
    {
        if ($e->getCode() == 204) {
            return false;
        }

        throw $e;
    }

    protected function handleType($type)
    {
        return strtoupper($type) == self::OBJECT_TYPE_EDGE ? self::OBJECT_TYPE_EDGE : self::OBJECT_TYPE_VERTEX;
    }

    public function createIndex($name)
    {
        if (!$name) {
            return false;
        }

        return $this->connection->send("g.createKeyIndex('name', Vertex.class)");
    }

    public function applyBinds($arrayBinds)
    {
        foreach ($arrayBinds as $key => $value) {
            $this->connection->message->bindValue("{$key}", "{$value}");
        }
    }

    protected function prepareQuery($label, $properties)
    {
        $arrayBinds = ParserProperties::parseBindValues($label, $properties, $this->getSufix());
        $this->applyBinds($arrayBinds);
    }

    public function executeQuery($query)
    {
        return $this->connection->send($query);
    }

    abstract public function getType();
}
