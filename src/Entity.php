<?php

namespace RecastAI;

/**
 * Class Entity
 * @package RecastAI
 */
Class Entity
{
    public $name;
    public $raw;

    /**
     * Entity constructor.
     * @param $name
     * @param $data
     */
    public function __construct($name, $data)
    {
        $this->name = $name;
        $this->raw = $data;
        foreach ($data as $key => $value) {
            $this->{$key} = $value;
        }
    }
}
