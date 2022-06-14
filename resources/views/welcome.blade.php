@extends('layouts.app')

@section('content')

<nav class="absolute z-10 flex items-center content-center justify-between w-full px-4 py-6 lg:px-24 md:px-12">
    <a href="{{ route('home') }}" class="flex items-center">
        <x-logo class="w-auto mr-3 text-indigo-600 h-9" alt="{{ config('app.name') }} Logo" />
        <span class="self-center hidden text-xl font-semibold whitespace-nowrap md:inline">{{ config('app.name') }}</span>
    </a>
    <ul class="items-center hidden md:flex">
        <li class="mx-3">
            <a href="features">Features</a>
        </li>
        <li class="mx-3">
            <a href="pricing">Pricing</a>
        </li>
        <li class="mx-3">
            <a href="company">Company</a>
        </li>
    </ul>
    <div class="hidden ml-auto sm:ml-0 md:block">
        <button class="mr-6 opacity-0 pointer-events-none">Login</button>
        <a href="{{ route('register')  }}" class="text-white bg-black hover:bg-gray-800 focus:ring-4 focus:outline-none focus:ring-gray-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center inline-flex items-center">
            Sign up
            <svg class="w-4 h-4 ml-2 -mr-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd"></path>
            </svg>
        </a>
    </div>
    <div id="showMenu" class="ml-2 md:hidden">
        <button data-collapse-toggle="mobile-menu-4" type="button" class="inline-flex items-center p-2 text-sm text-gray-500 rounded-lg md:hidden hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200" aria-controls="mobile-menu-4" aria-expanded="false">
            <span class="sr-only">Open main menu</span>
            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" d="M3 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 10a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 15a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"></path>
            </svg>
            <svg class="hidden w-6 h-6" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
            </svg>
        </button>
    </div>
</nav>

<section class="relative flex flex-col justify-center bg-gray-50 sm:px-6 lg:px-8">
    <div class="absolute inset-x-0 flex justify-center w-full -top-16 opacity-20">
        <svg class="w-full md:w-2/3 motion-safe:animate-pulse-slow" viewBox="0 0 1082 436" version="1.1" xmlns="http://www.w3.org/2000/svg">
            <g id="semicircles" transform="translate(0 -646)">
                <path d="M0 541C0 467.622 14.1716 397.428 42.5149 330.418C69.893 265.694 108.54 208.373 158.455 158.455C208.373 108.538 265.694 69.8917 330.418 42.5149C397.431 14.1716 467.625 0 541 0C614.375 0 684.569 14.1716 751.582 42.5149C816.298 69.8881 873.619 108.535 923.545 158.455C973.452 208.37 1012.1 265.691 1039.49 330.418C1067.83 397.431 1082 467.625 1082 541C1082 614.37 1067.83 684.564 1039.49 751.582C1012.11 816.314 973.463 873.635 923.545 923.545C873.619 973.468 816.298 1012.11 751.582 1039.49C684.564 1067.83 614.37 1082 541 1082C467.625 1082 397.431 1067.83 330.418 1039.49C265.697 1012.11 208.376 973.463 158.455 923.545C108.537 873.63 69.8904 816.309 42.5149 751.582C14.1716 684.569 0 614.375 0 541L0 541ZM2 541C2 614.109 16.119 684.044 44.3569 750.803C71.6294 815.288 110.134 872.398 159.869 922.131C209.608 971.866 266.717 1010.37 331.197 1037.64C397.967 1065.88 467.901 1080 541 1080C614.104 1080 684.039 1065.88 750.803 1037.64C815.278 1010.38 872.387 971.872 922.13 922.131C971.866 872.398 1010.37 815.288 1037.64 750.803C1065.88 684.036 1080 614.102 1080 541C1080 467.896 1065.88 397.962 1037.64 331.197C1010.37 266.709 971.866 209.6 922.13 159.869C872.392 110.133 815.283 71.6287 750.803 44.3569C684.039 16.119 614.104 2 541 2C467.896 2 397.962 16.119 331.197 44.3569C266.712 71.632 209.602 110.136 159.869 159.869C110.136 209.602 71.632 266.712 44.3569 331.197C16.119 397.956 2 467.891 2 541L2 541Z" id="circle_1" fill="#710C76" fill-rule="evenodd" stroke="none" />
                <path d="M40 540C40 472.048 53.1238 407.044 79.3715 344.988C104.725 285.049 140.515 231.967 186.74 185.74C232.967 139.513 286.049 103.724 345.988 78.3715C408.046 52.1238 473.05 39 541 39C608.95 39 673.954 52.1238 736.012 78.3714C795.943 103.721 849.026 139.51 895.26 185.74C941.478 231.964 977.268 285.047 1002.63 344.988C1028.88 407.046 1042 472.05 1042 540C1042 607.945 1028.88 672.949 1002.63 735.012C977.277 794.958 941.488 848.041 895.26 894.261C849.026 940.493 795.943 976.282 736.012 1001.63C673.949 1027.88 608.945 1041 541 1041C473.05 1041 408.046 1027.88 345.988 1001.63C286.052 976.277 232.969 940.488 186.74 894.261C140.512 848.036 104.723 794.953 79.3715 735.012C53.1238 672.954 40 607.95 40 540L40 540ZM41.8517 540C41.8517 607.704 54.9267 672.467 81.0768 734.291C106.333 794.008 141.99 846.895 188.049 892.951C234.109 939.01 286.996 974.667 346.709 999.923C408.542 1026.07 473.305 1039.15 540.999 1039.15C608.698 1039.15 673.462 1026.07 735.29 999.923C794.998 974.672 847.885 939.014 893.95 892.951C940.009 846.895 975.666 794.008 1000.92 734.291C1027.07 672.46 1040.15 607.697 1040.15 540C1040.15 472.301 1027.07 407.537 1000.92 345.709C975.666 285.989 940.009 233.103 893.95 187.049C847.89 140.99 795.003 105.333 735.29 80.0773C673.462 53.9272 608.698 40.8521 540.999 40.8521C473.3 40.8521 408.537 53.9272 346.709 80.0773C286.991 105.336 234.104 140.993 188.049 187.049C141.993 233.105 106.335 285.992 81.0768 345.709C54.9267 407.533 41.8517 472.296 41.8517 540L41.8517 540Z" id="circle_2" fill="#770877" fill-rule="evenodd" stroke="none" />
                <path d="M80 541C80 478.473 92.076 418.659 116.228 361.558C139.558 306.405 172.49 257.56 215.024 215.024C257.56 172.488 306.405 139.556 361.558 116.228C418.661 92.076 478.475 80 541 80C603.525 80 663.339 92.076 720.442 116.228C775.589 139.553 824.433 172.485 866.976 215.024C909.504 257.558 942.436 306.403 965.772 361.558C989.924 418.661 1002 478.475 1002 541C1002 603.52 989.924 663.334 965.772 720.442C942.445 775.602 909.513 824.447 866.976 866.976C824.433 909.517 775.589 942.449 720.442 965.772C663.334 989.924 603.52 1002 541 1002C478.475 1002 418.661 989.924 361.558 965.772C306.407 942.445 257.562 909.513 215.024 866.976C172.487 824.442 139.555 775.597 116.228 720.442C92.076 663.339 80 603.525 80 541L80 541ZM81.7039 541C81.7039 603.298 93.735 662.891 117.797 719.779C141.037 774.728 173.847 823.393 216.228 865.771C258.612 908.152 307.276 940.963 362.221 964.202C419.117 988.265 478.71 1000.3 541 1000.3C603.294 1000.3 662.886 988.265 719.778 964.202C774.719 940.967 823.383 908.157 865.771 865.771C908.152 823.393 940.962 774.728 964.202 719.779C988.264 662.885 1000.3 603.292 1000.3 541C1000.3 478.706 988.264 419.113 964.202 362.222C940.962 307.27 908.152 258.605 865.771 216.229C823.388 173.847 774.723 141.037 719.778 117.798C662.886 93.7353 603.294 81.7042 541 81.7042C478.706 81.7042 419.113 93.7353 362.221 117.798C307.271 141.039 258.607 173.85 216.228 216.229C173.849 258.608 141.039 307.272 117.797 362.222C93.735 419.109 81.7039 478.702 81.7039 541L81.7039 541Z" id="circle_3" fill="#801B1B" fill-rule="evenodd" stroke="none" />
                <path d="M120 540C120 482.898 131.028 428.274 153.085 376.128C174.39 325.76 204.464 281.154 243.308 242.308C282.154 203.463 326.76 173.389 377.128 152.085C429.276 130.028 483.9 119 541 119C598.1 119 652.724 130.028 704.872 152.085C755.234 173.386 799.84 203.461 838.692 242.308C877.53 281.152 907.604 325.758 928.915 376.128C950.972 428.276 962 482.9 962 540C962 597.096 950.972 651.72 928.915 703.872C907.612 754.246 877.538 798.853 838.692 837.692C799.84 876.542 755.234 906.616 704.872 927.915C652.72 949.972 598.096 961 541 961C483.9 961 429.276 949.972 377.128 927.915C326.762 906.612 282.156 876.538 243.308 837.692C204.462 798.849 174.388 754.242 153.085 703.872C131.028 651.724 120 597.1 120 540L120 540ZM121.556 540C121.556 596.893 132.543 651.315 154.517 703.266C175.74 753.448 205.704 797.89 244.408 836.591C283.113 875.295 327.555 905.259 377.733 926.482C429.692 948.456 484.115 959.444 540.999 959.444C597.888 959.444 652.31 948.456 704.265 926.482C754.439 905.263 798.881 875.299 837.591 836.591C876.295 797.89 906.258 753.448 927.481 703.266C949.456 651.309 960.443 596.887 960.443 540C960.443 483.111 949.456 428.689 927.481 376.734C906.258 326.55 876.295 282.108 837.591 243.408C798.885 204.704 754.443 174.74 704.265 153.518C652.31 131.543 597.888 120.556 540.999 120.556C484.11 120.556 429.688 131.543 377.733 153.518C327.551 174.743 283.109 204.707 244.408 243.408C205.706 282.11 175.742 326.552 154.517 376.734C132.543 428.685 121.556 483.107 121.556 540L121.556 540Z" id="circle_4" fill="#6F3702" fill-rule="evenodd" stroke="none" />
                <path d="M160 541C160 489.324 169.98 439.889 189.941 392.697C209.222 347.115 236.439 306.747 271.592 271.592C306.747 236.438 347.115 209.221 392.697 189.941C439.891 169.98 489.326 160 541 160C592.674 160 642.109 169.98 689.303 189.941C734.879 209.219 775.247 236.436 810.408 271.592C845.555 306.745 872.772 347.114 892.059 392.697C912.02 439.891 922 489.326 922 541C922 592.671 912.02 642.105 892.059 689.303C872.78 734.89 845.563 775.258 810.408 810.408C775.247 845.566 734.879 872.783 689.303 892.059C642.105 912.02 592.671 922 541 922C489.326 922 439.891 912.02 392.697 892.059C347.117 872.78 306.749 845.563 271.592 810.408C236.437 775.255 209.22 734.886 189.941 689.303C169.98 642.109 160 592.674 160 541L160 541ZM161.408 541C161.408 592.488 171.352 641.739 191.238 688.754C210.445 734.168 237.562 774.387 272.588 809.412C307.616 844.438 347.836 871.555 393.246 890.762C440.269 910.648 489.52 920.592 541 920.592C592.484 920.592 641.735 910.648 688.754 890.762C734.161 871.559 774.38 844.442 809.412 809.412C844.438 774.387 871.555 734.168 890.762 688.754C910.648 641.733 920.592 592.482 920.592 541C920.592 489.516 910.648 440.265 890.762 393.246C871.555 347.83 844.438 307.611 809.412 272.588C774.384 237.561 734.164 210.445 688.754 191.238C641.735 171.352 592.484 161.408 541 161.408C489.516 161.408 440.265 171.352 393.246 191.238C347.832 210.447 307.613 237.564 272.588 272.588C237.564 307.613 210.447 347.832 191.238 393.246C171.352 440.261 161.408 489.513 161.408 541L161.408 541Z" id="circle_5" fill="#560600" fill-rule="evenodd" stroke="none" />
            </g>
        </svg>
    </div>

    <div class="max-w-screen-xl px-4 pb-24 mx-auto pt-36 lg:h-screen lg:items-center lg:flex">
        <div class="max-w-4xl mx-auto text-center">
            <h1 class="text-5xl font-extrabold tracking-wider text-center text-gray-800 md:text-7xl">
                World-Class Parking
                <strong>
                    <span class="text-indigo-600">
                        Control
                    </span>
                    Platform
                </strong>
            </h1>

            <p class="mt-4 text-xl font-medium text-center text-gray-600 sm:leading-relaxed">
                Regulate the flow of traffic in your parking lots with the power of innovative technology
            </p>

            <div class="flex flex-wrap justify-center gap-4 mt-8">
                <a href="{{ route('register')  }}" class="block w-full px-5 py-3 font-medium text-white transition duration-150 ease-in-out bg-indigo-600 border border-transparent rounded-lg sm:w-max hover:bg-indigo-500 focus:outline-none focus:border-indigo-700 focus:ring-indigo active:bg-indigo-700">
                    Get started
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
        <div class="max-w-lg mx-auto text-center lg:text-left lg:mx-0">
            <h2 class="text-4xl font-bold tracking-tight sm:text-5xl">Find your career path</h2>

            <p class="mt-4 text-lg text-gray-300">
                Lorem ipsum dolor sit amet consectetur adipisicing elit. Aut vero
                aliquid sint distinctio iure ipsum cupiditate? Quis, odit assumenda?
                Deleniti quasi inventore, libero reiciendis minima aliquid tempora.
                Obcaecati, autem.
            </p>

            <a href="{{ route('register')  }}" class=" mt-8 text-white bg-indigo-600 hover:bg-indigo-800 focus:ring-4 focus:outline-none focus:ring-indigo-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center inline-flex items-center">
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
            <div class="relative inset-0 z-0 w-full bg-indigo-600 rounded-full aspect-w-1 aspect-h-1"></div>

            <div class="absolute top-0 z-10 w-full" 
                 x-data="{count: 0}"
                 x-init="
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
                    }, 8000)"
                >
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
    <div class="container px-4 mx-auto sm:px-6 lg:px-8">
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

                        <a href="{{ route('register')  }}" class="text-white bg-indigo-600 hover:bg-indigo-800 focus:ring-4 focus:outline-none focus:ring-indigo-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center inline-flex items-center">
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
    <div class="container px-4 m-auto space-y-8 text-gray-500 md:px-12">
        <div>
            <span class="px-2 py-1 text-lg font-semibold text-indigo-600 bg-indigo-200 rounded-full">Main features</span>
            <h2 class="max-w-lg mt-4 text-2xl font-bold text-gray-900 md:text-4xl">A Technology-Powered Approach to Parking Control</h2>
        </div>
        <div class="grid mt-16 overflow-hidden border divide-x divide-y rounded-xl sm:grid-cols-2 lg:divide-y-0 lg:grid-cols-3 xl:grid-cols-4">
            <div class="relative group bg-white transition hover:z-[1] hover:shadow-2xl">
                <div class="relative p-8 space-y-8">
                    <!-- <img src="images/avatars/burger.png" class="w-10" width="512" height="512" alt="burger illustration"> -->

                    <div class="space-y-2">
                        <h5 class="text-xl font-medium text-gray-800 transition group-hover:text-indigo-600">First feature</h5>
                        <p class="text-sm text-gray-600">Neque Dolor, fugiat non cum doloribus aperiam voluptates nostrum.</p>
                    </div>
                    <a href="#" class="flex items-center justify-between group-hover:text-indigo-600">
                        <span class="text-sm">Read more</span>
                        <span class="text-2xl transition duration-300 -translate-x-4 opacity-0 group-hover:opacity-100 group-hover:translate-x-0">&RightArrow;</span>
                    </a>
                </div>
            </div>
            <div class="relative group bg-white transition hover:z-[1] hover:shadow-2xl">
                <div class="relative p-8 space-y-8">
                    <!-- <img src="images/avatars/trowel.png" class="w-10" width="512" height="512" alt="burger illustration"> -->

                    <div class="space-y-2">
                        <h5 class="text-xl font-medium text-gray-800 transition group-hover:text-indigo-600">Second feature</h5>
                        <p class="text-sm text-gray-600">Neque Dolor, fugiat non cum doloribus aperiam voluptates nostrum.</p>
                    </div>
                    <a href="#" class="flex items-center justify-between group-hover:text-indigo-600">
                        <span class="text-sm">Read more</span>
                        <span class="text-2xl transition duration-300 -translate-x-4 opacity-0 group-hover:opacity-100 group-hover:translate-x-0">&RightArrow;</span>
                    </a>
                </div>
            </div>
            <div class="relative group bg-white transition hover:z-[1] hover:shadow-2xl">
                <div class="relative p-8 space-y-8">
                    <!-- <img src="images/avatars/package-delivery.png" class="w-10" width="512" height="512" alt="burger illustration"> -->

                    <div class="space-y-2">
                        <h5 class="text-xl font-medium text-gray-800 transition group-hover:text-indigo-600">Third feature</h5>
                        <p class="text-sm text-gray-600">Neque Dolor, fugiat non cum doloribus aperiam voluptates nostrum.</p>
                    </div>
                    <a href="#" class="flex items-center justify-between group-hover:text-indigo-600">
                        <span class="text-sm">Read more</span>
                        <span class="text-2xl transition duration-300 -translate-x-4 opacity-0 group-hover:opacity-100 group-hover:translate-x-0">&RightArrow;</span>
                    </a>
                </div>
            </div>
            <div class="relative group bg-gray-100 transition hover:z-[1] hover:shadow-2xl lg:hidden xl:block">
                <div class="relative p-8 space-y-8 transition duration-300 border-dashed rounded-lg group-hover:bg-white group-hover:border group-hover:scale-90">
                    <!-- <img src="images/avatars/metal.png" class="w-10" width="512" height="512" alt="burger illustration"> -->

                    <div class="space-y-2">
                        <h5 class="text-xl font-medium text-gray-800 transition group-hover:text-indigo-600">More features</h5>
                        <p class="text-sm text-gray-600">Neque Dolor, fugiat non cum doloribus aperiam voluptates nostrum.</p>
                    </div>
                    <a href="#" class="flex items-center justify-between group-hover:text-indigo-600">
                        <span class="text-sm">Read more</span>
                        <span class="text-2xl transition duration-300 -translate-x-4 opacity-0 group-hover:opacity-100 group-hover:translate-x-0">&RightArrow;</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="relative py-16 text-white bg-gray-900 sm:py-24">
    <div class="container px-4 mx-auto sm:px-6 lg:px-8">
        <div class="max-w-xl mx-auto text-center">
            <h2 class="text-4xl font-bold tracking-tight sm:text-5xl">
                Start for free. Pay as you grow. Switch at any time.
            </h2>

            <p class="mt-4 text-lg text-gray-300">
                No matter how many team members you have - our pricing is simple, transparent and adapts to the size of your company
            </p>
        </div>

        <div class="grid grid-cols-1 gap-8 mt-12 md:grid-cols-2 lg:grid-cols-3">
            <a class="block p-8 transition border border-gray-600 shadow-xl rounded-xl hover:shadow-indigo-500/10 hover:border-indigo-500/10" href="/services/digital-campaigns">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path d="M12 14l9-5-9-5-9 5 9 5z" />
                    <path d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222" />
                </svg>

                <h3 class="mt-4 text-xl font-bold text-white">Starter</h3>

                <div class="mt-4 text-white">
                    <span class="text-[2rem] font-bold">$</span>
                    <span class="font-bold text-[2.625rem]">49</span>
                    <span class="text-gray-400">/month</span>
                </div>

                <p class="mt-1 text-gray-300">
                    Ideal for smaller organizations that need to control parking efficiently.
                </p>

                <ul class="mt-4 -mb-2 text-gray-400 grow">
                    <li class="flex items-center mb-2">
                        <svg class="w-3 h-3 mr-3 text-indigo-400 fill-current shrink-0" viewBox="0 0 12 12" xmlns="http://www.w3.org/2000/svg">
                            <path d="M10.28 2.28L3.989 8.575 1.695 6.28A1 1 0 00.28 7.695l3 3a1 1 0 001.414 0l7-7A1 1 0 0010.28 2.28z"></path>
                        </svg>
                        <span>Up to 2 team members</span>
                    </li>
                    <li class="flex items-center mb-2">
                        <svg class="w-3 h-3 mr-3 text-indigo-400 fill-current shrink-0" viewBox="0 0 12 12" xmlns="http://www.w3.org/2000/svg">
                            <path d="M10.28 2.28L3.989 8.575 1.695 6.28A1 1 0 00.28 7.695l3 3a1 1 0 001.414 0l7-7A1 1 0 0010.28 2.28z"></path>
                        </svg>
                        <span>Up to 1 parking lots</span>
                    </li>
                    <li class="flex items-center mb-2">
                        <svg class="w-3 h-3 mr-3 text-indigo-400 fill-current shrink-0" viewBox="0 0 12 12" xmlns="http://www.w3.org/2000/svg">
                            <path d="M10.28 2.28L3.989 8.575 1.695 6.28A1 1 0 00.28 7.695l3 3a1 1 0 001.414 0l7-7A1 1 0 0010.28 2.28z"></path>
                        </svg>
                        <span>Up to 50 vehicles per parking lot</span>
                    </li>
                    <li class="flex items-center mb-2">
                        <svg class="w-3 h-3 mr-3 text-indigo-400 fill-current shrink-0" viewBox="0 0 12 12" xmlns="http://www.w3.org/2000/svg">
                            <path d="M10.28 2.28L3.989 8.575 1.695 6.28A1 1 0 00.28 7.695l3 3a1 1 0 001.414 0l7-7A1 1 0 0010.28 2.28z"></path>
                        </svg>
                        <span>Dedicated Support</span>
                    </li>
                </ul>
            </a>

            <a class="block p-8 transition border-2 border-indigo-500 shadow-xl bg-indigo-500/30 rounded-xl hover:shadow-indigo-500/20 hover:border-indigo-500/90" href="/services/digital-campaigns">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path d="M12 14l9-5-9-5-9 5 9 5z" />
                    <path d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222" />
                </svg>

                <h3 class="mt-4 text-xl font-bold text-white">Premium</h3>

                <div class="mt-4 text-white">
                    <span class="text-[2rem] font-bold">$</span>
                    <span class="font-bold text-[2.625rem]">99</span>
                    <span class="text-gray-400">/month</span>
                </div>

                <p class="mt-1 text-gray-300">
                    For larger organizations that need to reliable, and scalable solutions.
                </p>

                <ul class="mt-4 -mb-2 text-gray-400 grow">
                    <li class="flex items-center mb-2">
                        <svg class="w-3 h-3 mr-3 text-indigo-400 fill-current shrink-0" viewBox="0 0 12 12" xmlns="http://www.w3.org/2000/svg">
                            <path d="M10.28 2.28L3.989 8.575 1.695 6.28A1 1 0 00.28 7.695l3 3a1 1 0 001.414 0l7-7A1 1 0 0010.28 2.28z"></path>
                        </svg>
                        <span>Up to 5 team members</span>
                    </li>
                    <li class="flex items-center mb-2">
                        <svg class="w-3 h-3 mr-3 text-indigo-400 fill-current shrink-0" viewBox="0 0 12 12" xmlns="http://www.w3.org/2000/svg">
                            <path d="M10.28 2.28L3.989 8.575 1.695 6.28A1 1 0 00.28 7.695l3 3a1 1 0 001.414 0l7-7A1 1 0 0010.28 2.28z"></path>
                        </svg>
                        <span>Up to 5 parking lots</span>
                    </li>
                    <li class="flex items-center mb-2">
                        <svg class="w-3 h-3 mr-3 text-indigo-400 fill-current shrink-0" viewBox="0 0 12 12" xmlns="http://www.w3.org/2000/svg">
                            <path d="M10.28 2.28L3.989 8.575 1.695 6.28A1 1 0 00.28 7.695l3 3a1 1 0 001.414 0l7-7A1 1 0 0010.28 2.28z"></path>
                        </svg>
                        <span>Up to 100 vehicles per parking lot</span>
                    </li>
                    <li class="flex items-center mb-2">
                        <svg class="w-3 h-3 mr-3 text-indigo-400 fill-current shrink-0" viewBox="0 0 12 12" xmlns="http://www.w3.org/2000/svg">
                            <path d="M10.28 2.28L3.989 8.575 1.695 6.28A1 1 0 00.28 7.695l3 3a1 1 0 001.414 0l7-7A1 1 0 0010.28 2.28z"></path>
                        </svg>
                        <span>Dedicated Support</span>
                    </li>
                </ul>
            </a>

            <a class="block p-8 transition border border-gray-600 shadow-xl rounded-xl hover:shadow-indigo-500/10 hover:border-indigo-500/10" href="/services/digital-campaigns">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path d="M12 14l9-5-9-5-9 5 9 5z" />
                    <path d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222" />
                </svg>

                <h3 class="mt-4 text-xl font-bold text-white">Enterprise</h3>

                <div class="mt-4 text-white">
                    <span class="text-[2rem] font-bold">$</span>
                    <span class="font-bold text-[2.625rem]">199</span>
                    <span class="text-gray-400">/month</span>
                </div>

                <p class="mt-1 text-gray-300">
                    Available for establishments with large parking traffic, customized or unique business models
                </p>

                <ul class="mt-4 -mb-2 text-gray-400 grow">
                    <li class="flex items-center mb-2">
                        <svg class="w-3 h-3 mr-3 text-indigo-400 fill-current shrink-0" viewBox="0 0 12 12" xmlns="http://www.w3.org/2000/svg">
                            <path d="M10.28 2.28L3.989 8.575 1.695 6.28A1 1 0 00.28 7.695l3 3a1 1 0 001.414 0l7-7A1 1 0 0010.28 2.28z"></path>
                        </svg>
                        <span>Up to 10 team members</span>
                    </li>
                    <li class="flex items-center mb-2">
                        <svg class="w-3 h-3 mr-3 text-indigo-400 fill-current shrink-0" viewBox="0 0 12 12" xmlns="http://www.w3.org/2000/svg">
                            <path d="M10.28 2.28L3.989 8.575 1.695 6.28A1 1 0 00.28 7.695l3 3a1 1 0 001.414 0l7-7A1 1 0 0010.28 2.28z"></path>
                        </svg>
                        <span>Up to 20 parking lots</span>
                    </li>
                    <li class="flex items-center mb-2">
                        <svg class="w-3 h-3 mr-3 text-indigo-400 fill-current shrink-0" viewBox="0 0 12 12" xmlns="http://www.w3.org/2000/svg">
                            <path d="M10.28 2.28L3.989 8.575 1.695 6.28A1 1 0 00.28 7.695l3 3a1 1 0 001.414 0l7-7A1 1 0 0010.28 2.28z"></path>
                        </svg>
                        <span>Up to 200 vehicles per parking lot</span>
                    </li>
                    <li class="flex items-center mb-2">
                        <svg class="w-3 h-3 mr-3 text-indigo-400 fill-current shrink-0" viewBox="0 0 12 12" xmlns="http://www.w3.org/2000/svg">
                            <path d="M10.28 2.28L3.989 8.575 1.695 6.28A1 1 0 00.28 7.695l3 3a1 1 0 001.414 0l7-7A1 1 0 0010.28 2.28z"></path>
                        </svg>
                        <span>Dedicated Support</span>
                    </li>
                </ul>
            </a>
        </div>

        <div class="mt-12 text-center">
            <a href="{{ route('register')  }}" class="text-white bg-indigo-600 hover:bg-indigo-800 focus:ring-4 focus:outline-none focus:ring-indigo-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center inline-flex items-center">
                More Details
                <svg class="w-4 h-4 ml-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                </svg>
            </a>
        </div>
    </div>
</section>

<section class="relative py-16 sm:py-24 bg-gray-50">
    <div class="container px-4 mx-auto sm:px-6 lg:px-8">
        <div class="max-w-[38rem] mx-auto text-center">
            <h2 class="text-4xl font-bold tracking-tight sm:text-5xl">
                Most innovative businesses choose us
            </h2>
            <p class="max-w-lg mx-auto mt-4 text-lg text-gray-600">
                Lorem ipsum dolor sit amet consectetur adipisicing elit. Aspernatur
                praesentium natus sapiente commodi. Aliquid sunt tempore iste
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
    <div class="container px-4 m-auto space-y-8 md:px-12">
        <div class="flex flex-col items-center justify-between px-8 py-16 overflow-hidden bg-indigo-600 md:px-12 md:flex-row rounded-xl">
            <div class="mb-6 md:mb-0">
                <h2 class="text-3xl font-bold text-center text-white md:text-left md:text-4xl">Ready to get started?</h2>
                <p class="mt-2 text-lg font-semibold text-center text-indigo-300 md:text-left">We have a generous Basic tier available to get you started right away.</p>
            </div>

            <div>
                <a href="{{ route('register')  }}" class="text-indigo-600 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-indigo-300 font-semibold rounded-lg text-sm px-5 py-2.5 text-center inline-flex items-center">
                    Get Started
                    <svg class="w-4 h-4 ml-2 -mr-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                    </svg>
                </a>
            </div>
        </div>
    </div>
</section>

<footer class="py-16 bg-gray-900">
    <div class="container px-4 mx-auto sm:px-6 md:px-12">
        <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
            <div>
                <a href="{{ route('home') }}" class="flex items-center">
                    <x-logo class="w-auto mr-3 text-indigo-600 h-9" alt="{{ config('app.name') }} Logo" />
                    <span class="self-center text-3xl font-semibold text-white whitespace-nowrap">{{ config('app.name') }}</span>
                </a>

                <p class="max-w-xs mt-4 text-sm text-gray-300">
                    Lorem ipsum dolor sit amet consectetur adipisicing elit. Voluptas, accusantium.
                </p>

                <div class="flex mt-8 space-x-6 text-gray-300">
                    <a class="hover:opacity-75" href="" target="_blank" rel="noreferrer">
                        <span class="sr-only"> Facebook </span>

                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path fill-rule="evenodd" d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z" clip-rule="evenodd" />
                        </svg>
                    </a>

                    <a class="hover:opacity-75" href="" target="_blank" rel="noreferrer">
                        <span class="sr-only"> Instagram </span>

                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path fill-rule="evenodd" d="M12.315 2c2.43 0 2.784.013 3.808.06 1.064.049 1.791.218 2.427.465a4.902 4.902 0 011.772 1.153 4.902 4.902 0 011.153 1.772c.247.636.416 1.363.465 2.427.048 1.067.06 1.407.06 4.123v.08c0 2.643-.012 2.987-.06 4.043-.049 1.064-.218 1.791-.465 2.427a4.902 4.902 0 01-1.153 1.772 4.902 4.902 0 01-1.772 1.153c-.636.247-1.363.416-2.427.465-1.067.048-1.407.06-4.123.06h-.08c-2.643 0-2.987-.012-4.043-.06-1.064-.049-1.791-.218-2.427-.465a4.902 4.902 0 01-1.772-1.153 4.902 4.902 0 01-1.153-1.772c-.247-.636-.416-1.363-.465-2.427-.047-1.024-.06-1.379-.06-3.808v-.63c0-2.43.013-2.784.06-3.808.049-1.064.218-1.791.465-2.427a4.902 4.902 0 011.153-1.772A4.902 4.902 0 015.45 2.525c.636-.247 1.363-.416 2.427-.465C8.901 2.013 9.256 2 11.685 2h.63zm-.081 1.802h-.468c-2.456 0-2.784.011-3.807.058-.975.045-1.504.207-1.857.344-.467.182-.8.398-1.15.748-.35.35-.566.683-.748 1.15-.137.353-.3.882-.344 1.857-.047 1.023-.058 1.351-.058 3.807v.468c0 2.456.011 2.784.058 3.807.045.975.207 1.504.344 1.857.182.466.399.8.748 1.15.35.35.683.566 1.15.748.353.137.882.3 1.857.344 1.054.048 1.37.058 4.041.058h.08c2.597 0 2.917-.01 3.96-.058.976-.045 1.505-.207 1.858-.344.466-.182.8-.398 1.15-.748.35-.35.566-.683.748-1.15.137-.353.3-.882.344-1.857.048-1.055.058-1.37.058-4.041v-.08c0-2.597-.01-2.917-.058-3.96-.045-.976-.207-1.505-.344-1.858a3.097 3.097 0 00-.748-1.15 3.098 3.098 0 00-1.15-.748c-.353-.137-.882-.3-1.857-.344-1.023-.047-1.351-.058-3.807-.058zM12 6.865a5.135 5.135 0 110 10.27 5.135 5.135 0 010-10.27zm0 1.802a3.333 3.333 0 100 6.666 3.333 3.333 0 000-6.666zm5.338-3.205a1.2 1.2 0 110 2.4 1.2 1.2 0 010-2.4z" clip-rule="evenodd" />
                        </svg>
                    </a>

                    <a class="hover:opacity-75" href="" target="_blank" rel="noreferrer">
                        <span class="sr-only"> Twitter </span>

                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M8.29 20.251c7.547 0 11.675-6.253 11.675-11.675 0-.178 0-.355-.012-.53A8.348 8.348 0 0022 5.92a8.19 8.19 0 01-2.357.646 4.118 4.118 0 001.804-2.27 8.224 8.224 0 01-2.605.996 4.107 4.107 0 00-6.993 3.743 11.65 11.65 0 01-8.457-4.287 4.106 4.106 0 001.27 5.477A4.072 4.072 0 012.8 9.713v.052a4.105 4.105 0 003.292 4.022 4.095 4.095 0 01-1.853.07 4.108 4.108 0 003.834 2.85A8.233 8.233 0 012 18.407a11.616 11.616 0 006.29 1.84" />
                        </svg>
                    </a>

                    <a class="hover:opacity-75" href="" target="_blank" rel="noreferrer">
                        <span class="sr-only"> GitHub </span>

                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path fill-rule="evenodd" d="M12 2C6.477 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.531 1.032 1.531 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0112 6.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.202 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.943.359.309.678.92.678 1.855 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.019 10.019 0 0022 12.017C22 6.484 17.522 2 12 2z" clip-rule="evenodd" />
                        </svg>
                    </a>

                    <a class="hover:opacity-75" href="" target="_blank" rel="noreferrer">
                        <span class="sr-only"> Dribbble </span>

                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path fill-rule="evenodd" d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10c5.51 0 10-4.48 10-10S17.51 2 12 2zm6.605 4.61a8.502 8.502 0 011.93 5.314c-.281-.054-3.101-.629-5.943-.271-.065-.141-.12-.293-.184-.445a25.416 25.416 0 00-.564-1.236c3.145-1.28 4.577-3.124 4.761-3.362zM12 3.475c2.17 0 4.154.813 5.662 2.148-.152.216-1.443 1.941-4.48 3.08-1.399-2.57-2.95-4.675-3.189-5A8.687 8.687 0 0112 3.475zm-3.633.803a53.896 53.896 0 013.167 4.935c-3.992 1.063-7.517 1.04-7.896 1.04a8.581 8.581 0 014.729-5.975zM3.453 12.01v-.26c.37.01 4.512.065 8.775-1.215.25.477.477.965.694 1.453-.109.033-.228.065-.336.098-4.404 1.42-6.747 5.303-6.942 5.629a8.522 8.522 0 01-2.19-5.705zM12 20.547a8.482 8.482 0 01-5.239-1.8c.152-.315 1.888-3.656 6.703-5.337.022-.01.033-.01.054-.022a35.318 35.318 0 011.823 6.475 8.4 8.4 0 01-3.341.684zm4.761-1.465c-.086-.52-.542-3.015-1.659-6.084 2.679-.423 5.022.271 5.314.369a8.468 8.468 0 01-3.655 5.715z" clip-rule="evenodd" />
                        </svg>
                    </a>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-8 lg:col-span-2 sm:grid-cols-2 lg:grid-cols-4">
                <div>
                    <p class="font-medium text-gray-500">
                        Company
                    </p>

                    <nav class="flex flex-col mt-4 space-y-2 text-sm text-gray-300">
                        <a class="hover:opacity-75" href=""> About </a>
                        <a class="hover:opacity-75" href=""> Meet the Team </a>
                        <a class="hover:opacity-75" href=""> History </a>
                        <a class="hover:opacity-75" href=""> Careers </a>
                    </nav>
                </div>

                <div>
                    <p class="font-medium text-gray-500">
                        Services
                    </p>

                    <nav class="flex flex-col mt-4 space-y-2 text-sm text-gray-300">
                        <a class="hover:opacity-75" href=""> 1on1 Coaching </a>
                        <a class="hover:opacity-75" href=""> Company Review </a>
                        <a class="hover:opacity-75" href=""> Accounts Review </a>
                        <a class="hover:opacity-75" href=""> HR Consulting </a>
                        <a class="hover:opacity-75" href=""> SEO Optimisation </a>
                    </nav>
                </div>

                <div>
                    <p class="font-medium text-gray-500">
                        Helpful Links
                    </p>

                    <nav class="flex flex-col mt-4 space-y-2 text-sm text-gray-300">
                        <a class="hover:opacity-75" href=""> Contact </a>
                        <a class="hover:opacity-75" href=""> FAQs </a>
                        <a class="hover:opacity-75" href=""> Live Chat </a>
                    </nav>
                </div>

                <div>
                    <p class="font-medium text-gray-500">
                        Legal
                    </p>

                    <nav class="flex flex-col mt-4 space-y-2 text-sm text-gray-300">
                        <a class="hover:opacity-75" href=""> Privacy Policy </a>
                        <a class="hover:opacity-75" href=""> Terms & Conditions </a>
                        <a class="hover:opacity-75" href=""> Returns Policy </a>
                        <a class="hover:opacity-75" href=""> Accessibility </a>
                    </nav>
                </div>
            </div>
        </div>

        <p class="mt-8 text-xs text-gray-300">
            &copy; 2022 {{ config('app.name') }} Inc.
        </p>
    </div>
</footer>

<!-- <a href='https://www.freepik.com/vectors/parking'>Parking vector created by macrovector - www.freepik.com</a> -->
<!-- Photo by <a href="https://unsplash.com/@carlesrgm?utm_source=unsplash&utm_medium=referral&utm_content=creditCopyText">Carles Rabada</a> on <a href="https://unsplash.com/s/photos/parking-lot?utm_source=unsplash&utm_medium=referral&utm_content=creditCopyText">Unsplash</a> -->
<!-- Photo by <a href="https://unsplash.com/@hydngallery?utm_source=unsplash&utm_medium=referral&utm_content=creditCopyText">Haidan</a> on <a href="https://unsplash.com/s/photos/parking-lot?utm_source=unsplash&utm_medium=referral&utm_content=creditCopyText">Unsplash</a>    -->
<!-- Photo by <a href="https://unsplash.com/@von_co?utm_source=unsplash&utm_medium=referral&utm_content=creditCopyText">Ivana Cajina</a> on <a href="https://unsplash.com/s/photos/parking-lot?utm_source=unsplash&utm_medium=referral&utm_content=creditCopyText">Unsplash</a> -->
<!-- Photo by <a href="https://unsplash.com/@ryansearle?utm_source=unsplash&utm_medium=referral&utm_content=creditCopyText">Ryan Searle</a> on <a href="https://unsplash.com/s/photos/parking-lot?utm_source=unsplash&utm_medium=referral&utm_content=creditCopyText">Unsplash</a>-->
@endsection