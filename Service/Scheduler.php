<?php

namespace Bundle\DeploymentBundle\Service;

class Scheduler
{
    protected $rules;

    public function __construct()
    {
        $this->rules = array();
    }

    public function addRule($rule, $values)
    {
        if(! isset($this->rules[$rule])) {
            $this->rules[$rule] = array();
        }

        if(! is_array($values)) {
            $values = array($values);
        }

        $this->rules[$rule] = array_merge($this->rules[$rule], $values);
    }

    public function addRules(array $rules)
    {
        foreach($rules as $name => $values) {
            $this->addRule($name, $values);
        }
    }

    public function getRule($name)
    {
        if(isset($this->rules[$name])) {
            return $this->rules[$name];
        }

        return null;
    }
}