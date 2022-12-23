@extends('layouts.app')

@section('content')

<x-navbar />

<section class="relative flex flex-col justify-center px-4 pb-24 mx-auto overflow-hidden md:px-8 pt-36 md:py-48 max-w-8xl bg-gray-50">
    <div class="absolute inset-x-0 flex justify-center w-full -top-1/3 md:-top-3/4 opacity-20">
        <svg class="w-full m-auto md:w-2/3" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 100 100" preserveAspectRatio="xMidYMid">
            <circle cx="50" cy="50" r="0" fill="none" class="stroke-primary-400" stroke-width="3">
                <animate attributeName="r" repeatCount="indefinite" dur="5s" values="0;40" keyTimes="0;1" keySplines="0 0.2 0.8 1" calcMode="spline" begin="0s"></animate>
                <animate attributeName="opacity" repeatCount="indefinite" dur="5s" values="1;0" keyTimes="0;1" keySplines="0.2 0 0.8 1" calcMode="spline" begin="0s"></animate>
            </circle>
            <circle cx="50" cy="50" r="0" fill="none" class="stroke-primary-400" stroke-width="3">
                <animate attributeName="r" repeatCount="indefinite" dur="5s" values="0;40" keyTimes="0;1" keySplines="0 0.2 0.8 1" calcMode="spline" begin="-1s"></animate>
                <animate attributeName="opacity" repeatCount="indefinite" dur="5s" values="1;0" keyTimes="0;1" keySplines="0.2 0 0.8 1" calcMode="spline" begin="-1s"></animate>
            </circle>
            <circle cx="50" cy="50" r="0" fill="none" class="stroke-primary-800" stroke-width="3">
                <animate attributeName="r" repeatCount="indefinite" dur="5s" values="0;40" keyTimes="0;1" keySplines="0 0.2 0.8 1" calcMode="spline" begin="-2s"></animate>
                <animate attributeName="opacity" repeatCount="indefinite" dur="5s" values="1;0" keyTimes="0;1" keySplines="0.2 0 0.8 1" calcMode="spline" begin="-2s"></animate>
            </circle>
        </svg>
    </div>

    <div class="relative z-10 max-w-screen-xl mx-auto md:px-4 lg:items-center lg:flex">
        <div class="max-w-4xl mx-auto text-center">
            <h1 class="text-4xl font-extrabold tracking-wider text-center text-gray-800 sm:text-5xl md:text-7xl">
                World-Class Parking
                <strong>
                    <span class="text-primary-600">
                        Control
                    </span>
                    Platform
                </strong>
            </h1>

            <p class="mt-4 text-xl font-medium text-center text-gray-600 sm:leading-relaxed">
                Regulate the flow of traffic in your parking lots with the power of innovative technology
            </p>

            <div class="flex flex-wrap justify-center gap-4 mt-8">
                <a href="{{ route('register')  }}" class="flex items-center justify-center w-full px-5 py-3 font-medium text-center text-white rounded-lg bg-primary-700 sm:w-max hover:bg-primary-800 focus:ring-4 focus:outline-none focus:ring-primary-300">
                    {{ __('Get started') }}
                </a>
                <a href="{{ route('register')  }}" class="flex items-center justify-center w-full px-5 py-3 font-medium text-center text-white bg-black rounded-lg sm:w-max hover:bg-gray-800 focus:ring-4 focus:outline-none focus:ring-gray-300">
                    Learn more
                    <svg class="w-4 h-4 ml-2 -mr-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                    </svg>
                </a>
            </div>
        </div>
    </div>
</section>

<section class="py-16 text-white bg-gray-900 sm:py-24">
    <div class="container grid grid-cols-1 px-4 m-auto sm:px-6 md:px-12 lg:grid-cols-2 gap-y-8 lg:gap-x-16 lg:items-center">
        <div class="max-w-lg mx-auto my-4 text-center lg:text-left lg:mx-0">
            <h2 class="text-4xl font-bold tracking-tight sm:text-5xl">Find your career path</h2>

            <p class="mt-4 text-lg text-gray-300">
                Lorem ipsum dolor sit amet consectetur adipisicing elit. Aut vero
                aliquid sint distinctio iure ipsum cupiditate? Quis, odit assumenda?
                Deleniti quasi inventore, libero reiciendis minima aliquid tempora.
                Obcaecati, autem.
            </p>

            <a href="{{ route('register')  }}" class=" mt-8 text-white bg-primary-600 hover:bg-primary-800 focus:ring-4 focus:outline-none focus:ring-primary-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center inline-flex items-center">
                Get Started
                <svg class="w-4 h-4 ml-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                </svg>
            </a>
        </div>

        <style>
            .active {
                opacity: 1;
            }

        </style>

        <div class="relative w-full">
            <div class="relative inset-0 z-0 w-full rounded-full bg-primary-600 aspect-w-1 aspect-h-1"></div>

            <div class="absolute top-0 z-10 w-full" x-data="{count: 0}" x-init="
                    $el.children.item(count).classList.add('active');
                    
                    setInterval(() => {
                        let carousel = $el.children;
                        
                        if(count == carousel.length - 1){
                            count = 0;
                        }else {
                            count++;
                        }

                        for (const elem of carousel){
                            elem.classList.toggle('active');
                            
                            if(elem == carousel.item(count)){
                                elem.classList.remove('active');
                            }
                        }
                    }, 8000)">
                <div class="absolute w-full h-full transition-opacity duration-[600ms] ease-in-out opacity-0">
                    <img src="{{ asset('images/macbook-1.png') }}" alt="app screen">
                </div>
                <div class="absolute w-full h-full transition-opacity duration-[600ms] ease-in-out opacity-0">
                    <img src="{{ asset('images/macbook-2.png') }}" alt="app screen">
                </div>
                <div class="absolute w-full h-full transition-opacity duration-[600ms] ease-in-out opacity-0">
                    <img src="{{ asset('images/macbook-3.png') }}" alt="app screen">
                </div>
            </div>
        </div>
    </div>
</section>

<section class="py-16 bg-white sm:py-24">
    <div class="container px-4 mx-auto sm:px-6 md:px-12">
        <div class="grid grid-cols-1 lg:grid-cols-2 lg:h-screen">
            <div class="relative z-10 lg:py-16">
                <div class="relative h-64 overflow-hidden rounded-2xl sm:h-80 lg:h-full">
                    <img class="absolute inset-0 object-cover w-full h-full" src="{{ asset('images/pl-2-sm.jpg') }}" alt="Indoors house" />
                </div>
            </div>

            <div class="relative flex items-center bg-gray-100">
                <span class="hidden lg:inset-y-0 lg:absolute lg:w-16 lg:bg-gray-100 lg:block lg:-left-16"></span>

                <div class="p-8 sm:p-16 lg:p-24">
                    <div class="max-w-xl mx-auto space-y-4 text-center">
                        <h2 class="text-4xl font-bold tracking-tight sm:text-5xl">
                            Manage your parking systems whatever the scale.
                        </h2>

                        <p class="text-lg text-gray-700">
                            No matter how many team members you have - our pricing is simple, transparent and adapts to the size of your company
                        </p>

                        <a href="{{ route('register') }}" class="text-white bg-primary-600 hover:bg-primary-800 focus:ring-4 focus:outline-none focus:ring-primary-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center inline-flex items-center">
                            More Details
                            <svg class="w-4 h-4 ml-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="py-16 sm:py-24 bg-gray-50">
    <div class="container px-4 m-auto space-y-8 text-gray-500 sm:px-6 md:px-12">
        <div>
            <span class="px-2 py-1 text-lg font-semibold rounded-full text-primary-600 bg-primary-200">Main features</span>
            <h2 class="max-w-lg mt-4 text-2xl font-bold text-gray-900 md:text-4xl">A Technology-Powered Approach to Parking Control</h2>
        </div>
        <div class="grid mt-16 overflow-hidden border divide-x divide-y rounded-xl sm:grid-cols-2 lg:divide-y-0 lg:grid-cols-3 xl:grid-cols-4">
            <div class="relative group bg-white transition hover:z-[1] hover:shadow-2xl">
                <div class="relative p-8 space-y-8">
                    <!-- <img src="images/avatars/burger.png" class="w-10" width="512" height="512" alt="burger illustration"> -->

                    <div class="space-y-2">
                        <h5 class="text-xl font-medium text-gray-800 transition group-hover:text-primary-600">First feature</h5>
                        <p class="text-sm text-gray-600">Neque Dolor, fugiat non cum doloribus aperiam voluptates nostrum.</p>
                    </div>
                    <a href="#" class="flex items-center justify-between group-hover:text-primary-600">
                        <span class="text-sm">Read more</span>
                        <span class="text-2xl transition duration-300 -translate-x-4 opacity-0 group-hover:opacity-100 group-hover:translate-x-0">&RightArrow;</span>
                    </a>
                </div>
            </div>
            <div class="relative group bg-white transition hover:z-[1] hover:shadow-2xl">
                <div class="relative p-8 space-y-8">
                    <!-- <img src="images/avatars/trowel.png" class="w-10" width="512" height="512" alt="burger illustration"> -->

                    <div class="space-y-2">
                        <h5 class="text-xl font-medium text-gray-800 transition group-hover:text-primary-600">Second feature</h5>
                        <p class="text-sm text-gray-600">Neque Dolor, fugiat non cum doloribus aperiam voluptates nostrum.</p>
                    </div>
                    <a href="#" class="flex items-center justify-between group-hover:text-primary-600">
                        <span class="text-sm">Read more</span>
                        <span class="text-2xl transition duration-300 -translate-x-4 opacity-0 group-hover:opacity-100 group-hover:translate-x-0">&RightArrow;</span>
                    </a>
                </div>
            </div>
            <div class="relative group bg-white transition hover:z-[1] hover:shadow-2xl">
                <div class="relative p-8 space-y-8">
                    <!-- <img src="images/avatars/package-delivery.png" class="w-10" width="512" height="512" alt="burger illustration"> -->

                    <div class="space-y-2">
                        <h5 class="text-xl font-medium text-gray-800 transition group-hover:text-primary-600">Third feature</h5>
                        <p class="text-sm text-gray-600">Neque Dolor, fugiat non cum doloribus aperiam voluptates nostrum.</p>
                    </div>
                    <a href="#" class="flex items-center justify-between group-hover:text-primary-600">
                        <span class="text-sm">Read more</span>
                        <span class="text-2xl transition duration-300 -translate-x-4 opacity-0 group-hover:opacity-100 group-hover:translate-x-0">&RightArrow;</span>
                    </a>
                </div>
            </div>
            <div class="relative group bg-gray-100 transition hover:z-[1] hover:shadow-2xl lg:hidden xl:block">
                <div class="relative p-8 space-y-8 transition duration-300 border-dashed rounded-lg group-hover:bg-white group-hover:border group-hover:scale-90">
                    <!-- <img src="images/avatars/metal.png" class="w-10" width="512" height="512" alt="burger illustration"> -->

                    <div class="space-y-2">
                        <h5 class="text-xl font-medium text-gray-800 transition group-hover:text-primary-600">More features</h5>
                        <p class="text-sm text-gray-600">Neque Dolor, fugiat non cum doloribus aperiam voluptates nostrum.</p>
                    </div>
                    <a href="#" class="flex items-center justify-between group-hover:text-primary-600">
                        <span class="text-sm">Read more</span>
                        <span class="text-2xl transition duration-300 -translate-x-4 opacity-0 group-hover:opacity-100 group-hover:translate-x-0">&RightArrow;</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="relative py-16 text-white bg-gray-900 sm:py-24">
    <div class="container px-4 mx-auto sm:px-6 md:px-12">
        <div class="max-w-xl mx-auto text-center">
            <h2 class="text-4xl font-bold tracking-tight sm:text-5xl">
                Start for free. Pay as you grow. Switch at any time.
            </h2>

            <p class="mt-4 text-lg text-gray-300">
                Our pricing is simple, transparent and adapts to the size of your organization
            </p>
        </div>

        <x-plan :plans="$plans" />

        <div class="mt-12 text-center">
            <a href="{{ route('plans.pricing') }}" class="text-white bg-primary-600 hover:bg-primary-700 focus:ring-4 focus:outline-none focus:ring-primary-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center inline-flex items-center">
                More Details
                <svg class="w-4 h-4 ml-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                </svg>
            </a>
        </div>
    </div>
</section>

<section class="relative py-16 sm:py-24 bg-gray-50">
    <div class="container px-4 mx-auto sm:px-6 md:px-12">
        <div class="max-w-[38rem] mx-auto text-center">
            <h2 class="text-4xl font-bold tracking-tight sm:text-5xl">
                Why our clients choose us
            </h2>
            <p class="max-w-lg mx-auto mt-4 text-lg text-gray-600">
                From small organizations to medium and large enterprises, our clients
                always recieve the best services.
            </p>
        </div>

        <div class="relative grid grid-cols-1 gap-4 mt-8 before:block before:inset-0 before:bg-center before:bg-contain before:bg-no-repeat before:bg-circles before:absolute before:z-0 md:gap-8 md:mt-16 lg:grid-cols-2">
            <div class="absolute inset-0 backdrop-blur-2xl bg-gray-50/50"></div>

            <div class="relative max-w-5xl px-4 py-8 mx-auto">
                <section class="p-8 bg-white rounded-lg shadow-md">
                    <div class="grid grid-cols-1 gap-10 sm:grid-cols-3 sm:items-center">
                        <div class="relative">
                            <div class="aspect-w-1 aspect-h-1">
                                <img src="https://www.hyperui.dev/photos/man-5.jpeg" alt="" class="object-cover rounded-lg" />
                            </div>

                            <div class="absolute inline-flex p-2 bg-white rounded-lg shadow-xl -bottom-4 -right-4">
                                <svg class="w-10 text-red-700" viewBox="0 0 50 52" xmlns="http://www.w3.org/2000/svg" fill="currentColor">
                                    <title>Laravel</title>
                                    <path d="M49.626 11.564a.809.809 0 0 1 .028.209v10.972a.8.8 0 0 1-.402.694l-9.209 5.302V39.25c0 .286-.152.55-.4.694L20.42 51.01c-.044.025-.092.041-.14.058-.018.006-.035.017-.054.022a.805.805 0 0 1-.41 0c-.022-.006-.042-.018-.063-.026-.044-.016-.09-.03-.132-.054L.402 39.944A.801.801 0 0 1 0 39.25V6.334c0-.072.01-.142.028-.21.006-.023.02-.044.028-.067.015-.042.029-.085.051-.124.015-.026.037-.047.055-.071.023-.032.044-.065.071-.093.023-.023.053-.04.079-.06.029-.024.055-.05.088-.069h.001l9.61-5.533a.802.802 0 0 1 .8 0l9.61 5.533h.002c.032.02.059.045.088.068.026.02.055.038.078.06.028.029.048.062.072.094.017.024.04.045.054.071.023.04.036.082.052.124.008.023.022.044.028.068a.809.809 0 0 1 .028.209v20.559l8.008-4.611v-10.51c0-.07.01-.141.028-.208.007-.024.02-.045.028-.068.016-.042.03-.085.052-.124.015-.026.037-.047.054-.071.024-.032.044-.065.072-.093.023-.023.052-.04.078-.06.03-.024.056-.05.088-.069h.001l9.611-5.533a.801.801 0 0 1 .8 0l9.61 5.533c.034.02.06.045.09.068.025.02.054.038.077.06.028.029.048.062.072.094.018.024.04.045.054.071.023.039.036.082.052.124.009.023.022.044.028.068zm-1.574 10.718v-9.124l-3.363 1.936-4.646 2.675v9.124l8.01-4.611zm-9.61 16.505v-9.13l-4.57 2.61-13.05 7.448v9.216l17.62-10.144zM1.602 7.719v31.068L19.22 48.93v-9.214l-9.204-5.209-.003-.002-.004-.002c-.031-.018-.057-.044-.086-.066-.025-.02-.054-.036-.076-.058l-.002-.003c-.026-.025-.044-.056-.066-.084-.02-.027-.044-.05-.06-.078l-.001-.003c-.018-.03-.029-.066-.042-.1-.013-.03-.03-.058-.038-.09v-.001c-.01-.038-.012-.078-.016-.117-.004-.03-.012-.06-.012-.09v-.002-21.481L4.965 9.654 1.602 7.72zm8.81-5.994L2.405 6.334l8.005 4.609 8.006-4.61-8.006-4.608zm4.164 28.764l4.645-2.674V7.719l-3.363 1.936-4.646 2.675v20.096l3.364-1.937zM39.243 7.164l-8.006 4.609 8.006 4.609 8.005-4.61-8.005-4.608zm-.801 10.605l-4.646-2.675-3.363-1.936v9.124l4.645 2.674 3.364 1.937v-9.124zM20.02 38.33l11.743-6.704 5.87-3.35-8-4.606-9.211 5.303-8.395 4.833 7.993 4.524z" />
                                </svg>
                            </div>
                        </div>

                        <blockquote class="sm:col-span-2">
                            <p class="text-xl font-medium">
                                Lorem ipsum dolor sit amet consectetur adipisicing elit. Sunt
                                perspiciatis cumque neque ut nobis excepturi, quasi iure quisquam
                                autem alias.
                            </p>

                            <cite class="inline-flex items-center mt-8 not-italic">
                                <span class="hidden w-6 h-px bg-gray-400 sm:inline-block"></span>
                                <p class="text-sm text-gray-500 uppercase sm:ml-3">
                                    <strong>Simon Cooper</strong>, Inbetweener Co.
                                </p>
                            </cite>
                        </blockquote>
                    </div>
                </section>
            </div>
            <div class="relative max-w-5xl px-4 mx-auto">
                <section class="p-8 bg-white rounded-lg shadow-md">
                    <div class="grid grid-cols-1 gap-12 sm:grid-cols-3 sm:items-center">
                        <div class="relative">
                            <div class="aspect-w-1 aspect-h-1">
                                <img src="https://www.hyperui.dev/photos/man-5.jpeg" alt="" class="object-cover rounded-lg" />
                            </div>

                            <div class="absolute inline-flex p-2 bg-white rounded-lg shadow-xl -bottom-4 -right-4">
                                <svg class="w-10 text-red-700" viewBox="0 0 50 52" xmlns="http://www.w3.org/2000/svg" fill="currentColor">
                                    <title>Laravel</title>
                                    <path d="M49.626 11.564a.809.809 0 0 1 .028.209v10.972a.8.8 0 0 1-.402.694l-9.209 5.302V39.25c0 .286-.152.55-.4.694L20.42 51.01c-.044.025-.092.041-.14.058-.018.006-.035.017-.054.022a.805.805 0 0 1-.41 0c-.022-.006-.042-.018-.063-.026-.044-.016-.09-.03-.132-.054L.402 39.944A.801.801 0 0 1 0 39.25V6.334c0-.072.01-.142.028-.21.006-.023.02-.044.028-.067.015-.042.029-.085.051-.124.015-.026.037-.047.055-.071.023-.032.044-.065.071-.093.023-.023.053-.04.079-.06.029-.024.055-.05.088-.069h.001l9.61-5.533a.802.802 0 0 1 .8 0l9.61 5.533h.002c.032.02.059.045.088.068.026.02.055.038.078.06.028.029.048.062.072.094.017.024.04.045.054.071.023.04.036.082.052.124.008.023.022.044.028.068a.809.809 0 0 1 .028.209v20.559l8.008-4.611v-10.51c0-.07.01-.141.028-.208.007-.024.02-.045.028-.068.016-.042.03-.085.052-.124.015-.026.037-.047.054-.071.024-.032.044-.065.072-.093.023-.023.052-.04.078-.06.03-.024.056-.05.088-.069h.001l9.611-5.533a.801.801 0 0 1 .8 0l9.61 5.533c.034.02.06.045.09.068.025.02.054.038.077.06.028.029.048.062.072.094.018.024.04.045.054.071.023.039.036.082.052.124.009.023.022.044.028.068zm-1.574 10.718v-9.124l-3.363 1.936-4.646 2.675v9.124l8.01-4.611zm-9.61 16.505v-9.13l-4.57 2.61-13.05 7.448v9.216l17.62-10.144zM1.602 7.719v31.068L19.22 48.93v-9.214l-9.204-5.209-.003-.002-.004-.002c-.031-.018-.057-.044-.086-.066-.025-.02-.054-.036-.076-.058l-.002-.003c-.026-.025-.044-.056-.066-.084-.02-.027-.044-.05-.06-.078l-.001-.003c-.018-.03-.029-.066-.042-.1-.013-.03-.03-.058-.038-.09v-.001c-.01-.038-.012-.078-.016-.117-.004-.03-.012-.06-.012-.09v-.002-21.481L4.965 9.654 1.602 7.72zm8.81-5.994L2.405 6.334l8.005 4.609 8.006-4.61-8.006-4.608zm4.164 28.764l4.645-2.674V7.719l-3.363 1.936-4.646 2.675v20.096l3.364-1.937zM39.243 7.164l-8.006 4.609 8.006 4.609 8.005-4.61-8.005-4.608zm-.801 10.605l-4.646-2.675-3.363-1.936v9.124l4.645 2.674 3.364 1.937v-9.124zM20.02 38.33l11.743-6.704 5.87-3.35-8-4.606-9.211 5.303-8.395 4.833 7.993 4.524z" />
                                </svg>
                            </div>
                        </div>

                        <blockquote class="sm:col-span-2">
                            <p class="text-xl font-medium">
                                Lorem ipsum dolor sit amet consectetur adipisicing elit. Sunt
                                perspiciatis cumque neque ut nobis excepturi, quasi iure quisquam
                                autem alias.
                            </p>

                            <cite class="inline-flex items-center mt-8 not-italic">
                                <span class="hidden w-6 h-px bg-gray-400 sm:inline-block"></span>
                                <p class="text-sm text-gray-500 uppercase sm:ml-3">
                                    <strong>Simon Cooper</strong>, Inbetweener Co.
                                </p>
                            </cite>
                        </blockquote>
                    </div>
                </section>
            </div>
        </div>
    </div>
</section>

<section class="py-16 overflow-hidden md:py-24 md:pt-0 bg-gray-50">
    <div class="container px-4 m-auto space-y-8 sm:px-6 md:px-12">
        <div class="flex flex-col items-center justify-between px-8 py-16 overflow-hidden bg-primary-600 md:px-12 md:flex-row rounded-xl">
            <div class="mb-6 md:mb-0">
                <h2 class="text-3xl font-bold text-center text-white md:text-left md:text-4xl">Ready to get started?</h2>
                <p class="max-w-md mt-2 text-lg font-semibold text-center text-primary-200 md:text-left">We have a generous Standard tier available to get you started right away.</p>
            </div>

            <div>
                <a href="{{ route('register')  }}" class="inline-flex items-center px-8 py-3 text-lg font-semibold text-center bg-white rounded-lg text-primary-600 hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-primary-300">
                    Try Smart-tapp for free
                    <svg class="w-4 h-4 ml-2 -mr-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                    </svg>
                </a>
            </div>
        </div>
    </div>
</section>

<x-footer />

@endsection
