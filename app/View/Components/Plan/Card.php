<?php

namespace App\View\Components\Plan;

use Illuminate\View\Component;

class Card extends Component
{
    /**
     * The plan Model.
     *
     * @var \Jojostx\Larasubs\Models\Plan
     */
    public $plan;

    /**
     * The query params array.
     */
    public array $params = [];

    /**
     * The plan Currency.
     *
     * @var \Akaunting\Money\Currency
     */
    public $currency;

    /**
     * hightlight the plan in the blade view.
     *
     * @var null|bool
     */
    public $shouldHighlight;

    /**
     * the route to be use in the link button of plan in the blade view.
     *
     * @var null|bool
     */
    public string $route;

    /**
     * the label to be use in the link button of plan in the blade view.
     *
     * @var null|bool
     */
    public string $routeLabel;

    /**
     * Create a new component instance.
     *
     * @param  \Jojostx\Larasubs\Models\Plan  $plan
     * @param  bool  $should_highlight
     * @return void
     */
    public function __construct(
        $plan,
        $shouldHighlight = null,
        $route = '',
        $routeLabel = '',
        array $params = []
    ) {
        $this->plan = $plan;
        $this->shouldHighlight = $shouldHighlight;
        $this->route = \filled($route) ? $route : 'register';
        $this->routeLabel = \filled($routeLabel) ? $routeLabel : __('Start now');
        $this->currency = currency($this->plan->currency);
        $this->params = $params;
    }

    public function getCurrency()
    {
        return $this->currency;
    }

    public function getCurrencySymbol()
    {
        return $this->currency->getSymbol();
    }

    public function getParams()
    {
        return \array_merge(['plan' => $this->plan->slug], $this->params);
    }

    public function getPrice()
    {
        return money($this->plan->price, $this->plan->currency);
    }

    public function getPricePerInterval()
    {
        return $this->getPrice()->divide($this->plan->interval)->getValue();
    }

    public function getIntervalType()
    {
        return $this->plan->interval_type;
    }

    public function getIcon()
    {
        return $this->plan->description->get('icon');
    }

    public function getTag()
    {
        return $this->plan->description->get('tag');
    }

    public function getFeatureTag($feature)
    {
        return sprintf($feature->description->get('tag', $feature->name), $feature->pivot->units);
    }

    public function getRoute()
    {
        return \route($this->route, $this->getParams());
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.plan.card');
    }
}
