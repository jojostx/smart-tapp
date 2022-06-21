<div class="fixed inset-x-0 bottom-0 z-50 pb-2 js-cookie-consent cookie-consent">
    <div class="px-6 mx-auto max-w-7xl">
        <div class="p-4 rounded-lg bg-primary-100">
            <div class="flex flex-wrap items-center justify-between">
                <div class="items-center flex-1 max-w-2xl">
                    <p class="mr-3 text-sm text-black cookie-consent__message">
                        {!! trans('cookie-consent::texts.message') !!}
                    </p>
                </div>
                <div class="flex-shrink-0 w-full mt-2 sm:mt-0 sm:w-auto">
                    <button class="flex items-center justify-center px-4 py-2 text-sm font-medium rounded-md cursor-pointer text-primary-100 bg-primary-800 js-cookie-consent-agree cookie-consent__agree hover:bg-primary-300">
                        {{ trans('cookie-consent::texts.agree') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
