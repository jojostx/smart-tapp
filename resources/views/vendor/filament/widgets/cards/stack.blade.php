<div {{
        $attributes
            ->merge($getExtraAttributes())
            ->class([
                'flex flex-col',
                match ($getSpace()) {
                    1 => 'space-y-1',
                    2 => 'space-y-2',
                    3 => 'space-y-3',
                    4 => 'space-y-4',
                    default => 'space-y-1',
                },
            ])
    }}>
    @foreach ($getCachedData() as $card)
    {{ $card }}
    @endforeach
</div>