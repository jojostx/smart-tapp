<div class="mt-4">
    <div class="mb-4 space-y-2">
        <h1 class="font-semibold text-gray-900">Subscription</h1>
        <p class="text-sm text-gray-500 dark:text-gray-400">This section displays information about your current subscription.</p>

        <div class="p-4 text-sm border bg-primary-50 border-primary-600 text-primary-600">
            <ul class="list-disc list-inside">
                <li> You can switch from a free plan at anytime.</li>
                <li> You can switch plans for your current subscription only once in {{ \planChangeFrequencyLimit() }}. </li>
            </ul>
        </div>
    </div>

    {{ $this->table }}
</div>
