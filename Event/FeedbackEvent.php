<?php

namespace BeSimple\DeploymentBundle\Event;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\Process\Process;

class FeedbackEvent extends Event
{
    /**
     * @var string
     */
    protected $type;

    /**
     * @var string
     */
    protected $line;

    /**
     * @param string $type
     * @param string $line
     */
    public function __construct($type, $line)
    {
        $this->type = $type;
        $this->line = $line;
    }

    /**
     * @return boolean
     */
    public function isError()
    {
        return $this->type === 'err';
    }

    /**
     * @return string
     */
    public function getFeedback()
    {
        return $this->line;
    }
}
