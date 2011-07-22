<?php

namespace BeSimple\DeploymentBundle\Exceptions;

use BeSimple\DeploymentBundle\Protocol\ProtocolInterface;

/**
 * @author: Jean-François Simon <contact@jfsimon.fr>
 */
class ConnectionException extends ProtocolException
{
    protected $protocol;
    protected $host;
    protected $port;

    public function __construct(ProtocolInterface $protocol, $host, $port)
    {
        $this->protocol = $protocol;
        $this->host     = $host;
        $this->port     = $port;
    }

    public function getWarning()
    {
        return sprintf('Connection failed on %s:%s', $this->host, $this->port);
    }

    public function getHost()
    {
        return $this->host;
    }

    public function getPort()
    {
        return $this->port;
    }
}
