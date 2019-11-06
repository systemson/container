<?php

namespace Tests\Example;

class Controller
{
    /**
     * @var Model
     */
    //public $model;
    public Model $model; // For PHP 7.4 property injection.

    /**
     * @var View
     */
    public $view;
    // public View $view; // For PHP 7.4 property injection.

    public $id;

    public function __construct(Model $model, View $view, int $optional = 1)
    {
        $this->model = $model;
        $this->view = $view;
        $this->id = $optional;
    }

    public function getModel(): Model
    {
        return $this->model;
    }

    public function getView(): View
    {
        return $this->view;
    }

    public function setId(int $id): int
    {
        return $this->id = $id;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function index(string $name): string
    {
        return $this->view->hello($name);
    }

    public function setBoolean(bool $value): bool
    {
        return $value;
    }

    public function setInt(int $value = 0): int
    {
        return $value;
    }
}
