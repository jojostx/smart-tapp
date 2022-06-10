<div class="relative py-12">
    <img src="images/bg2.webp" class="fixed inset-0 rotate-0 blur-xl" alt="background">

    <div class="container relative px-6 m-auto text-gray-500 md:px-12 xl:px-40">
        <div class="m-auto space-y-8 md:w-8/12 lg:w-6/12 xl:w-6/12">
            {{ $logo ?? '' }}

            <div class="w-full px-6 py-6 mt-4 overflow-hidden bg-white shadow-md backdrop-blur-2xl bg-opacity-80 sm:max-w-md sm:rounded-lg">
                {{ $slot }}
            </div>
            <div class="space-x-4 text-center md:text-white">
                <span>© {{ config('app.name') }}</span>
                <a href="#" class="text-sm hover:text-indigo-100 hover:underline">Contact</a>
                <a href="#" class="text-sm hover:text-indigo-100 hover:underline">Privacy &amp; Terms</a>
            </div>
        </div>
    </div>
</div>