<?php

namespace BeSimple\DeploymentBundle\Exceptions;

use BeSimple\DeploymentBundle\Protocol\ProtocolInterface;
use BeSimple\DeploymentBundle\Model\Identity;

/**
 * @author: Jean-François Simon <contact@jfsimon.fr>
 */
class ProtocolException extends \RuntimeException
{
    protected $protocol;
    protected $warning;

    public function __construct(ProtocolInterface $protocol, $warning)
    {
        $this->protocol = $protocol;
        $this->warning  = $warning;
    }

    public function getMessage()
    {
        return sprintf('[%s]: $message', $this->getProtocolName(), $this->message);
    }

    public function getProtocol()
    {
        return $this->protocol;
    }

    public function getWarning()
    {
        return $this->warning;
    }

    public function getProtocolName()
    {
        $class = get_class($this->protocol);

        return substr($class, strrpos($class, '\\') + 1);
    }
}
