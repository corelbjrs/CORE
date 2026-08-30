<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'C.O.R.E. Platform') }}</title>

    <!-- Tailwind CSS (Vite setup) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#fbfbfb] dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC] min-h-screen flex flex-col items-center justify-between p-6 lg:p-8 font-sans antialiased">

    <!-- Top Navigation Header -->
    <header class="w-full lg:max-w-4xl max-w-[335px] text-sm mb-6">
        @if (Route::has('login'))
            <nav class="flex items-center justify-end gap-3">

                {{-- Public Jobs Route Button --}}
                {{-- @if (Route::has('public.jobs'))
                    <a
                        href="{{ route('public.jobs') }}"
                        class="inline-block px-4 py-1.5 dark:text-[#EDEDEC] text-[#1b1b18] border border-transparent hover:border-[#19140035] dark:hover:border-[#3E3E3A] rounded-md text-sm font-medium transition-colors"
                    >
                        Browse Jobs
                    </a>
                @endif --}}

                @auth
                    <a
                        href="{{ url('/dashboard') }}"
                        class="inline-block px-4 py-1.5 dark:text-[#EDEDEC] border-[#19140035] hover:border-[#1915014a] border text-[#1b1b18] dark:border-[#3E3E3A] dark:hover:border-[#62605b] rounded-md text-sm font-medium transition-colors"
                    >
                        Dashboard
                    </a>
                @else
                    <a
                        href="{{ route('login') }}"
                        class="inline-block px-4 py-1.5 dark:text-[#EDEDEC] text-[#1b1b18] border border-transparent hover:border-[#19140035] dark:hover:border-[#3E3E3A] rounded-md text-sm font-medium transition-colors"
                    >
                        Log in
                    </a>

                    @if (Route::has('register'))
                        <a
                            href="{{ route('register') }}"
                            class="inline-block px-4 py-1.5 dark:text-[#EDEDEC] border-[#19140035] hover:border-[#1915014a] border text-[#1b1b18] dark:border-[#3E3E3A] dark:hover:border-[#62605b] rounded-md text-sm font-medium transition-colors"
                        >
                            Register
                        </a>
                    @endif
                @endauth
            </nav>
        @endif
    </header>

    <!-- Main Content Hero Card -->
    <div class="flex items-center justify-center w-full lg:grow my-auto">
        <main class="flex max-w-[335px] w-full flex-col lg:max-w-4xl lg:flex-row bg-white dark:bg-[#161615] border border-[#19140015] dark:border-[#3E3E3A] rounded-xl overflow-hidden shadow-lg">

            <!-- Left Banner Image Section -->
            <div class="relative lg:w-1/2 w-full shrink-0 min-h-[280px] lg:min-h-[420px] overflow-hidden bg-[#0f172a]">
                <img
                    src="{{ asset('images/core.png') }}"
                    alt="C.O.R.E. Banner"
                    class="w-full h-full object-cover"
                >

                <!-- Inner Shadow Overlay for Dark Mode Accent -->
                <div class="absolute inset-0 pointer-events-none shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.1)] dark:shadow-[inset_0px_0px_0px_1px_#fffaed1a]"></div>
            </div>

            <!-- Right Content Section (Vertically Centered) -->
            <div class="p-6 lg:p-8 lg:w-1/2 w-full flex items-center">
                <div class="w-full">

                    <h2 class="text-xl lg:text-2xl font-bold tracking-tight text-[#1b1b18] dark:text-[#EDEDEC] mb-3">
                        About the C.O.R.E.
                    </h2>

                    <p class="text-sm leading-relaxed text-[#706f6c] dark:text-[#A1A09A] mb-5">
                        Welcome to the centralized Public Employment Service portal. This application is designed to simplify and organize employment opportunities by directly connecting local job seekers with verified employers.

                    </p>

                    <!-- Feature Cards Container -->
                    <div class="space-y-3 mb-6">

                        <!-- For Job Seekers -->
                        <div class="p-3.5 rounded-lg bg-[#f8fafc] dark:bg-[#1f1f1e] border border-[#e2e8f0] dark:border-[#2a2a28]">
                            <p class="text-xs lg:text-sm leading-normal text-[#706f6c] dark:text-[#A1A09A]">
                                <strong class="font-semibold text-[#1b1b18] dark:text-[#EDEDEC] block mb-0.5">For Job Seekers:</strong>
                                Discover location-based job openings and apply directly online.
                            </p>
                        </div>       

                        <!-- For Employers & Admins -->
                        <div class="p-3.5 rounded-lg bg-[#f8fafc] dark:bg-[#1f1f1e] border border-[#e2e8f0] dark:border-[#2a2a28]">
                            <p class="text-xs lg:text-sm leading-normal text-[#706f6c] dark:text-[#A1A09A]">
                                <strong class="font-semibold text-[#1b1b18] dark:text-[#EDEDEC] block mb-0.5">For Employers & Admins:</strong>
                                Publish vacancies and track applicant matching in real time.
                            </p>
                        </div>
                    </div>

                    <!-- "Find Your Future" Clickable CTA Button -->
                    <div class="pt-1">
                        @if (Route::has('public.jobs'))
                            <a href="{{ route('login') }}" class="w-full inline-flex justify-center items-center px-6 py-3 bg-gradient-to-r from-orange-500 to-amber-500 hover:from-orange-600 hover:to-amber-600 text-white font-semibold rounded-full shadow-md hover:shadow-lg transition-all duration-200 text-center tracking-wide text-sm">
                                Find Your Future &rarr;
                            </a>
                        @else
                            <a href="{{ route('register') }}" class="w-full inline-flex justify-center items-center px-6 py-3 bg-gradient-to-r from-orange-500 to-amber-500 hover:from-orange-600 hover:to-amber-600 text-white font-semibold rounded-full shadow-md hover:shadow-lg transition-all duration-200 text-center tracking-wide text-sm">
                                Find Your Future &rarr;
                            </a>
                        @endif
                    </div>

                </div>
            </div>

        </main>
    </div>

    <!-- Footer Spacer / Footer -->
    <footer class="w-full text-center text-xs text-[#706f6c] dark:text-[#A1A09A] mt-6">
        &copy; {{ date('Y') }} C.O.R.E. All rights reserved.
    </footer>

</body>
</html>
