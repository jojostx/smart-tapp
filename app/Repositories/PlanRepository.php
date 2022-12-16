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
        return getCentralConnection();
    }

    public function all(): Collection
    {
        return Plan::on($this->centralConnection)->all();
    }

    public function getActive(): Collection
    {
        return Plan::on($this->centralConnection)->whereActive()->get();
    }

    public function getActiveExceptFree(): Collection
    {
        $query = Plan::on($this->centralConnection);

        return $query->where('price', '>', 0)->whereActive()->get();
    }

    public function getActiveExcept(string|array $slug): Collection
    {
        $query = Plan::on($this->centralConnection);

        if (is_array($slug) && \filled($slug)) {
            $slugs = \array_values($slug);

            return $query->whereNotIn('slug', $slugs)->whereActive()->get();
        }

        return $query->whereNot('slug', $slug)->whereActive()->get();
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
