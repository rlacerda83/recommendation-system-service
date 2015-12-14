<?php

namespace App\Services\Gremlin;

use Brightzone\GremlinDriver\Connection as GremlinConnection;

class Connection
{
    /**
     * @param array $options
     *
     * @throws \Exception
     *
     * @return bool
     */
    public static function getConnection($options = [])
    {
        static $connection = null;

        if ($connection === null) {
            $configConnection = [
                'host'     => env('GREMLIN_HOST', isset($options['host']) ? $options['host'] : 'localhost'),
                'port'     => env('GREMLIN_PORT', isset($options['port']) ? $options['port'] : '8182'),
                'graph'    => env('GREMLIN_GRAPH', isset($options['graph']) ? $options['graph'] : 'graph'),
                'username' => env('GREMLIN_USERNAME', isset($options['username']) ? $options['username'] : null),
                'password' => env('GREMLIN_PASSWORD', isset($options['password']) ? $options['password'] : null),
                'ssl'      => env('GREMLIN_SSL', isset($options['ssl']) ? $options['ssl'] : null),
            ];

            $connection = new GremlinConnection($configConnection);
            $connection->open();
        }

        return $connection;
    }
}
