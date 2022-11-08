<?php

namespace Reverb\Ratchet;

use Ratchet\ConnectionInterface;
use Ratchet\WebSocket\MessageComponentInterface;
use Reverb\Contracts\ConnectionManager;
use Reverb\Server as ReverbServer;

class Server implements MessageComponentInterface
{
    public function __construct(protected ReverbServer $server, protected ConnectionManager $manager)
    {
    }

    /**
     * Handle the a client connection.
     *
     * @param  \Ratchet\ConnectionInterface  $connection
     * @return void
     */
    public function onOpen(ConnectionInterface $connection)
    {
        $this->server->open(
            $this->connection($connection)
        );
    }

    /**
     * Handle a new message received by the connected client.
     *
     * @param  \Ratchet\ConnectionInterface  $connection
     * @param  string  $message
     * @return void
     */
    public function onMessage(ConnectionInterface $from, $message)
    {
        $this->server->message(
            $this->connection($from),
            $message
        );
    }

    /**
     * Handle a client disconnection.
     *
     * @param  \Ratchet\ConnectionInterface  $connection
     * @return void
     */
    public function onClose(ConnectionInterface $connection)
    {
        $this->server->close(
            $this->connection($connection),
        );
    }

    /**
     * Handle an error.
     *
     * @param  \Ratchet\ConnectionInterface  $connection
     * @param  \Exception  $e
     * @return void
     */
    public function onError(ConnectionInterface $connection, \Exception $e)
    {
        $this->server->error(
            $this->connection($connection),
            $e
        );
    }

    /**
     * Get a Reverb connection from a Ratchet connection.
     *
     * @param  \Ratchet\ConnectionInterface  $connection
     * @return \Reverb\Contracts\Connection
     */
    protected function connection(ConnectionInterface $connection): Connection
    {
        if (! $managedConnection = $this->manager->get($connection->resourceId)) {
            $managedConnection = $this->manager->connect(
                new Connection($connection)
            );
        }

        return $managedConnection;
    }
}
