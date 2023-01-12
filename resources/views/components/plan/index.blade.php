@props(['params' => [], 'plans', 'groupByInterval' => false])

@php
    if($groupByInterval)
    {
        $free_plans = $plans->filter->isFree();

        $plans = $plans
            ->groupBy('interval')
            ->map(function ($group) use ($free_plans) {
                return $group->concat($free_plans)->unique()->sortBy('sort_order');
            });
    } else {
        $plans = $plans->filter(function ($plan) {
            return $plan->interval == 12;
        });
    }
@endphp

@if ($groupByInterval)
    <div x-data="{ interval: 12 }" class="mt-12">
        <div class="py-4 text-center">
            <div class="w-full max-w-sm px-4 mx-auto">
                <div class="flex w-full p-2 border-2 border-primary-500 rounded-xl bg-primary-500/30">
                    <button @click="interval = 1" :class="interval == 1 ? 'bg-primary-500 shadow-ios' : 'hover:text-primary-100'" class="block w-full py-1 text-sm text-white transition rounded-lg" type="button">
                        Monthly
                    </button>
                    <button @click="interval = 12" :class="interval == 12 ? 'bg-primary-500 shadow-ios' : 'hover:text-primary-100'" class="block w-full py-1 text-sm text-white transition rounded-lg" type="button">
                        Annual
                    </button>
                </div>
            </div>
        </div>

        @foreach ($plans as $planGroupKey => $planGroup)
            <div x-cloak x-show="interval == {{ $planGroupKey }}" class="mt-6 space-y-4 sm:space-y-0 sm:grid sm:grid-cols-2 sm:gap-6 lg:max-w-4xl lg:mx-auto xl:max-w-none xl:mx-0 xl:grid-cols-4">
                @foreach ($planGroup as $plan)
                    <x-plan.card :$plan :$params :should-highlight="$plan->description->get('highlight', false)" />
                @endforeach
            </div>
        @endforeach
    </div>
@else
    <div class="mt-12 space-y-4 sm:mt-16 sm:space-y-0 sm:grid sm:grid-cols-2 sm:gap-6 lg:max-w-4xl lg:mx-auto xl:max-w-none xl:mx-0 xl:grid-cols-4">
        @foreach ($plans as $plan)
            <x-plan.card :$plan :$params :should-highlight="$plan->description->get('highlight', false)" />
        @endforeach
    </div>
@endif

