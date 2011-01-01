<?php

namespace Bundle\DeploymentBundle\Service;

class Scheduler
{
    protected $rules;

    public function __construct()
    {
        $this->rules = array();
    }

    public function addRule($name, $values)
    {
        if(! isset($this->rules[$name])) {
            $this->rules[$name] = array();
        }

        if(! is_array($values)) {
            $values = array($values);
        }

        $this->rules[$name] = array_merge($this->rules[$name], $values);
    }

    public function addRules(array $rules)
    {
        foreach($rules as $name => $values) {
            $this->addRule($name, $values);
        }
    }

    public function getIgnore()
    {
        return $this->getParameter('ignore');
    }

    public function getForce()
    {
        return $this->getParameter('force');
    }

    public function getCommands()
    {
        return $this->getParameter('commands');
    }

    protected function getParameter($parameter)
    {
        $values = array();

        foreach($this->rules as $rule) {
            $values = array_merge($values, $rule[$parameter]);
        }

        return array_unique($values);
    }
}