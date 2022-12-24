@props(['params' => [], 'plans'])

<div class="mt-12 space-y-4 sm:mt-16 sm:space-y-0 sm:grid sm:grid-cols-2 sm:gap-6 lg:max-w-4xl lg:mx-auto xl:max-w-none xl:mx-0 xl:grid-cols-4">
    @foreach ($plans as $plan)
        <x-plan.card :$plan :$params :should-highlight="$plan->description->get('highlight', false)" />
    @endforeach
</div>
