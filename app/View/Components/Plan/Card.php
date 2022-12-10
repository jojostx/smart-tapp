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
    public $should_highlight;

    /**
     * Create a new component instance.
     * 
     * @param \Jojostx\Larasubs\Models\Plan $plan
     * @param bool $should_highlight
     * 
     * @return void
     */
    public function __construct($plan, $should_highlight = null)
    {
        $this->plan = $plan;
        $this->should_highlight = $should_highlight;
        $this->currency = currency($this->plan->currency);
    }

    public function is_highlighted()
    {
        if ($this->should_highlight === null) {
            $this->should_highlight = $this->plan->description->get('highlight', false);
        }

        return $this->should_highlight;
    }

    public function getCurrency()
    {
        return $this->currency;
    }

    public function getCurrencySymbol()
    {
        return $this->currency->getSymbol();
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
