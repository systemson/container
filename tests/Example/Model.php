<?php

namespace Tests\Example;

class Model
{
    public $id;

    public function getId()
    {
        return $this->id ?? 1;
    }

    public function setId($id)
    {
        $this->id = $id;
    }
}
