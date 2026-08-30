<x-app-layout>
<div class="container mx-auto px-4 py-8">
    <!-- Header with Toggle Button -->
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            @if(request()->has('show_all'))
                <h1 class="text-2xl font-bold text-gray-800">All Job Vacancies</h1>
                <p class="text-sm text-gray-600">
                    Showing the complete list of all active vacancies with no search or profile filters applied.
                </p>
            @else
                <h1 class="text-2xl font-bold text-gray-800">Job Recommendations For You</h1>
                <p class="text-sm text-gray-600">
                    Showing active jobs matched to your skills profile alongside recommendations, latest postings, and top choices.
                </p>
            @endif
        </div>

        <!-- Filter Toggle and Reset Buttons -->
        <div class="flex items-center gap-2 self-start sm:self-center">
            @if(request()->has('show_all') || request()->hasAny(['job_type', 'course', 'province', 'town']))
                <a href="{{ route('recommended') }}" class="inline-flex items-center justify-center bg-gray-100 border border-gray-200 text-gray-700 py-2.5 px-4 rounded-xl hover:bg-gray-200 font-semibold text-sm transition duration-150 shadow-sm">
                    Reset View
                </a>
            @endif

            <button id="open-filter-btn" type="button" class="inline-flex items-center justify-center gap-2 bg-indigo-600 text-white py-2.5 px-5 rounded-xl hover:bg-indigo-700 font-semibold text-sm shadow-sm transition duration-150">
                <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 3c2.755 0 5.455.232 8.083.678.533.09.917.556.917 1.096v1.044a2.25 2.25 0 0 1-.659 1.591l-5.432 5.432a2.25 2.25 0 0 0-.659 1.591v2.927a2.25 2.25 0 0 1-1.244 2.013L9.75 21v-6.568a2.25 2.25 0 0 0-.659-1.591L3.659 7.409A2.25 2.25 0 0 1 3 5.818V4.774c0-.54.384-1.006.917-1.096A48.32 48.32 0 0 1 12 3Z" />
                </svg>
                Search & Filter Jobs
                @if(request()->hasAny(['job_type', 'course', 'province', 'town']))
                    <span class="ml-1 px-1.5 py-0.5 text-xs bg-indigo-800 rounded-full">Active</span>
                @endif
            </button>
        </div>
    </div>

    <!-- Backdrop Modal -->
    <div id="filter-modal" class="fixed inset-0 z-50 hidden bg-gray-900 bg-opacity-50 backdrop-blur-sm flex items-center justify-center p-4 transition-opacity">
        <div class="w-full max-w-xl rounded-xl bg-white p-6 text-gray-600 shadow-xl relative">
            <div class="flex items-center justify-between border-b border-gray-100 pb-3 mb-4">
                <h3 class="text-lg font-bold text-gray-800">Advanced Job Search</h3>
                <button id="close-filter-btn" type="button" class="text-gray-400 hover:text-gray-600 p-1 rounded-lg hover:bg-gray-100 transition">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Filter Form -->
            <form action="{{ route('recommended') }}" method="GET">
                @if(request()->has('show_all'))
                    <input type="hidden" name="show_all" value="1">
                @endif

                <div class="space-y-4 mb-6">
                    <div>
                        <label for="job_type" class="block text-sm font-medium text-gray-700 mb-1">Type of Job</label>
                        <select id="job_type" name="job_type" class="block w-full text-sm rounded-md shadow-sm border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                            <option value=""></option>
                            <option value="Full Time" {{ request('job_type') == 'Full Time' ? 'selected' : '' }}>Full Time</option>
                            <option value="Part Time" {{ request('job_type') == 'Part Time' ? 'selected' : '' }}>Part Time</option>
                            <option value="Contract / Freelance" {{ request('job_type') == 'Contract / Freelance' ? 'selected' : '' }}>Contract / Freelance</option>
                            <option value="Temporary / Seasonal" {{ request('job_type') == 'Temporary / Seasonal' ? 'selected' : '' }}>Temporary / Seasonal</option>
                        </select>
                    </div>

                    <div>
                        <label for="course" class="block text-sm font-medium text-gray-700 mb-1">Filter by Course</label>
                        <select id="course" name="course" class="block w-full text-sm rounded-md shadow-sm border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                            <option value=""></option>
                            @foreach($courses as $c)
                                <option value="{{ $c->display_name }}" {{ request('course') == $c->display_name ? 'selected' : '' }}>{{ $c->display_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="pt-2 border-t border-gray-100">
                        <h4 class="text-sm font-semibold text-gray-800 mb-3">Preferred Work Location</h4>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="province" class="block text-xs font-medium text-gray-500 mb-1">Province</label>
                                <select id="province" name="province" class="block w-full text-sm rounded-md shadow-sm border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">Select Province</option>
                                </select>
                            </div>
                            <div>
                                <label for="town" class="block text-xs font-medium text-gray-500 mb-1">Town / City</label>
                                <select id="town" name="town" class="block w-full text-sm rounded-md shadow-sm border-gray-300 focus:border-indigo-500 focus:ring-indigo-500" disabled>
                                    <option value="">Select Town/City</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-2 pt-4 border-t border-gray-100">
                    @if(request()->hasAny(['job_type', 'course', 'province', 'town']))
                        <a href="{{ route('recommended', request()->only(['show_all'])) }}" class="px-4 py-2 text-sm font-medium text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-md transition">
                            Clear Filters
                        </a>
                    @endif
                    <button type="submit" class="bg-indigo-600 text-white py-2 px-5 rounded-md hover:bg-indigo-700 font-medium text-sm transition">
                        Filter Jobs
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- MAIN 2-COLUMN GRID LAYOUT -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-start">

        <!-- COLUMN 1: MATCHED & NEAR YOU (BLUE SECTION) -->
        <div class="space-y-4">
            <div class="pb-2 border-b-2 border-blue-400">
                <h2 class="text-base font-bold text-gray-800 flex items-center gap-2">
                    <span class="h-3 w-3 rounded-full bg-blue-500 flex-shrink-0"></span>
                    Matched & Near You
                </h2>
                <p class="text-xs text-gray-500">Skills-matched and sorted by proximity.</p>
            </div>

            @if($jobs->isEmpty())
                <div class="bg-gray-50 border border-gray-200 text-gray-500 p-4 rounded-xl text-sm">
                    No qualification matches found in your area.
                </div>
            @else
                <div class="space-y-4">
                    @foreach($jobs as $job)
                        <div class="bg-white border-l-4 border-blue-400 border-t border-r border-b rounded-r-xl p-4 shadow-sm hover:shadow transition">
                            <div class="flex items-start gap-3">
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-1.5 flex-wrap mb-1 text-xs text-gray-500">
                                        <span class="inline-flex items-center rounded-full text-xs font-semibold bg-blue-50 text-blue-800 px-2 py-0.5">
                                            {{ isset($job->distance) ? round($job->distance, 1) . ' km away' : 'Nearby' }}
                                        </span>
                                        <span>•</span>
                                        <span class="font-semibold text-gray-700 truncate">{{ $job->barangay ?? '' }}, {{ $job->town }}</span>
                                    </div>
                                    <h3 class="text-base font-semibold text-gray-900 leading-snug mb-0.5 truncate">{{ $job->job_title }}</h3>
                                    <p class="text-xs font-semibold text-gray-700 mb-2 truncate">{{ $job->company_name ?? 'N/A' }}</p>

                                    <p class="text-xs text-gray-500 line-clamp-2">
                                        <span class="font-semibold text-gray-700">SUMMARY:</span>
                                        {{ !empty($job->job_summary) ? trim(strip_tags(html_entity_decode($job->job_summary))) : 'GENERAL' }}
                                    </p>
                                </div>
                                <div class="w-14 h-14 flex-shrink-0 rounded-lg overflow-hidden border border-gray-200 bg-gray-50 flex items-center justify-center">
                                    @if(!empty($job->company_logo) && Storage::disk('public')->exists($job->company_logo))
                                        <img src="{{ Storage::url($job->company_logo) }}" alt="Logo" class="w-full h-full object-cover">
                                    @else
                                        <img src="{{ asset('images/company-logo/default-logo.jpg') }}" alt="Default Logo" class="w-full h-full object-cover">
                                    @endif
                                </div>
                            </div>

                            <div class="mt-3 pt-2 border-t border-gray-100 flex justify-between items-center text-xs">
                                <span class="text-gray-400">{{ \Carbon\Carbon::parse($job->created_at)->diffForHumans() }}</span>
                                <a href="/recd/{{ $job->job_id }}" class="font-semibold text-blue-600 hover:text-blue-400">
                                    View &rarr;
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- COLUMN 2: DISCOVERY & TRENDS (STACKED SECTIONS) -->
        <div class="space-y-8">
            <!-- 1. COLLABORATIVE FILTERING (GREEN SECTION) -->
            @if(isset($collaborativeJobs) && !$collaborativeJobs->isEmpty())
                <div>
                    <div class="mb-4 pb-2 border-b-2 border-green-400">
                        <h2 class="text-base font-bold text-gray-800 flex items-center gap-2">
                            <span class="h-3 w-3 rounded-full bg-green-500 flex-shrink-0"></span>
                            People Also Viewed
                        </h2>
                        <p class="text-xs text-gray-500">Based on peer search history.</p>
                    </div>
                    <div class="space-y-4">
                        @foreach($collaborativeJobs as $job)
                            <div class="bg-white border-l-4 border-green-400 border-t border-r border-b  rounded-r-xl p-4 shadow-sm hover:shadow transition">
                                <div class="flex justify-between items-start gap-2 mb-1">
                                    <h3 class="text-base font-semibold text-gray-900 leading-snug truncate">{{ $job->job_title }}</h3>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-green-50 text-green-800 flex-shrink-0">
                                        Peer Choice
                                    </span>
                                </div>

                                <p class="text-xs text-gray-500 line-clamp-2 mb-2">
                                    <span class="font-semibold text-gray-700">Summary:</span>
                                    {{ !empty($job->job_summary) ? trim(strip_tags(html_entity_decode($job->job_summary))) : 'GENERAL' }}
                                </p>

                                <div class="text-xs text-gray-500 space-y-0.5">
                                    <div><span class="font-medium text-gray-700">Type:</span> {{ $job->job_type }}</div>
                                    <div class="truncate"><span class="font-medium text-gray-700">Location:</span> {{ $job->barangay ?? '' }}, {{ $job->town }}</div>
                                </div>

                                <div class="mt-3 pt-2 border-t border-gray-100 flex justify-between items-center text-xs">
                                    <span class="text-gray-400">{{ \Carbon\Carbon::parse($job->created_at)->diffForHumans() }}</span>
                                    <a href="/recd/{{ $job->job_id }}" class="font-semibold text-green-600 hover:text-green-800">
                                        View &rarr;
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @else
                <div class="bg-gray-50 border border-gray-200 text-gray-500 p-4 rounded-xl text-sm">
                    <div class="w-full">
                        <h2 class="text-xl lg:text-2xl font-bold tracking-tight text-[#1b1b18] dark:text-[#EDEDEC] mb-3">
                            About the C.O.R.E.
                        </h2>

                        <p class="text-sm leading-relaxed text-[#706f6c] dark:text-[#A1A09A] mb-5">
                            Welcome to the centralized Public Employment Service portal. This application is designed to simplify and organize employment opportunities by directly connecting local job seekers with verified employers. It provides a user-friendly interface for job seekers to explore available positions, apply online, and receive notifications about relevant job openings.
                        </p>
                        <p class="text-sm leading-relaxed text-[#706f6c] dark:text-[#A1A09A] mb-5">
                            Employers can efficiently post vacancies and track the recruitment process in real time. The platform also offers resources and support for skill development, career guidance, and access to government programs aimed at enhancing employability. By fostering a transparent and efficient job market, the C.O.R.E. portal aims to empower individuals and communities towards sustainable employment and economic growth.
                        </p>
                        <div class="space-y-3 mb-6">

                            <!-- For Job Seekers -->
                            <div class="p-3.5 rounded-lg bg-[#f8fafc] dark:bg-[#1f1f1e] border border-[#e2e8f0] dark:border-[#2a2a28]">
                                <p class="text-xs lg:text-sm leading-normal text-[#706f6c] dark:text-[#A1A09A]">
                                    <strong class="font-semibold text-[#1b1b18] dark:text-[#EDEDEC] block mb-0.5">The C.O.R.E portal is still learning, help us improve!</strong>
                                    <a href="{{ route('recommended') }}" class="text-blue-500 hover:text-blue-700">
                                        Go to Suggestion Box
                                    </a>to provide feedback on job matches, application process, and overall user experience. This will help the C.O.R.E. portal refine its algorithms and improve the quality of job recommendations.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- 2. NEWLY ADDED JOBS (PURPLE SECTION) -->
            @if(isset($newlyAddedJobs) && !$newlyAddedJobs->isEmpty())
                <div>
                    <div class="mb-4 pb-2 border-b-2 border-purple-400">
                        <h2 class="text-base font-bold text-gray-800 flex items-center gap-2">
                            <span class="h-3 w-3 rounded-full bg-purple-500 flex-shrink-0"></span>
                            Newly Added Jobs
                        </h2>
                        <p class="text-xs text-gray-500">Latest job postings updated recently.</p>
                    </div>

                    <div class="space-y-4">
                        @foreach($newlyAddedJobs as $job)
                            <div class="bg-white border-l-4 border-purple-400 border-t border-r border-b  rounded-r-xl p-4 shadow-sm hover:shadow transition">
                                <div class="flex justify-between items-start gap-2 mb-1">
                                    <h3 class="text-base font-semibold text-gray-900 leading-snug truncate">{{ $job->job_title }}</h3>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-purple-50 text-purple-800 flex-shrink-0">
                                        New
                                    </span>
                                </div>

                                <p class="text-xs font-medium text-gray-600 mb-1 truncate">{{ $job->company_name ?? 'N/A' }}</p>

                                <p class="text-xs text-gray-500 line-clamp-2 mb-2">
                                    {{ !empty($job->job_summary) ? trim(strip_tags(html_entity_decode($job->job_summary))) : 'GENERAL' }}
                                </p>

                                <div class="text-xs text-gray-500 space-y-0.5">
                                    <div><span class="font-medium text-gray-700">Type:</span> {{ $job->job_type }}</div>
                                    <div class="truncate"><span class="font-medium text-gray-700">Location:</span> {{ $job->town }}, {{ $job->province }}</div>
                                </div>

                                <div class="mt-3 pt-2 border-t border-gray-100 flex justify-between items-center text-xs">
                                    <span class="text-purple-600 font-medium">{{ \Carbon\Carbon::parse($job->created_at)->diffForHumans() }}</span>
                                    <a href="/recd/{{ $job->job_id }}" class="font-semibold text-purple-600 hover:text-purple-800">
                                        View &rarr;
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- 3. MOST SAVED JOBS (AMBER SECTION) -->
            @if(isset($mostSavedJobs) && !$mostSavedJobs->isEmpty())
                <div>
                    <div class="mb-4 pb-2 border-b-2 border-amber-400">
                        <h2 class="text-base font-bold text-gray-800 flex items-center gap-2">
                            <span class="h-3 w-3 rounded-full bg-amber-500 flex-shrink-0"></span>
                            Most Saved Jobs
                        </h2>
                        <p class="text-xs text-gray-500">Popular vacancies bookmarked by applicants.</p>
                    </div>

                    <div class="space-y-4">
                        @foreach($mostSavedJobs as $job)
                            <div class="bg-white border-l-4 border-amber-400 border-t border-r border-b rounded-r-xl p-4 shadow-sm hover:shadow transition">
                                <div class="flex justify-between items-start gap-2 mb-1">
                                    <h3 class="text-base font-semibold text-gray-900 leading-snug truncate">{{ $job->job_title }}</h3>
                                    @if(isset($job->saves_count))
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold bg-amber-50 text-amber-800 flex-shrink-0">
                                            <svg class="w-3 h-3 fill-current text-amber-500" viewBox="0 0 20 20">
                                                <path d="M5 4a2 2 0 012-2h6a2 2 0 012 2v14l-5-2.5L5 18V4z"/>
                                            </svg>
                                            {{ $job->saves_count }} saves
                                        </span>
                                    @endif
                                </div>

                                <p class="text-xs font-medium text-gray-600 mb-1 truncate">{{ $job->company_name ?? 'N/A' }}</p>

                                <p class="text-xs text-gray-500 line-clamp-2 mb-2">
                                    {{ !empty($job->job_summary) ? trim(strip_tags(html_entity_decode($job->job_summary))) : 'GENERAL' }}
                                </p>

                                <div class="text-xs text-gray-500 space-y-0.5">
                                    <div><span class="font-medium text-gray-700">Type:</span> {{ $job->job_type }}</div>
                                    <div class="truncate"><span class="font-medium text-gray-700">Location:</span> {{ $job->town }}, {{ $job->province }}</div>
                                </div>

                                <div class="mt-3 pt-2 border-t border-gray-100 flex justify-between items-center text-xs">
                                    <span class="text-gray-400">{{ \Carbon\Carbon::parse($job->created_at)->diffForHumans() }}</span>
                                    <a href="/recd/{{ $job->job_id }}" class="font-semibold text-amber-600 hover:text-amber-800">
                                        View &rarr;
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal & Async API Script Block -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    const modal = document.getElementById('filter-modal');
    const openBtn = document.getElementById('open-filter-btn');
    const closeBtn = document.getElementById('close-filter-btn');

    const openModal = () => modal.classList.remove('hidden');
    const closeModal = () => modal.classList.add('hidden');

    openBtn.addEventListener('click', openModal);
    closeBtn.addEventListener('click', closeModal);

    modal.addEventListener('click', function(e) {
        if (e.target === modal) closeModal();
    });

    const provinceSelect = document.getElementById('province');
    const townSelect = document.getElementById('town');

    const provincesUrl = "{{ url('/api/provinces') }}";
    const townsUrl     = "{{ url('/api/towns') }}";

    fetch(provincesUrl)
        .then(res => res.json())
        .then(provinces => {
            provinces.forEach(province => {
                let opt = document.createElement('option');
                opt.value = province;
                opt.textContent = province;
                if(province === "{{ request('province') }}") opt.selected = true;
                provinceSelect.appendChild(opt);
            });
            if(provinceSelect.value) provinceSelect.dispatchEvent(new Event('change'));
        })
        .catch(err => console.error('Error fetching provinces:', err));

    provinceSelect.addEventListener('change', function () {
        const province = this.value;
        townSelect.innerHTML = '<option value="">Select Town/City</option>';
        townSelect.disabled = true;

        if (!province) return;

        fetch(`${townsUrl}?province=${encodeURIComponent(province)}`)
            .then(res => res.json())
            .then(towns => {
                towns.forEach(t => {
                    let opt = document.createElement('option');
                    opt.value = t.town;
                    opt.textContent = t.town;
                    if(t.town === "{{ request('town') }}") opt.selected = true;
                    townSelect.appendChild(opt);
                });
                townSelect.disabled = false;
            });
    });
});
</script>
</x-app-layout>
