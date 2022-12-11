<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Jojostx\Larasubs\Models\Plan;

class PlanRepository
{
    protected ?string $centralConnection;

    public function __construct()
    {
        $this->centralConnection = static::getCentralConnection();
    }

    protected static function getCentralConnection(): string
    {
        return config('tenancy.database.central_connection');
    }

    public function all(): Collection
    {
        return Plan::on($this->centralConnection)->all();
    }

    public function getActive(): Collection
    {
        return Plan::on($this->centralConnection)->whereActive()->get();
    }

    public function getBySlug(string $slug): ?Plan
    {
        return Plan::on($this->centralConnection)->where('slug', $slug)->first();
    }

    public function getActiveBySlug(string $slug): ?Plan
    {
        return Plan::on($this->centralConnection)->whereActive()->where('slug', $slug)->first();
    }

    public function getInactiveBySlug(string $slug): ?Plan
    {
        return Plan::on($this->centralConnection)->whereNotActive()->where('slug', $slug)->first();
    }
}