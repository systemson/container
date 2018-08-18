<?php

namespace Tests\Example;

class Controller
{
    /**
     *
     * @inject Tests\Example\View
     *
     * @var string
     */
    public $view;

    /**
     *
     * @var Amber\Container\Tests\Example\Model
     */
    public $model;

    public function __construct(int $id, Model $model)
    {
        $this->model = $model;
    }

    public function getModel()
    {
        return $this->model;
    }

    public function getView()
    {
        return $this->view;
    }
}
