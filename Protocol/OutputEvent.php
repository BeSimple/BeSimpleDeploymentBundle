<?php

namespace BeSimple\DeploymentBundle\Protocol;

use Symfony\Component\EventDispatcher\Event;

/**
 * @author: Jean-François Simon <contact@jfsimon.fr>
 */
class OutputEvent extends Event
{
    protected $subject;
    protected $timestamp;
    protected $output;

    public function __construct(ProtocolInterface $subject, Output $output)
    {
        $this->subject   = $subject;
        $this->timestamp = time();
        $this->output    = $output;
    }

    public function getSubject()
    {
        return $this->subject;
    }

    public function getTimestamp()
    {
        return $this->timestamp;
    }

    public function getMessage()
    {
        return $this->output->getMessage();
    }

    public function getIsError()
    {
        return $this->output->getIsError();
    }
}
