<x-filament::page x-data="{ tab: window.location.hash ? window.location.hash.substring(1) : 'plans' }">
    <div id="tabs_toggle" class="mb-4 border-b border-gray-400 dark:border-gray-700">
        <ul class="flex flex-wrap -mb-px text-sm font-medium text-center" role="tablist">
            <li class="mr-2" role="presentation">
                <button :class="tab === 'plans' ? 'active-tab': 'inactive-tab'" @click.prevent="tab = 'plans'; window.location.hash = 'plans'" class="tab " id="plans-tab" type="button" role="tab " aria-controls="plans" aria-selected="false">Plans</button>
            </li>
            <li class="mr-2" role="presentation">
                <button :class="tab === 'subscription' ? 'active-tab': 'inactive-tab'" @click.prevent="tab = 'subscription'; window.location.hash = 'subscription'" class="tab " id="subscription-tab" type="button" role="tab" aria-controls="subscription" aria-selected="true">Subscription</button>
            </li>
            <li class="mr-2" role="presentation">
                <button :class="tab === 'payment-methods' ? 'active-tab': 'inactive-tab'" @click.prevent="tab = 'payment-methods'; window.location.hash = 'payment-methods'" class="tab " id="payment-methods-tab" type="button" role="tab" aria-controls="payment-methods" aria-selected="false">Payment Methods</button>
            </li>
            <li role="presentation">
                <button :class="tab === 'billing-information' ? 'active-tab': 'inactive-tab'" @click.prevent="tab = 'billing-information'; window.location.hash = 'billing-information'" class="tab " id="billing-information-tab" type="button" role="tab" aria-controls="billing-information" aria-selected="false">Billing Information</button>
            </li>
        </ul>
    </div>

    <div id="tabs">
        <div x-show="tab === 'plans'" x-cloak class="p-4 py-6 bg-gray-900 rounded-lg dark:bg-gray-800" id="plans" role="tabpanel" aria-labelledby="plans-tab">
            <div>
                <p class="text-sm text-gray-300 dark:text-gray-300">You can change your plan <strong class="font-medium text-white dark:text-white">only once in 3 months. Please make sure to not exceed the feature allocations for the plan you are downgrading to, otherwise your excess parking lots, accesses and team members will be deleted!</strong></p>
                <x-plan :plans="$this->plans" :params="$this->params" />
            </div>
        </div>

        <div x-show="tab === 'subscription'" x-cloak class="p-4 bg-white rounded-lg dark:bg-gray-800" id="subscription" role="tabpanel" aria-labelledby="subscription-tab">
            @livewire('components.settings.subscriptions')

            @livewire('components.settings.payment-receipts')
        </div>

        <div x-show="tab === 'payment-methods'" x-cloak class="p-4 bg-white rounded-lg dark:bg-gray-800" id="payment-methods" role="tabpanel" aria-labelledby="payment-methods-tab">
            @livewire('components.settings.payment-methods')
        </div>

        <div x-show="tab === 'billing-information'" x-cloak class="p-4 space-y-4 bg-white rounded-lg dark:bg-gray-800" id="billing-information" role="tabpanel" aria-labelledby="billing-information-tab">
            <p class="text-gray-900 dark:text-gray-200">
                This information will be added to your invoice. <br>
                <span class="text-sm text-gray-500 dark:text-gray-400">
                    Some fields <strong class="font-medium text-gray-600 dark:text-white">(organization, email & name)</strong> will default to their equivalent for your account if left blank.
                </span>
            </p>

            <form wire:submit.prevent="saveBillingInfo">
                {{ $this->billingInfoForm }}

                <div class="flex items-center mt-6">
                    <x-filament::button type="submit" id="save-billing-info">
                        {{ __('Save changes') }}
                    </x-filament::button>
                </div>
            </form>
        </div>
    </div>
</x-filament::page>
