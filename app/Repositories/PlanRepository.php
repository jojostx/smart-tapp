<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Jojostx\Larasubs\Models\Plan;

class PlanRepository extends Repository
{
    protected static string $model = Plan::class;

    protected static function getConnection(): ?string
    {
        return getCentralConnection();
    }

    public function getAll(): Collection
    {
        return $this->all();
    }

    public function getActive(): Collection
    {
        return $this->whereActive()->get();
    }

    public function getActiveExceptFree(): Collection
    {
        return $this->where('price', '>', 0)->whereActive()->get();
    }

    public function getActiveExcept(string | array $slug): Collection
    {
        if (is_array($slug) && \filled($slug)) {
            $slugs = \array_values($slug);

            return $this->whereNotIn('slug', $slugs)->whereActive()->get();
        }

        return $this->whereNot('slug', $slug)->whereActive()->get();
    }

    public function getBySlug(string $slug): ?Plan
    {
        return $this->where('slug', $slug)->first();
    }

    public function getActiveBySlug(string $slug): ?Plan
    {
        return $this->whereActive()->where('slug', $slug)->first();
    }

    public function getInactiveBySlug(string $slug): ?Plan
    {
        return $this->whereNotActive()->where('slug', $slug)->first();
    }
}
