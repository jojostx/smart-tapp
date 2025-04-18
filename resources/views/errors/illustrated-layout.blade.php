<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>@yield('title')</title>

    <!-- Styles -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>

<body class="antialiased">
    <div class="relative flex items-center justify-center w-screen h-screen min-h-screen bg-gray-100 dark:bg-gray-900 sm:items-center sm:pt-0">
        <div class="container flex flex-col items-center justify-center px-5 text-gray-700 md:flex-row">
            <div class="flex justify-center max-w-md sm:max-w-lg">
                {{-- @todo make it work in dark mode --}}
                <svg viewBox="0 0 800 600" xmlns="http://www.w3.org/2000/svg">
                    <defs>
                        <clipPath id="a">
                            <path d="m380.86 346.16c-1.247 4.651-4.668 8.421-9.196 10.06-9.332 3.377-26.2 7.817-42.301 3.5s-28.485-16.599-34.877-24.192c-3.101-3.684-4.177-8.66-2.93-13.311l7.453-27.798c0.756-2.82 3.181-4.868 6.088-5.13 6.755-0.61 20.546-0.608 41.785 5.087s33.181 12.591 38.725 16.498c2.387 1.682 3.461 4.668 2.705 7.488l-7.452 27.798z" />
                        </clipPath>
                    </defs>
                    <g fill="none" stroke="#fcfcfc" stroke-miterlimit="10" stroke-width="3">
                        <circle cx="572.86" cy="108.8" r="90.788" />
                        <circle cx="548.89" cy="62.319" r="13.074" />
                        <circle cx="591.74" cy="158.92" r="7.989" />
                        <path d="m476.56 101.46c-30.404 2.164-49.691 4.221-49.691 8.007 0 6.853 63.166 12.408 141.08 12.408s141.08-5.555 141.08-12.408c0-3.378-15.347-4.988-40.243-7.225" stroke-linecap="round" />
                        <path d="m483.98 127.43c23.462 1.531 52.515 2.436 83.972 2.436 36.069 0 68.978-1.19 93.922-3.149" opacity=".5" stroke-linecap="round" />
                    </g>
                    <g fill="none" stroke="#fcfcfc" stroke-linecap="round" stroke-miterlimit="10" stroke-width="3">
                        <line x1="518.07" x2="518.07" y1="245.38" y2="266.58" />
                        <line x1="508.13" x2="528.01" y1="255.98" y2="255.98" />
                        <line x1="154.55" x2="154.55" y1="231.39" y2="252.6" />
                        <line x1="144.61" x2="164.49" y1="242" y2="242" />
                        <line x1="320.14" x2="320.14" y1="132.75" y2="153.95" />
                        <line x1="310.19" x2="330.08" y1="143.35" y2="143.35" />
                        <line x1="200.67" x2="200.67" y1="483.11" y2="504.32" />
                        <line x1="210.61" x2="190.73" y1="493.71" y2="493.71" />
                    </g>
                    <g fill="none" stroke="#fcfcfc" stroke-linecap="round" stroke-miterlimit="10" stroke-width="3">
                        <line x1="432.17" x2="432.17" y1="380.52" y2="391.83" />
                        <line x1="426.87" x2="437.47" y1="386.18" y2="386.18" />
                        <line x1="489.56" x2="489.56" y1="299.76" y2="308.12" />
                        <line x1="485.64" x2="493.47" y1="303.94" y2="303.94" />
                        <line x1="231.47" x2="231.47" y1="291.01" y2="299.37" />
                        <line x1="227.55" x2="235.39" y1="295.19" y2="295.19" />
                        <line x1="244.03" x2="244.03" y1="547.54" y2="555.9" />
                        <line x1="247.95" x2="240.11" y1="551.72" y2="551.72" />
                        <line x1="186.36" x2="186.36" y1="406.97" y2="415.33" />
                        <line x1="190.28" x2="182.44" y1="411.15" y2="411.15" />
                        <line x1="480.3" x2="480.3" y1="406.97" y2="415.33" />
                        <line x1="484.22" x2="476.38" y1="411.15" y2="411.15" />
                    </g>
                    <g fill="none" stroke="#fcfcfc" stroke-linecap="round" stroke-miterlimit="10" stroke-width="3">
                        <circle cx="588.98" cy="255.98" r="7.952" />
                        <circle cx="450.07" cy="320.26" r="7.952" />
                        <circle cx="168.3" cy="353.75" r="7.952" />
                        <circle cx="429.52" cy="201.18" r="7.952" />
                        <circle cx="200.67" cy="176.31" r="7.952" />
                        <circle cx="133.34" cy="477.01" r="7.952" />
                        <circle cx="283.52" cy="568.03" r="7.952" />
                        <circle cx="413.62" cy="482.39" r="7.952" />
                    </g>
                    <g fill="#fcfcfc">
                        <circle cx="549.88" cy="296.4" r="2.651" />
                        <circle cx="253.29" cy="229.24" r="2.651" />
                        <circle cx="434.82" cy="263.93" r="2.651" />
                        <circle cx="183.71" cy="544.18" r="2.651" />
                        <circle cx="382.52" cy="530.92" r="2.651" />
                        <circle cx="130.69" cy="305.61" r="2.651" />
                        <circle cx="480.3" cy="477.01" r="2.651" />
                    </g>
                    <g clip-path="url(cordClip)">
                        <path d="m273.81 410.97s-54.527 39.501-115.34 38.218c-2.28-0.048-4.926-0.241-7.841-0.548-68.038-7.178-134.29-43.963-167.33-103.87-0.908-1.646-1.793-3.3-2.654-4.964-18.395-35.511-37.259-83.385-32.075-118.82" fill="none" stroke="#fcfcfc" stroke-linecap="round" stroke-linejoin="round" stroke-miterlimit="10" stroke-width="3" />
                        <path d="m338.16 454.69-64.726-17.353c-11.086-2.972-17.664-14.369-14.692-25.455l15.694-58.537c3.889-14.504 18.799-23.11 33.303-19.221l52.349 14.035c14.504 3.889 23.11 18.799 19.221 33.303l-15.694 58.537c-2.972 11.085-14.368 17.663-25.455 14.691z" fill="#fff" stroke="#0E0620" stroke-linecap="round" stroke-linejoin="round" stroke-miterlimit="10" stroke-width="3" />
                        <g fill="#fff" stroke="#fcfcfc" stroke-linecap="round" stroke-linejoin="round" stroke-miterlimit="10" stroke-width="3">
                            <line x1="323.4" x2="295.28" y1="236.62" y2="353.75" />
                            <circle cx="323.67" cy="235.62" r="6.375" />
                        </g>
                        <g fill="#fff" stroke="#0E0620" stroke-linecap="round" stroke-linejoin="round" stroke-miterlimit="10" stroke-width="3">
                            <path d="m360.63 363.04c1.352 1.061 4.91 5.056 5.824 6.634l27.874 47.634c3.855 6.649 1.59 15.164-5.059 19.02-6.649 3.855-15.164 1.59-19.02-5.059l-5.603-9.663" />
                            <path d="m388.76 434.68c5.234-3.039 7.731-8.966 6.678-14.594 2.344 1.343 4.383 3.289 5.837 5.793 4.411 7.596 1.829 17.33-5.767 21.741s-17.33 1.829-21.741-5.767c-1.754-3.021-2.817-5.818-2.484-9.046 4.34 4.551 11.802 5.169 17.477 1.873z" />
                        </g>
                        <g fill="#fff" stroke="#0E0620" stroke-linecap="round" stroke-linejoin="round" stroke-miterlimit="10" stroke-width="3">
                            <path d="m301.3 347.66c-1.702 0.242-5.91 1.627-7.492 2.536l-47.965 27.301c-6.664 3.829-8.963 12.335-5.134 18.999s12.335 8.963 18.999 5.134l9.685-5.564" />
                            <path d="m241.98 395.32c-3.012-5.25-2.209-11.631 1.518-15.977-2.701-9e-3 -5.44 0.656-7.952 2.096-7.619 4.371-10.253 14.09-5.883 21.71 4.371 7.619 14.09 10.253 21.709 5.883 3.03-1.738 5.35-3.628 6.676-6.59-6.033 1.768-12.803-1.429-16.068-7.122z" />
                        </g>
                        <g stroke="#0E0620" stroke-linecap="round" stroke-linejoin="round" stroke-miterlimit="10" stroke-width="3">
                            <path d="m353.35 365.39c-7.948 1.263-16.249 0.929-24.48-1.278-8.232-2.207-15.586-6.07-21.836-11.14-17.004 4.207-31.269 17.289-36.128 35.411l-1.374 5.123c-7.112 26.525 8.617 53.791 35.13 60.899s53.771-8.632 60.883-35.158l1.374-5.123c4.858-18.122-0.949-36.585-13.569-48.734z" fill="#fff" />
                            <path d="m269.68 394.91c26.3 20.643 59.654 29.585 93.106 25.724l2.419-0.114" fill="none" />
                        </g>
                        <g stroke="#0E0620" stroke-linecap="round" stroke-linejoin="round" stroke-miterlimit="10" stroke-width="3">
                            <path d="m312.96 456.73-14.315 53.395c-1.896 7.07 2.299 14.338 9.37 16.234 7.07 1.896 14.338-2.299 16.234-9.37l17.838-66.534c-8.633 5.427-18.558 6.928-29.127 6.275z" fill="#fff" />
                            <line x1="304.88" x2="330.49" y1="486.85" y2="493.71" fill="none" />
                            <path d="m296.32 452.27-14.315 53.394c-1.896 7.07-9.164 11.265-16.234 9.37-7.07-1.896-11.265-9.164-9.37-16.234l17.838-66.534c4.759 9.017 12.602 15.281 22.081 20.004z" fill="#fff" />
                            <line x1="262.64" x2="288.24" y1="475.52" y2="482.39" fill="none" />
                        </g>
                        <ellipse transform="matrix(.259 -.9659 .9659 .259 -51.544 563.24)" cx="341.3" cy="315.21" rx="61.961" ry="60.305" fill="#fff" stroke="#0E0620" stroke-linecap="round" stroke-linejoin="round" stroke-miterlimit="10" stroke-width="3" />
                        <path d="m330.87 261.34c-7.929 1.72-15.381 5.246-21.799 10.246" fill="none" stroke="#0E0620" stroke-linecap="round" stroke-linejoin="round" stroke-miterlimit="10" stroke-width="3" />
                        <path d="m380.86 346.16c-1.247 4.651-4.668 8.421-9.196 10.06-9.332 3.377-26.2 7.817-42.301 3.5s-28.485-16.599-34.877-24.192c-3.101-3.684-4.177-8.66-2.93-13.311l7.453-27.798c0.756-2.82 3.181-4.868 6.088-5.13 6.755-0.61 20.546-0.608 41.785 5.087s33.181 12.591 38.725 16.498c2.387 1.682 3.461 4.668 2.705 7.488l-7.452 27.798z" fill="#64748b" stroke="#0E0620" stroke-linecap="round" stroke-linejoin="round" stroke-miterlimit="10" stroke-width="3" />
                        <g clip-path="url(#a)">
                            <polygon points="278.44 375.6 383 264.08 364.39 251.62 264.81 364.93" fill="none" stroke="#0E0620" stroke-miterlimit="10" stroke-width="3" />
                        </g>
                    </g>
                </svg>
            </div>
            <div class="flex flex-col items-center max-w-md sm:block">
                <div class="mb-2 font-bold text-gray-900 border-r dark:text-gray-500 text-7xl sm:mb-3">@yield('code')</div>

                <div class="space-y-2 text-lg text-center text-gray-900 border-r dark:text-gray-300 sm:text-left">
                    @yield('message')
                </div>

                <div class="mt-4 sm:mt-6">
                    <a href="{{ config('app.url') }}" class="inline px-4 py-2 text-sm font-medium leading-5 text-white transition-colors duration-150 border border-transparent rounded-lg shadow bg-primary-600 dark:text-primary-50 focus:outline-none focus:shadow-outline-primary active:bg-primary-600 hover:bg-primary-700">
                        Back to homepage
                    </a>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
