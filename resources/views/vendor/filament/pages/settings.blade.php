<x-filament::page x-data="{ tab: window.location.hash ? window.location.hash.substring(1) : 'subscription' }">
  <div class="mb-4 border-b border-gray-400 dark:border-gray-700">
    <ul class="flex flex-wrap -mb-px text-sm font-medium text-center" id="myTab" data-tabs-toggle="#myTabContent" role="tablist">
      <li class="mr-2" role="presentation">
        <button :class="tab === 'subscription' ? 'active-tab': 'inactive-tab'" @click.prevent="tab = 'subscription'; window.location.hash = 'subscription'" class="tab " id="subscription-tab" type="button" role="tab" aria-controls="subscription" aria-selected="true">Subscription</button>
      </li>
      <li class="mr-2" role="presentation">
        <button :class="tab === 'plans' ? 'active-tab': 'inactive-tab'" @click.prevent="tab = 'plans'; window.location.hash = 'plans'" class="tab " id="plans-tab" type="button" role="tab " aria-controls="plans" aria-selected="false">Plans</button>
      </li>
      <li class="mr-2" role="presentation">
        <button :class="tab === 'payment-methods' ? 'active-tab': 'inactive-tab'" @click.prevent="tab = 'payment-methods'; window.location.hash = 'payment-methods'" class="tab " id="payment-methods-tab" type="button" role="tab" aria-controls="payment-methods" aria-selected="false">Payment Methods</button>
      </li>
      <li role="presentation">
        <button :class="tab === 'billing-information' ? 'active-tab': 'inactive-tab'" @click.prevent="tab = 'billing-information'; window.location.hash = 'billing-information'" class="tab " id="billing-information-tab" type="button" role="tab" aria-controls="billing-information" aria-selected="false">Billing Information</button>
      </li>
    </ul>
  </div>
  <div id="myTabContent">
    <div x-show="tab === 'subscription'" x-cloak class="p-4 bg-white rounded-lg dark:bg-gray-800" id="subscription" role="tabpanel" aria-labelledby="subscription-tab">
      <p class="text-sm text-gray-500 dark:text-gray-400">This is some placeholder content the <strong class="font-medium text-gray-800 dark:text-white">Subscription tab's associated content</strong>. Clicking another tab will toggle the visibility of this one for the next. The tab JavaScript swaps classes to control the content visibility and styling.</p>
    </div>
    <div x-show="tab === 'plans'" x-cloak class="p-4 bg-white rounded-lg dark:bg-gray-800" id="plans" role="tabpanel" aria-labelledby="plans-tab">
      <p class="text-sm text-gray-500 dark:text-gray-400">This is some placeholder content the <strong class="font-medium text-gray-800 dark:text-white">Plans tab's associated content</strong>. Clicking another tab will toggle the visibility of this one for the next. The tab JavaScript swaps classes to control the content visibility and styling.</p>
    </div>
    <div x-show="tab === 'payment-methods'" x-cloak class="p-4 bg-white rounded-lg dark:bg-gray-800" id="payment-methods" role="tabpanel" aria-labelledby="payment-methods-tab">
      <p class="text-sm text-gray-500 dark:text-gray-400">This is some placeholder content the <strong class="font-medium text-gray-800 dark:text-white">Payment Methods tab's associated content</strong>. Clicking another tab will toggle the visibility of this one for the next. The tab JavaScript swaps classes to control the content visibility and styling.</p>
    </div>
    <div x-show="tab === 'billing-information'" x-cloak class="p-4 bg-white rounded-lg dark:bg-gray-800" id="billing-information" role="tabpanel" aria-labelledby="billing-information-tab">
      <p class="text-sm text-gray-500 dark:text-gray-400">This is some placeholder content the <strong class="font-medium text-gray-800 dark:text-white">Billing Information tab's associated content</strong>. Clicking another tab will toggle the visibility of this one for the next. The tab JavaScript swaps classes to control the content visibility and styling.</p>
    </div>
  </div>
</x-filament::page>