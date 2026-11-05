<?php

declare(strict_types=1);

namespace App\Core;

class Container
{
    private array $bindings = [];
    private array $instances = [];

    public function bind(string $name, callable $factory): void
    {
        $this->bindings[$name] = $factory;
    }

    public function singleton(string $name, callable $factory): void
    {
        $this->bindings[$name] = function ($c) use ($name, $factory) {
            return $this->instances[$name] ??= $factory($c);
        };
    }

    public function make(string $name): mixed
    {
        if (!isset($this->bindings[$name])) {
            throw new \RuntimeException("Binding tidak ditemukan: {$name}");
        }

        return ($this->bindings[$name])($this);
    }
}