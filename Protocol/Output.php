<?php

namespace BeSimple\DeploymentBundle\Protocol;

/**
 * @author: Jean-François Simon <contact@jfsimon.fr>
 */
class Output
{
    protected $message;
    protected $isError;

    public function __construct($message, $isError = false)
    {
        $this->message = $message;
        $this->isError = $isError;
    }

    public function getMessage()
    {
        return $this->message;
    }

    public function getIsError()
    {
        return $this->isError;
    }
}
