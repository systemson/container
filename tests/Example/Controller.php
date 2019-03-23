<?php

namespace Tests\Example;

class Controller
{
    /**
     * @var Amber\Container\Tests\Example\Model
     */
    public $model;

    /**
     * @inject Tests\Example\View
     *
     * @var string
     */
    public $view;
    public $id;

    public function __construct(Model $model, View $view, int $optional = 1)
    {
        $this->model = $model;
        $this->view = $view;
        $this->id = $optional;
    }

    public function getModel()
    {
        return $this->model;
    }

    public function getView()
    {
        return $this->view;
    }

    public function setId(int $id)
    {
        return $this->id = $id;
    }
}
