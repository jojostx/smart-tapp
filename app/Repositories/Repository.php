<?php
  
namespace App\Repositories;

use Illuminate\Support\Facades\App;

abstract class Repository
{
    protected static string $model;

    protected static ?string $connection;

    public function __call(string $name, array $arguments)
    {
        return (App::make(static::$model))::on(static::getConnection())->{$name}(...$arguments);
    }

    protected static function getConnection(): ?string
    {
        return static::$connection;
    }
}