<?php

namespace App\View\Components\Plan;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\View\Component;
use Jojostx\Larasubs\Models\Plan;

class Index extends Component
{
    /**
     * The query params array.
     */
    public array $params = [];

    /**
     * The plans collection.
     *
     * @var null|Collection
     */
    public $plans;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($plans, array $params = [])
    {
        $this->plans = $plans;
        $this->params = $params;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.plan.index');
    }
}
