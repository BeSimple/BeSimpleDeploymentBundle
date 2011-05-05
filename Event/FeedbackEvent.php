<?php

namespace BeSimple\DeploymentBundle\Event;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\Process\Process;

class FeedbackEvent extends Event
{
    protected $type;
    protected $line;

    public function __construct($type, $line)
    {
        $this->type = $type;
        $this->line = $line;
    }

    public function isError()
    {
        return $this->type === 'err';
    }

    public function getFeedback()
    {
        return $this->line;
    }
}
