<x-filament::page>
    <div class="gap-4 md:grid md:grid-cols-7">
        <div class="max-w-3xl pb-6 sm:grid-cols-2 col-span-full lg:col-span-4">
            <div id="personal_information">
                <p class="mb-4 font-bold tracking-wide sm:text-lg">Personal Information</p>

                <div class="p-4 bg-white border rounded-lg shadow-sm md:p-6">
                    <form wire:submit.prevent="savePersonalInfo">
                        {{ $this->personalInfoForm }}
                        
                        @if ($this->canUpdateInfo())
                        <div class="flex items-center mt-6">
                            <x-filament::button type="submit" id="save-personal-info">
                                {{ __('Save changes') }}
                            </x-filament::button>
                        </div>
                        @endif
                    </form>
                </div>
            </div>

            <div class="mt-6 sm:mt-10" id="password_information">
                <p class="mb-4 font-bold tracking-wide sm:text-lg">Password Information</p>

                <div class="p-4 bg-white border rounded-lg shadow-sm md:p-6">
                    <form wire:submit.prevent="savePasswordInfo">
                        {{ $this->passwordInfoForm}}

                        <div class="mt-4">
                            <div class="font-medium">Password requirements:</div>
                            <div class="text-sm text-gray-500">Ensure that these requirements are met:</div>
                            <ul class="pl-4 text-xs leading-4 text-gray-500">
                                <li class="">1. At least 10 characters (and up to 100 characters)</li>
                                <li class="">2. The new password and the confirmation password must match</li>
                                <li class="">3. At least one lowercase and one uppercase character</li>
                                <li class="">4. Include at least one special character, e.g., ! @ # ?</li>
                            </ul>
                        </div>

                        <div class="flex items-center mt-6">
                            <x-filament::button type="submit" id="save-password-info">
                                {{ __('Save changes') }}
                            </x-filament::button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="sm:grid-cols-2 col-span-full lg:col-span-3">
            {{ $this->placeholderInfoForm }}
        </div>
    </div>
</x-filament::page>