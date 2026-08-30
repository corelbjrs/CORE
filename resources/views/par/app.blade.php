<x-app-layout>
    <div class="max-w-4xl mx-auto my-8 p-6 space-y-6 bg-slate-50 text-slate-800 antialiased font-sans">

        <!-- Top Action Buttons -->
        <div class="flex flex-wrap gap-3">
            @if($application->jobPosting->interviewees->contains('idno', $user->idno))
            @php
                // Fetch the specific interviewee record to read its pivot status
                $intervieweeRecord = $application->jobPosting->interviewees->where('idno', $user->idno)->first();
            @endphp

            <div class="flex items-center gap-3">
                @if($intervieweeRecord && $intervieweeRecord->pivot->status === 'interviewee')
                    <form action="{{ route('jobs.removeInterviewe', ['job_id' => $application->job_id, 'idno' => $user->idno]) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="px-5 py-2.5 bg-gray-600 text-white font-semibold rounded-lg hover:bg-gray-700 transition-colors focus:outline-none">
                            Remove from list
                        </button>
                    </form>

                    <form action="{{ route('jobs.hireApplicant', ['job_id' => $application->job_id, 'idno' => $user->idno]) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="px-5 py-2.5 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 transition-colors focus:outline-none">
                            Mark as hired
                        </button>
                    </form>

                @elseif($intervieweeRecord && $intervieweeRecord->pivot->status === 'hired')
                    <span class="px-4 py-2 bg-green-100 text-green-800 font-bold rounded-lg tracking-wide border border-green-200 flex items-center gap-1">
                        🎉 Hired
                    </span>
                @endif
            </div>
        @else
            <form action="{{ route('addToInterviewList', ['job_id' => $application->job_id, 'idno' => $user->idno]) }}" method="POST">
                @csrf
                <button type="submit" class="px-5 py-2.5 bg-[#2b3a8f] text-white font-semibold rounded-lg hover:bg-[#202c70] transition-colors focus:outline-none">
                    Add to list
                </button>
            </form>
        @endif
        </div>

        <!-- Unified Profile Section Card -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
            <!-- Section Header -->
            <div class="px-6 py-5 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
                <div>
                    <span class="text-xs font-semibold uppercase tracking-wider text-slate-400">Profile Information</span>
                    <h1 class="text-3xl font-bold text-slate-900 tracking-tight mt-1">
                        {{ trim(($userDetails->firstname ?? '') . ' ' . ($userDetails->middlename ?? '') . ' ' . ($userDetails->lastname ?? '') . ' ' . ($userDetails->ext ?? '')) }}
                    </h1>
                </div>
            </div>

            <!-- Details Grid -->
            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Left Side: Contact & Location Info -->
                <div class="space-y-4">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400">Contact & Location</h3>
                    <div class="space-y-3">
                        {{-- @if($userB->barangay_name || $userB->town_name || $userB->province) --}}
                            <div class="flex items-start gap-3 text-sm text-slate-600">
                                <div class="p-1 bg-slate-50 rounded-lg text-slate-400 mt-0.5">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Address</p>
                                    <p class="font-medium text-slate-700 mt-0.5">
                                        {{ implode(', ', array_filter([
                                            $userB->barangay_name ?  $userB->barangay_name : null,
                                            $userB->town_name,
                                            $userB->province
                                        ])) }}
                                    </p>
                                </div>
                            </div>
                        {{-- @endif

                        @if($userDetails->email) --}}
                            <div class="flex items-start gap-3 text-sm text-slate-600">
                                <div class="p-1 bg-slate-50 rounded-lg text-slate-400 mt-0.5">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0l-7.5-4.615a2.25 2.25 0 0 1-1.07-1.916V6.75" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Email Address</p>
                                    <a href="mailto:{{ $user->email }}" class="hover:underline text-indigo-600 font-semibold mt-0.5 inline-block">{{ $user->email }}</a>
                                </div>
                            </div>
                        {{-- @endif --}}

                        <!-- Mobile Number Display -->
                        <div class="flex items-start gap-3 text-sm text-slate-600">
                            <div class="p-1 bg-slate-50 rounded-lg text-slate-400 mt-0.5">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 1.5H8.25A2.25 2.25 0 0 0 6 3.75v16.5a2.25 2.25 0 0 0 2.25 2.25h7.5A2.25 2.25 0 0 0 18 20.25V3.75a2.25 2.25 0 0 0-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-3 18.75h3" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Mobile Number</p>
                                <p class="font-medium text-slate-700 mt-0.5">{{ $userDetails->mobile_no ?? 'Not Specified' }}</p>
                            </div>
                        </div>

                        <!-- Telephone Number Display -->
                        <div class="flex items-start gap-3 text-sm text-slate-600">
                            <div class="p-1 bg-slate-50 rounded-lg text-slate-400 mt-0.5">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 0 0 2.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.387a12.035 12.035 0 0 1-7.108-7.108c-.145-.44.02-.927.396-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 0 0-1.091-.852H4.5A2.25 2.25 0 0 0 2.25 4.5v2.25Z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Telephone Number</p>
                                <p class="font-medium text-slate-700 mt-0.5">{{ $userDetails->telephone_no ?? 'Not Specified' }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Side: Personal Identity Details -->
                <div class="space-y-4">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400">Personal Details</h3>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="flex items-center gap-3">
                            <div class="p-2 bg-slate-50 rounded-xl text-slate-400">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs text-slate-400 font-medium uppercase tracking-wider">Sex</p>
                                <p class="text-sm font-semibold text-slate-800">{{ $userDetails->sex ?? 'Not Specified' }}</p>
                            </div>
                        </div>

                        <div class="flex items-center gap-3">
                            <div class="p-2 bg-slate-50 rounded-xl text-slate-400">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs text-slate-400 font-medium uppercase tracking-wider">Civil Status</p>
                                <p class="text-sm font-semibold text-slate-800">{{ $userDetails->civil_status ?? 'Not Specified' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Combined Education & Skills Section Card -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 space-y-6">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h2 class="text-3xl font-bold tracking-tight text-[#1e2d56]">Education & Skills</h2>
                </div>
            </div>

            <!-- Course & Skills Card -->
            <div class="space-y-6">
                @php
                    // Map database educ_level values to user-friendly titles
                    $levelLabels = [
                        'vocational_course'      => 'Vocational Course',
                        'course_associate'       => 'Associate Degree',
                        'course_degree'          => 'Bachelor / Degree Course',
                        'postgrad_course_degree' => 'Postgraduate Degree',
                        'doctoral_course_degree' => 'Doctoral Degree',
                    ];

                    // Ensure $educationalDetails is an iterable collection
                    $educationalRecords = $educationalDetails instanceof \Illuminate\Support\Collection
                        ? $educationalDetails
                        : collect([$educationalDetails])->filter();

                    // Separate records into courses list and skills pool
                    $coursesList = [];
                    $skillsArray = [];

                    foreach ($educationalRecords as $detail) {
                        // Collect course information
                        if (!empty($detail->course_name)) {
                            $levelKey = $detail->educ_level ?? 'course_degree';
                            $levelName = $levelLabels[$levelKey] ?? 'Degree Course';

                            $coursesList[] = [
                                'level'          => $levelName,
                                'course_name'    => $detail->course_name,
                                'school'         => $detail->school ?? null,
                                'year_graduated' => $detail->year_graduated ?? null,
                            ];
                        }

                        // Extract and aggregate comma-separated skills across all entries
                        if (!empty($detail->skills)) {
                            if (is_array($detail->skills)) {
                                $skillsArray = array_merge($skillsArray, $detail->skills);
                            } else {
                                $extracted = array_filter(array_map('trim', explode(',', $detail->skills)));
                                $skillsArray = array_merge($skillsArray, $extracted);
                            }
                        }
                    }

                    // Clean & de-duplicate aggregated skills
                    $skillsArray = array_unique($skillsArray);
                    $rawSkillsString = implode(', ', $skillsArray);
                @endphp

                @if(!empty($coursesList) || !empty($skillsArray))
                    <div class="bg-white border border-[#e2e8f0] rounded-2xl p-6 relative shadow-xs">


                        <h3 class="text-xl font-bold text-[#1e2d56] mb-4">Academic Profile</h3>

                        <!-- 1. Display All Courses for this IDNO -->
                        @if(!empty($coursesList))
                            <div class="border-t border-slate-100 py-4 space-y-3">
                                <div class="flex items-center justify-between mb-2">
                                    <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400">
                                        Courses / Degrees Obtained ({{ count($coursesList) }})
                                    </h4>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    @foreach($coursesList as $course)
                                        <div class="bg-slate-50 border border-slate-100 rounded-xl p-3.5 flex flex-col justify-between">
                                            <div>
                                                <span class="text-[10px] font-bold uppercase tracking-wider text-[#2b3a8f] mb-1 block">
                                                    {{ $course['level'] }}
                                                </span>
                                                <h5 class="text-sm font-semibold text-slate-800">
                                                    {{ $course['course_name'] }}
                                                </h5>
                                            </div>

                                            <!-- Display optional school / year info if present -->
                                            @if($course['school'] || $course['year_graduated'])
                                                <div class="mt-2 pt-2 border-t border-slate-200/60 flex items-center justify-between text-xs text-slate-500">
                                                    <span>{{ $course['school'] ?? 'N/A' }}</span>
                                                    @if($course['year_graduated'])
                                                        <span class="font-medium text-slate-600">{{ $course['year_graduated'] }}</span>
                                                    @endif
                                                </div>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- 2. Display All Associated Skills -->
                        @if(!empty($skillsArray))
                            <div class="border-t border-slate-100 pt-4">
                                <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-3">Skills Acquired</h4>
                                <div class="flex flex-wrap gap-2">
                                    @foreach($skillsArray as $skill)
                                        <span class="px-3.5 py-1.5 bg-[#f4f6f9] text-[#2c3e50] rounded-full text-xs font-medium transition hover:bg-[#eaf0f6]">
                                            {{ $skill }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                    </div>
                @else
                    <!-- Fallback Empty State -->
                    <div class="text-center py-8 border border-dashed border-slate-200 rounded-2xl">
                        <p class="text-sm text-slate-400">No educational course records found for this ID number.</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Personal Summary Section Card -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 space-y-4">
            <h2 class="text-xl font-bold text-slate-900 tracking-tight">Personal summary</h2>
            @if(!empty($userDetails->about_me))
                <p class="text-sm text-slate-600 leading-relaxed">{{ $userDetails->about_me }}</p>

            @else
                <p class="text-sm text-slate-600 leading-relaxed">
                    Add a personal summary to your profile as a way to introduce who you are.
                </p>
                <button @click="openEditSummary('')" class="px-5 py-2 text-sm font-semibold text-indigo-700 bg-white border-2 border-indigo-700 rounded-xl hover:bg-indigo-50/50 active:bg-indigo-50 transition-colors">
                    Add summary
                </button>
            @endif
        </div>

        <!-- Bottom Action Sticky Bar alternative -->
        <div class="flex flex-wrap gap-3 pt-4 border-t border-slate-100">
            @if($application->jobPosting->interviewees->contains('idno', $user->idno))
            @php
                // Fetch the specific interviewee record to read its pivot status
                $intervieweeRecord = $application->jobPosting->interviewees->where('idno', $user->idno)->first();
            @endphp

            <div class="flex items-center gap-3">
                @if($intervieweeRecord && $intervieweeRecord->pivot->status === 'interviewee')
                    <form action="{{ route('jobs.removeInterviewe', ['job_id' => $application->job_id, 'idno' => $user->idno]) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="px-5 py-2.5 bg-gray-600 text-white font-semibold rounded-lg hover:bg-gray-700 transition-colors focus:outline-none">
                            Remove from list
                        </button>
                    </form>

                    <form action="{{ route('jobs.hireApplicant', ['job_id' => $application->job_id, 'idno' => $user->idno]) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="px-5 py-2.5 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 transition-colors focus:outline-none">
                            Mark as hired
                        </button>
                    </form>

                @elseif($intervieweeRecord && $intervieweeRecord->pivot->status === 'hired')
                    <span class="px-4 py-2 bg-green-100 text-green-800 font-bold rounded-lg tracking-wide border border-green-200 flex items-center gap-1">
                        🎉 Hired
                    </span>
                @endif
            </div>
        @else
            <form action="{{ route('addToInterviewList', ['job_id' => $application->job_id, 'idno' => $user->idno]) }}" method="POST">
                @csrf
                <button type="submit" class="px-5 py-2.5 bg-[#2b3a8f] text-white font-semibold rounded-lg hover:bg-[#202c70] transition-colors focus:outline-none">
                    Add to list
                </button>
            </form>
        @endif
        </div>

    </div>
</x-app-layout>
