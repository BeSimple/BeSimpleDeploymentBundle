<?php

namespace BeSimple\DeploymentBundle\Protocol\Exceptions;

use BeSimple\DeploymentBundle\Protocol\ProtocolInterface;
use BeSimple\DeploymentBundle\Model\Identity;

/**
 * @author: Jean-François Simon <contact@jfsimon.fr>
 */
class AuthenticationException extends ProtocolException
{
    protected $host;
    protected $identity;

    public function __construct(ProtocolInterface $protocol, $host, Identity $identity)
    {
        $this->protocol = $protocol;
        $this->host     = $host;
        $this->identity = $identity;
    }

    public function getWarning()
    {
        return sprintf('Authentication failed on %s for %s', $this->host, $this->identity->getUser());
    }

    public function getHost()
    {
        return $this->host;
    }

    public function getIdentity()
    {
        return $this->identity;
    }
}
