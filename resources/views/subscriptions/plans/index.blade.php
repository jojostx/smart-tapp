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
                Simple pricing for
                <strong>
                    <span class="text-primary-600">
                        Optimal
                    </span>
                    operation
                </strong>
            </h1>

            <p class="mt-4 text-xl font-medium text-center text-gray-600 sm:leading-relaxed">
                Regulate the flow of traffic in your parking lots with the power of innovative technology
            </p>
        </div>
    </div>
</section>

<section class="relative py-16 text-white bg-gray-900 sm:py-24">
    <div class="container px-4 mx-auto sm:px-6 md:px-12">
        <div class="max-w-xl mx-auto text-center">
            <h2 class="text-4xl font-bold tracking-tight sm:text-5xl">
                Choose Your Plan
            </h2>

            <p class="mt-4 text-lg text-gray-300">
                Our pricing is simple, transparent and adapts to the size of your organization
            </p>
        </div>

        <x-plan :plans="$plans" :group-by-interval="true" />
    </div>
</section>

<x-cta-card />

<x-footer />

<!-- <a href='https://www.freepik.com/vectors/parking'>Parking vector created by macrovector - www.freepik.com</a> -->
<!-- Photo by <a href="https://unsplash.com/@carlesrgm?utm_source=unsplash&utm_medium=referral&utm_content=creditCopyText">Carles Rabada</a> on <a href="https://unsplash.com/s/photos/parking-lot?utm_source=unsplash&utm_medium=referral&utm_content=creditCopyText">Unsplash</a> -->
<!-- Photo by <a href="https://unsplash.com/@hydngallery?utm_source=unsplash&utm_medium=referral&utm_content=creditCopyText">Haidan</a> on <a href="https://unsplash.com/s/photos/parking-lot?utm_source=unsplash&utm_medium=referral&utm_content=creditCopyText">Unsplash</a>    -->
<!-- Photo by <a href="https://unsplash.com/@von_co?utm_source=unsplash&utm_medium=referral&utm_content=creditCopyText">Ivana Cajina</a> on <a href="https://unsplash.com/s/photos/parking-lot?utm_source=unsplash&utm_medium=referral&utm_content=creditCopyText">Unsplash</a> -->
<!-- Photo by <a href="https://unsplash.com/@ryansearle?utm_source=unsplash&utm_medium=referral&utm_content=creditCopyText">Ryan Searle</a> on <a href="https://unsplash.com/s/photos/parking-lot?utm_source=unsplash&utm_medium=referral&utm_content=creditCopyText">Unsplash</a>-->
@endsection
