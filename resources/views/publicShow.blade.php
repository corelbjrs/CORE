<x-public-layout>
    <!-- Top Navigation Bar for Public Guests -->
    <nav class="bg-white border-b border-gray-200">
        <div class="container mx-auto px-4 py-3 flex justify-between items-center">
            <a href="/" class="text-xl font-bold text-indigo-600">PESO Job Portal</a>
            <div class="flex items-center gap-3">
                <a href="{{ route('login') }}" class="block w-full bg-white text-indigo-600 font-bold text-center py-2.5 px-4 rounded-xl text-sm shadow hover:bg-indigo-50 transition">
                    Sign In to Apply
                </a>
                <a href="{{ route('register') }}" class="text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 px-4 py-2 rounded-lg transition">Create Account</a>
            </div>
        </div>
    </nav>

    <div class="container mx-auto px-4 py-8 max-w-5xl">
        <!-- Back Link -->
        <div class="mb-6">
            <a href="{{ route('public.jobs') }}" class="inline-flex items-center gap-2 text-sm font-medium text-gray-500 hover:text-indigo-600 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to All Jobs
            </a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left Side: Main Job Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Job Header Card -->
                <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">
                    <div class="flex flex-wrap items-start justify-between gap-3">
                        <div>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-indigo-50 text-indigo-700 mb-2">
                                {{ isset($job->area_of_expertise) ? strtoupper($job->area_of_expertise) : 'GENERAL' }}
                            </span>
                            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 leading-tight">{{ $job->job_title }}</h1>
                            <p class="text-sm text-gray-500 mt-1">Posted {{ \Carbon\Carbon::parse($job->created_at)->format('F d, Y') }} ({{ \Carbon\Carbon::parse($job->created_at)->diffForHumans() }})</p>
                        </div>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-800">
                            {{ $job->job_type }}
                        </span>
                    </div>

                    <!-- Location & Details Pill Row -->
                    <div class="mt-6 pt-6 border-t border-gray-100 grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                        <div class="flex items-center gap-3 text-gray-600">
                            <div class="p-2 bg-indigo-50 text-indigo-600 rounded-lg">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs text-gray-400 font-medium">Work Location</p>
                                <p class="font-semibold text-gray-700">{{ $job->barangay }}, {{ $job->town }}, {{ $job->province }}</p>
                            </div>
                        </div>

                        @if(isset($job->vacancies))
                            <div class="flex items-center gap-3 text-gray-600">
                                <div class="p-2 bg-indigo-50 text-indigo-600 rounded-lg">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-400 font-medium">Open Positions</p>
                                    <p class="font-semibold text-gray-700">{{ $job->vacancies }} Vacancy / Vacancies</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Job Description & Qualifications -->
                <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm space-y-6">
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 mb-3">Job Description</h3>
                        <div class="text-gray-600 text-sm leading-relaxed whitespace-pre-line">
                            {{ trim(strip_tags(html_entity_decode($job->job_description ?? $job->description ?? 'No additional job description provided for this vacancy.'))) }}
                        </div>
                    </div>

                    @if(isset($job->qualification) || isset($job->requirements))
                        <div class="pt-6 border-t border-gray-100">
                            <h3 class="text-lg font-bold text-gray-900 mb-3">Qualifications & Requirements</h3>
                            <div class="text-gray-600 text-sm leading-relaxed whitespace-pre-line">
                                {{ trim(strip_tags(html_entity_decode($job->qualification ?? $job->requirements ?? 'No qualifications or requirements specified.'))) }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Right Side: CTA / Apply Sidebar -->
            <div class="space-y-6">
                <div class="bg-gradient-to-br from-indigo-600 to-purple-700 rounded-2xl p-6 text-white shadow-md">
                    <h3 class="text-lg font-bold">Ready to Apply?</h3>
                    <p class="text-indigo-100 text-sm mt-2 leading-relaxed">
                        Sign in to your PESO Job Portal account to submit your application and track status online.
                    </p>

                    <div class="mt-6 space-y-3">
                        <a href="{{ route('login') }}" class="block w-full bg-white text-indigo-600 font-bold text-center py-2.5 px-4 rounded-xl text-sm shadow hover:bg-indigo-50 transition">
                            Sign In to Apply
                        </a>
                        <a href="{{ route('register') }}" class="block w-full border border-indigo-200 text-white font-semibold text-center py-2.5 px-4 rounded-xl text-sm hover:bg-white/10 transition">
                            Create Free Account
                        </a>
                    </div>
                </div>

                <!-- Additional Helper Box -->
                <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-sm text-xs text-gray-500 space-y-2">
                    <p class="font-semibold text-gray-700 text-sm">About PESO Tangub City</p>
                    <p>The Public Employment Service Office facilitates job placement assistance for all jobseekers within the municipality and region.</p>
                </div>
            </div>
        </div>
    </div>
</x-public-layout>
