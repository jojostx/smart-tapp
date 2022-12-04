<div class="grid grid-cols-1 gap-8 mt-12 md:grid-cols-2 lg:grid-cols-3">
    @foreach ($plans as $plan)
        <x-plan.card :$plan/>
    @endforeach
</div>