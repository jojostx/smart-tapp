<?php

use App\Models\Tenant;
use Illuminate\Support\Carbon;
use Illuminate\Support\HtmlString;
use Jojostx\Larasubs\Models\Plan;
use Jojostx\Larasubs\Models\Subscription;

if (!function_exists('getAvatarUrl')) {
    /**
     *  generates an avatar for a name from the Ui-avatar website
     *
     *  @param  string  $name name to generate avatar for
     *  @param  string  $attributes configurations for the avatar
     *  @return string
     */
    function getUiAvatarUrl(string $name = '', array $attributes = []): string
    {
        $name = Str::of($name)
            ->trim()
            ->explode(' ')
            ->map(fn (string $segment): string => filled($segment) ? mb_substr($segment, 0, 1) : '')
            ->join(' ');

        $attributes = http_build_query(
            array_merge([
                'name' => str($name)->limit(3, '')->value(),
                'background' => 'random'
            ], $attributes)
        );

        return 'https://ui-avatars.com/api/?' . $attributes;
    }
}

if (!function_exists('replaceQrCodeAttributes')) {
    /**
     *  replaces qrcode width and height attributes with class(es)
     *
     *  @param  string  $qrcode a string representation of a QR Code svg, ex: <svg class="text-sm"><p style="line-height: 1.3rem;">sample</p></svg>
     *  @param  string  $classes a string representation of CSS classes to append
     *  @param  string  $id id attribute to be set on the qrcode svg
     *  @return HtmlString
     */
    function replaceQrCodeAttributes(string $qrcode = '', string $classes = 'w-full', string $id = ''): HtmlString
    {
        if (blank($qrcode)) {
            return str('<span> - </span>')->toHtmlString();
        }

        $xmlDom = new DOMDocument();

        if (!$xmlDom->loadXML($qrcode)) {
            return str($qrcode)->toHtmlString();
        }

        $nodes = $xmlDom->getElementsByTagName('svg');

        if ($nodes->length !== 0) {
            /** @var DOMElement $svg */
            $svg = $nodes[0];

            $svg->removeAttribute('width');

            $svg->removeAttribute('height');

            $svg->setAttribute('class', $classes);

            $svg->setAttribute('id', $id);

            $svg->setIdAttribute('id', true);

            $svg = $svg->ownerDocument->saveXML($svg);

            return str($svg)->toHtmlString();
        }

        return str($qrcode)->toHtmlString();
    }
}

if (!function_exists('in_range')) {
    /**
     * Determines if $number is between $min and $max
     *
     * @param  int  $number     The number to test
     * @param  int  $min        The minimum value in the range
     * @param  int  $max        The maximum value in the range
     * @param  bool  $inclusive  Whether the range should be inclusive or not
     * @return bool              Whether the number was in the range
     */
    function in_range($number, $min, $max, $inclusive = false)
    {
        if (is_int($number) && is_int($min) && is_int($max)) {
            return $inclusive
                ? ($number >= $min && $number <= $max)
                : ($number > $min && $number < $max);
        }

        return false;
    }
}

if (!function_exists('elapsed')) {
    /**
     * Determines if $time is in the past
     *
     * @param  Carbon  $time
     * @param  Carbon|null  $referenceTime
     * @return bool
     */
    function elapsed(Carbon $time, ?Carbon $referenceTime = null)
    {
        $referenceTime = $referenceTime ?? now();

        return $referenceTime->greaterThanOrEqualTo($time);
    }
}

if (!function_exists('flattenWithKeys')) {
    /**
     * Flattens an array recursively while preserving keys by "smushing" them
     *
     * Also works by typecasting objects or xml, example: (array) $obj
     *
     * @param  array  $array
     * @param  string  $nestingDelimiter string to be used as a delimiter for nested keys
     * @param  string  $root string to be prefixed to the keys
     * @param  array  $result
     * @return array
     */
    function flattenWithKeys(array $array, $nestingDelimiter = '.', $root = '', $result = []): array
    {
        foreach ($array as $k => $v) {
            if ((is_array($v) || is_object($v)) && !empty($v)) {
                $result = flattenWithKeys((array) $v, $nestingDelimiter, $root . $k . $nestingDelimiter, $result);
            } else {
                $result[$root . $k] = $v;
            }
        }

        return $result;
    }
}

if (!function_exists('getCentralConnection')) {
    /**
     * gets the name of the central database connection
     *
     * @return string
     */
    function getCentralConnection(): ?string
    {
        return config('tenancy.database.central_connection');
    }
}

if (!function_exists('getTenant')) {
    /**
     * gets the name of the central database connection
     *
     * @return null|Tenant
     */
    function getTenant(string | Tenant $tenant): ?Tenant
    {
        return ($tenant instanceof Tenant) ? $tenant : \tenancy()->find($tenant);
    }
}

if (!function_exists('getPlanPrice')) {
    /**
     * gets the price for a plan
     *
     * @return int
     */
    function getPlanPrice(Plan $plan): int
    {
        return (int) \money($plan->price, $plan->currency)->getValue();
    }
}

if (!function_exists('calculateProratedAmount')) {
    /**
     * gets the prorated amount between a new plan and the current plan for a subscription
     * @link https://www.zoho.com/in/subscriptions/prorated-billing/
     *
     * @throws InvalidArgumentException
     *
     * @return int
     */
    function calculateProratedAmount(Plan $newPlan, Subscription $subscription): int
    {
        $tolerance = 50;
        $currentPlan = $subscription->plan;

        if (blank($currentPlan) || $currentPlan->isFree()) {
            return (int) \money($newPlan->price, $newPlan->currency)->getValue();
        }

        $daysLeft = now()->diffInDays($subscription->ends_at);
        $daysUsed = now()->diffInDays($subscription->starts_at);
        $totalDays = $subscription->starts_at->diffInDays($subscription->ends_at);

        if ($newPlan->currency !== $currentPlan->currency) {
            throw new InvalidArgumentException('Cannot calculate prorated amount for plans with mismatching currency', 1);
        }

        if (abs($newPlan->price - $currentPlan->price) < $tolerance) {
            return (int) \money($newPlan->price, $newPlan->currency)->getValue();
        } elseif ($newPlan->price > ($currentPlan->price + $tolerance)) {
            // upgraded proration calc
            $credit = $currentPlan->price - (($currentPlan->price / $totalDays) * $daysUsed);
            $price = (($newPlan->price / $totalDays) * $daysLeft) - $credit;

            return (int) \money($price, $newPlan->currency)->getValue();
        } else {
            // downgraded proration calc
            $credit = $currentPlan->price - (($currentPlan->price / $totalDays) * $daysUsed);
            $price = $credit - (($newPlan->price / $totalDays) * $daysLeft);

            return (int) \money($price, $newPlan->currency)->getValue();
        }
    }
}

if (!function_exists('planChangeFrequencyLimit')) {
    /**
     * returns the plan change frequency limit in human readable form
     * eg: 3 months, 3 weeks, 3 days
     */
    function planChangeFrequencyLimit(): string
    {
        $interval = (string) config('app.plan_change_interval');
        return  $interval . ' ' . str(config('app.plan_change_interval_type'))->plural();
    }
}

if (!function_exists('tenantCanChangePlanFor')) {
    /**
     * check if a tenant can change the plan for a subscription
     * 
     * - a tenant can change plan when they have no sub
     * - a tenant can change plan when their sub's current plan is free
     * - a tenant can change a plan if their sub is ended
     * - a tenant can change a plan if their sub's plan has not been changed within the past 3 months.
     */
    function tenantCanChangePlanFor(Subscription $subscription = null): bool
    {
        return blank($subscription) ||
            $subscription->isEnded() ||
            $subscription->plan->isFree() ||
            !$subscription->planWasChangedInTimePast(config('app.plan_change_interval'), config('app.plan_change_interval_type'));
    }
}

if (!function_exists('tenantCannotChangePlanFor')) {
    /**
     * check if a tenant cannot change the plan for a subscription
     */
    function tenantCannotChangePlanFor(Subscription $subscription = null): bool
    {
        return !tenantCanChangePlanFor($subscription);
    }
}

if (!function_exists('getTokenizationAmount')) {
    /**
     * get the minimum amount to tokenize a card
     */
    function getTokenizationAmount()
    {
        return 100;
    }
}

if (!function_exists('getTokenizationCurrency')) {
    /**
     * get the currency to tokenize a card //should default to customer's
     */
    function getTokenizationCurrency(): string
    {
        return 'NGN';
    }
}

if (!function_exists('central_route')) {
    function central_route(string $centralDomain, $route, $parameters = [], $absolute = true)
    {
        // replace first occurance of hostname fragment with $centralDomain
        $url = route($route, $parameters, $absolute);
        $hostname = parse_url($url, PHP_URL_HOST);
        $position = strpos($url, $hostname);

        $centralDomain = str_replace('https://', '', $centralDomain);

        return substr_replace($url, $centralDomain, $position, strlen($hostname));
    }
}
