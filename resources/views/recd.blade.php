<x-app-layout>
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex flex-col justify-between items-center">
    @if(session('success'))
        <div class="mb-4 p-4 text-sm text-green-700 bg-green-100 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    @if(session('info'))
        <div class="mb-4 p-4 text-sm text-blue-700 bg-blue-100 rounded-lg">
            {{ session('info') }}
        </div>
    @endif

    @if(session('warning'))
        <div class="mb-4 p-4 text-sm text-red-700 bg-red-100 rounded-lg">
            {{ session('warning') }}
        </div>
    @endif
    </div>
    <div class="mb-4">
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative animate-fade-in" role="alert">
                <strong class="font-bold">Success!</strong>
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                <strong class="font-bold">Error!</strong>
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
        @endif
    </div>
</div>
<div class="max-w-4xl mx-auto p-6 bg-white shadow rounded-lg mt-6">
    <div class="border-b pb-4 mb-4">
        <h1 class="text-3xl font-bold text-gray-900">{{ $job->job_title }}</h1>
        <p class="text-sm text-gray-500">Posted {{ $job->created_at->diffForHumans() }}</p>
    </div>

    <div class="grid grid-cols-2 gap-4 mb-6 text-sm text-gray-700">
        <div><strong>Location:</strong> {{ $job->province }}, {{ $job->town }}, {{ $job->barangay }}</div>
        <div><strong>Type:</strong> {{ $job->job_type }}</div>
        <div><strong>Distance based on your work preferences:</strong> {{ round($job->distance, 1) }} km away</div>
        <!-- Added: Salary Display in the grid -->
        <div>
            <strong>Salary / Compensation:</strong>
            {{ $job->salary_range ? '₱' . number_format($job->salary_range, 2) : 'Not Specified' }}
        </div>
    </div>

    <div class="prose max-w-none text-gray-800">
        <h3 class="text-xl font-semibold mb-2">Job Summary</h3>
        <div class="mb-6 leading-relaxed">
            {!! $job->job_summary !!}
        </div>
        <h3 class="text-xl font-semibold mb-2">Job Description</h3>
        <div class="mb-6 leading-relaxed">
            {!! $job->job_description !!}
        </div>

        <h3 class="text-xl font-semibold mt-6 mb-2">Requirements</h3>
        <div class="leading-relaxed">
            {!! $job->job_requirements !!}
        </div>
    </div>

    <div class="mt-8">
        <a href="{{ url()->previous() }}" class="text-blue-600 hover:underline">&larr; Back to Recommendations</a>
    </div>
</div>
<!-- Main Buttons Layout Wrapper -->
<div class="mt-8 pt-6 border-t border-gray-200 flex flex-col sm:flex-row items-center justify-between gap-4">

    @php
        // 1. Check if the authenticated user is marked as 'hired' for this specific job
        $isHired = auth()->user()->interviewingJobs()
            ->where('job_interviewees.job_id', $job->job_id)
            ->where('job_interviewees.status', 'hired')
            ->exists();

        // 2. Check if the user has applied to this job
        $hasApplied = auth()->user()->appliedJobs->contains('job_id', $job->job_id);
    @endphp

    <div class="flex flex-col sm:flex-row items-center gap-4 w-full sm:w-auto">
        @if($isHired)
            <button type="button" disabled class="w-full sm:w-auto px-6 py-3 bg-green-100 text-green-800 font-bold text-center rounded-lg border border-green-300 cursor-not-allowed tracking-wide">
                🎉 Hired
            </button>

        @elseif($hasApplied)
            <div class="flex flex-col sm:flex-row items-center gap-3 w-full sm:w-auto">
                <button type="button" disabled class="w-full sm:w-auto px-6 py-3 bg-gray-300 text-gray-600 font-medium text-center rounded-lg cursor-not-allowed">
                    Applied
                </button>

                <form action="{{ route('jobs_cancel', $job->job_id) }}" method="POST" class="w-full sm:w-auto">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="w-full sm:w-auto px-5 py-3 bg-red-600 hover:bg-red-700 text-white font-medium text-center rounded-lg transition duration-150 ease-in-out">
                        Cancel Application
                    </button>
                </form>
            </div>

        @else
            <form action="{{ route('profile_review', $job->job_id) }}" class="w-full sm:w-auto">
                @csrf
                <button type="submit" class="w-full sm:w-auto px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-medium text-center rounded-lg shadow transition duration-150 ease-in-out">
                    Apply Now
                </button>
            </form>
        @endif
    </div>

    @if(!$isHired)
        <form action="{{ route('jobs_save', $job->job_id) }}" method="POST" class="w-full sm:w-auto">
            @csrf

            @if(Auth::user()->savedJobs()->where('job_saves.job_id', $job->job_id)->exists())
                <button type="submit" class="w-full sm:w-auto px-4 py-2 bg-blue-50 text-blue-700 border border-blue-300 font-medium text-center rounded-lg transition duration-150 ease-in-out flex items-center justify-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4 text-blue-600">
                        <path fill-rule="evenodd" d="M6.32 2.577a49.255 49.255 0 0 1 11.36 0c1.497.174 2.57 1.46 2.57 2.93V21a.75.75 0 0 1-1.085.67L12 18.089l-7.165 3.583A.75.75 0 0 1 3.75 21V5.507c0-1.47 1.073-2.756 2.57-2.93Z" clip-rule="evenodd" />
                    </svg>
                    Saved
                </button>
            @else
                <button type="submit" class="w-full sm:w-auto px-4 py-2 bg-white hover:bg-gray-50 text-gray-700 border border-gray-300 font-medium text-center rounded-lg transition duration-150 ease-in-out flex items-center justify-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 text-gray-500">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17.593 3.322c1.1.128 1.907 1.077 1.907 2.185V21L12 17.25 4.5 21V5.507c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0 1 11.186 0Z" />
                    </svg>
                    Save Job
                </button>
            @endif
        </form>
    @endif

</div>
</x-app-layout>

