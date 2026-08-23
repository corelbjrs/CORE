<x-app-layout>

<div x-data="{
        openModal: false,
        openEducationModal: false,
        openSummaryModal: false,
        modalType: 'add',
        educationForm: {
            degree: '',
            school: '',
            year: '',
            skills_raw: ''
        },
        summaryForm: {
            summary: ''
        },
        openEditModal(degree, school, year, skills) {
            this.modalType = 'edit';
            this.educationForm = { degree, school, year, skills_raw: skills };
            this.openEducationModal = true;
        },
        openAddModal() {
            this.modalType = 'add';
            this.educationForm = { degree: '', school: '', year: '', skills_raw: '' };
            this.openEducationModal = true;
        },
        openEditSummary(existingSummary) {
            this.summaryForm.summary = existingSummary;
            this.openSummaryModal = true;
        }
     }"
     @keydown.escape.window="openModal = false; openEducationModal = false; openSummaryModal = false;"
     class="max-w-4xl mx-auto my-8 p-6 bg-slate-50 text-slate-800 antialiased font-sans space-y-6">
    <!-- Information Banner: Importance of Profile Completion -->
    <div class="bg-gradient-to-r from-indigo-50 to-blue-50 border border-indigo-100/80 rounded-2xl p-5 flex gap-4 shadow-xs">
        <div class="p-2.5 bg-indigo-500 text-white rounded-xl h-fit shrink-0 shadow-sm">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6">
                <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 111.063.852l-.708 2.836a.75.75 0 001.063.852l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />
            </svg>
        </div>
        <div class="space-y-1">
            <h2 class="text-base font-bold text-indigo-950">Complete Your Profile to Get Discovered</h2>
            <p class="text-sm text-indigo-900/80 leading-relaxed">
                A complete profile is your key to unlocking opportunities! Employers use your contact info, location details, education history, and skills to match you with ideal livelihood and job placements. Please make sure to fill out your profile completely to increase your chances of being noticed and reached.
            </p>
        </div>
    </div>
    <!-- Unified Profile Section Card -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <!-- Section Header -->
        <div class="px-6 py-5 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
            <div>
                <span class="text-xs font-semibold uppercase tracking-wider text-slate-400">Profile Information</span>
                <h1 class="text-3xl font-bold text-slate-900 tracking-tight mt-1">
                    {{ trim(($user->firstname ?? '') . ' ' . ($user->middlename ?? '') . ' ' . ($user->lastname ?? '') . ' ' . ($user->ext ?? '')) }}
                </h1>
            </div>

            <button @click="openModal = true" class="flex items-center gap-2 px-4 py-2.5 text-sm font-semibold text-indigo-600 bg-indigo-50 hover:bg-indigo-100/80 active:bg-indigo-100 rounded-xl transition-all shadow-sm" title="Edit Profile">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Zm0 0L19.5 7.125" />
                </svg>
                <span>Edit Profile</span>
            </button>
        </div>

        <!-- Details Grid -->
        <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-8">
            <!-- Left Side: Contact & Location Info -->
            <div class="space-y-4">
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400">Contact & Location</h3>
                <div class="space-y-3">
                    @if($user->barangay || $user->town || $user->province)
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
                                    {{ implode(', ', array_filter([$user->barangay, $user->town, $user->province])) }}
                                </p>
                            </div>
                        </div>
                    @endif

                    @if($user->email)
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
                    @endif

                    <!-- Mobile Number Display -->
                    <div class="flex items-start gap-3 text-sm text-slate-600">
                        <div class="p-1 bg-slate-50 rounded-lg text-slate-400 mt-0.5">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 1.5H8.25A2.25 2.25 0 0 0 6 3.75v16.5a2.25 2.25 0 0 0 2.25 2.25h7.5A2.25 2.25 0 0 0 18 20.25V3.75a2.25 2.25 0 0 0-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-3 18.75h3" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Mobile Number</p>
                            <p class="font-medium text-slate-700 mt-0.5">{{ $user->mobile_no ?? 'Not Specified' }}</p>
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
                            <p class="font-medium text-slate-700 mt-0.5">{{ $user->telephone_no ?? 'Not Specified' }}</p>
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
                            <p class="text-sm font-semibold text-slate-800">{{ $user->sex ?? 'Not Specified' }}</p>
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
                            <p class="text-sm font-semibold text-slate-800">{{ $user->civil_status ?? 'Not Specified' }}</p>
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
                <p class="text-sm text-slate-400 mt-1">Your academic qualifications and the skills associated with them.</p>
            </div>
            <button @click="openAddModal()" class="flex items-center gap-2 px-4 py-2.5 text-sm font-semibold text-white bg-[#2b3a8f] hover:bg-[#202c70] active:bg-[#1a245c] rounded-xl transition-all shadow-sm focus:outline-none">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                <span>Add Details</span>
            </button>
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

                <!-- Optional: Edit Trigger Button -->
                <button @click="openEditModal('{{ addslashes($rawSkillsString ?? '') }}')"
                        class="absolute top-6 right-6 text-[#475569] hover:text-[#1e2d56] transition-colors focus:outline-none"
                        aria-label="Edit details">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                    </svg>
                </button>

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
        @if(!empty($user->about_me))
            <p class="text-sm text-slate-600 leading-relaxed">{{ $user->about_me }}</p>
            <button @click="openEditSummary('{{ addslashes($user->about_me) }}')" class="px-5 py-2 text-sm font-semibold text-indigo-700 bg-white border-2 border-indigo-700 rounded-xl hover:bg-indigo-50/50 active:bg-indigo-50 transition-colors">
                Edit summary
            </button>
        @else
            <p class="text-sm text-slate-600 leading-relaxed">
                Add a personal summary to your profile as a way to introduce who you are.
            </p>
            <button @click="openEditSummary('')" class="px-5 py-2 text-sm font-semibold text-indigo-700 bg-white border-2 border-indigo-700 rounded-xl hover:bg-indigo-50/50 active:bg-indigo-50 transition-colors">
                Add summary
            </button>
        @endif
    </div>

    <!-- ========================================================================= -->
    <!-- PROFILE EDIT MODAL -->
    <!-- ========================================================================= -->
    <div x-show="openModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;"
         x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
        <div class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm" @click="openModal = false"></div>
        <div class="flex min-h-full items-center justify-center p-4 text-center">
            <div x-show="openModal"
                 x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-xl transition-all my-8 w-full max-w-2xl border border-slate-100">
                <div class="px-6 py-4 flex justify-between items-center border-b border-slate-100">
                    <h3 class="text-xl font-bold text-slate-900 tracking-tight">Edit Profile Details</h3>
                    <button @click="openModal = false" class="text-slate-400 hover:text-slate-600 rounded-lg p-1.5 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" /></svg>
                    </button>
                </div>
                <form action="{{ route('contact_details', ['idno' => $user->idno]) }}" method="POST" class="space-y-6 p-6">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="job_id" value="{{ $job_id ?? $job->job_id }}">
                    <!-- Name Grid -->
                    <div class="space-y-3">
                        <h4 class="text-xs font-bold uppercase tracking-wider text-indigo-600">Full Name</h4>
                        <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
                            <div>
                                <label for="firstname" class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">First Name</label>
                                <input type="text" name="firstname" id="firstname" value="{{ $user->firstname }}" class="w-full rounded-xl border border-slate-200 bg-slate-50 text-sm font-medium text-slate-800 focus:border-indigo-500 focus:bg-white focus:ring-1 focus:ring-indigo-500 transition-all p-2.5">
                            </div>
                            <div>
                                <label for="middlename" class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">Middle Name</label>
                                <input type="text" name="middlename" id="middlename" value="{{ $user->middlename }}" class="w-full rounded-xl border border-slate-200 bg-slate-50 text-sm font-medium text-slate-800 focus:border-indigo-500 focus:bg-white focus:ring-1 focus:ring-indigo-500 transition-all p-2.5">
                            </div>
                            <div>
                                <label for="lastname" class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">Last Name</label>
                                <input type="text" name="lastname" id="lastname" value="{{ $user->lastname }}" class="w-full rounded-xl border border-slate-200 bg-slate-50 text-sm font-medium text-slate-800 focus:border-indigo-500 focus:bg-white focus:ring-1 focus:ring-indigo-500 transition-all p-2.5">
                            </div>
                            <div>
                                <label for="ext" class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">Suffix</label>
                                <input type="text" name="ext" id="ext" value="{{ $user->ext }}" class="w-full rounded-xl border border-slate-200 bg-slate-50 text-sm font-medium text-slate-800 focus:border-indigo-500 focus:bg-white focus:ring-1 focus:ring-indigo-500 transition-all p-2.5">
                            </div>
                        </div>
                    </div>

                    <!-- Contact Details -->
                    <div class="space-y-3">
                        <h4 class="text-xs font-bold uppercase tracking-wider text-indigo-600">Contact Details</h4>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div>
                                <label for="email" class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">Email Address</label>
                                <input type="email" name="email" id="email" value="{{ $userEmail->email }}" class="w-full rounded-xl border border-slate-200 bg-slate-50 text-sm font-medium text-slate-800 focus:border-indigo-500 focus:bg-white focus:ring-1 focus:ring-indigo-500 transition-all p-2.5">
                            </div>
                            <div>
                                <label for="mobile_no" class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">Mobile No.</label>
                                <input type="text" name="mobile_no" id="mobile_no" value="{{ $user->mobile_no }}" placeholder="e.g. 09171234567" class="w-full rounded-xl border border-slate-200 bg-slate-50 text-sm font-medium text-slate-800 focus:border-indigo-500 focus:bg-white focus:ring-1 focus:ring-indigo-500 transition-all p-2.5">
                            </div>
                            <div>
                                <label for="tel_no" class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">Telephone No.</label>
                                <input type="text" name="tel_no" id="tel_no" value="{{ $user->telephone_no }}" placeholder="e.g. (02) 8123-4567" class="w-full rounded-xl border border-slate-200 bg-slate-50 text-sm font-medium text-slate-800 focus:border-indigo-500 focus:bg-white focus:ring-1 focus:ring-indigo-500 transition-all p-2.5">
                            </div>
                        </div>
                    </div>

                    <!-- Demographics Grid -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="sex" class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">Sex</label>
                            <select name="sex" id="sex" class="w-full rounded-xl border border-slate-200 bg-slate-50 text-sm font-medium text-slate-800 focus:border-indigo-500 focus:bg-white focus:ring-1 focus:ring-indigo-500 transition-all p-2.5">
                                
                                <option value="" {{ !old('sex', $user->sex) ? 'selected' : '' }}>
                                    {{ $user->sex ? 'Current: ' . ($user->sex === 'M' ? 'Male' : ($user->sex === 'F' ? 'Female' : $user->sex)) : 'Select Sex' }}
                                </option>
                                
                                <option value="Male" {{ in_array(old('sex', $user->sex), ['Male', 'M']) ? 'selected' : '' }}>Male</option>
                                <option value="Female" {{ in_array(old('sex', $user->sex), ['Female', 'F']) ? 'selected' : '' }}>Female</option>
                            </select>
                        </div>
                        <div>
                            <label for="civil_status" class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">Civil Status</label>
                            <select name="civil_status" id="civil_status" class="w-full rounded-xl border border-slate-200 bg-slate-50 text-sm font-medium text-slate-800 focus:border-indigo-500 focus:bg-white focus:ring-1 focus:ring-indigo-500 transition-all p-2.5">
                                <option value="" {{ !old('civil_status', $user->civil_status) ? 'selected' : '' }}>
                                    {{ $user->civil_status ? 'Current: ' . $user->civil_status : 'Select Status' }}
                                </option>
                                <option value="Single" {{ old('civil_status', $user->civil_status) === 'Single' ? 'selected' : '' }}>Single</option>
                                <option value="Married" {{ old('civil_status', $user->civil_status) === 'Married' ? 'selected' : '' }}>Married</option>
                                <option value="Widowed" {{ old('civil_status', $user->civil_status) === 'Widowed' ? 'selected' : '' }}>Widowed</option>
                                <option value="Divorced" {{ old('civil_status', $user->civil_status) === 'Divorced' ? 'selected' : '' }}>Divorced</option>
                            </select>
                        </div>
                    </div>

                    <!-- Location Grid -->
                    <div class="space-y-3">
                        <h4 class="text-xs font-bold uppercase tracking-wider text-indigo-600">Location Details</h4>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div>
                                <label for="barangay" class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">Barangay</label>
                                <input type="text" name="barangay" id="barangay" value="{{ $user->barangay }}" class="w-full rounded-xl border border-slate-200 bg-slate-50 text-sm font-medium text-slate-800 focus:border-indigo-500 focus:bg-white focus:ring-1 focus:ring-indigo-500 transition-all p-2.5">
                            </div>
                            <div>
                                <label for="town" class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">Town / City</label>
                                <input type="text" name="town" id="town" value="{{ $user->town }}" class="w-full rounded-xl border border-slate-200 bg-slate-50 text-sm font-medium text-slate-800 focus:border-indigo-500 focus:bg-white focus:ring-1 focus:ring-indigo-500 transition-all p-2.5">
                            </div>
                            <div>
                                <label for="province" class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">Province</label>
                                <input type="text" name="province" id="province" value="{{ $user->province }}" class="w-full rounded-xl border border-slate-200 bg-slate-50 text-sm font-medium text-slate-800 focus:border-indigo-500 focus:bg-white focus:ring-1 focus:ring-indigo-500 transition-all p-2.5">
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-4 mt-6 border-t border-slate-100">
                        <button type="button" @click="openModal = false" class="px-4 py-2 text-sm font-semibold text-slate-600 border border-slate-200 rounded-xl hover:bg-slate-50 active:bg-slate-100 transition-colors">Cancel</button>
                        <button type="submit" class="px-5 py-2 text-sm font-semibold text-white bg-indigo-600 rounded-xl hover:bg-indigo-700 transition-colors shadow-sm">Save changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- COMBINED EDUCATION & SKILLS MODAL -->
    <!-- ========================================================================= -->
    <div x-show="openEducationModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;"
        x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
        <div class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm" @click="openEducationModal = false"></div>
        <div class="flex min-h-full items-center justify-center p-4 text-center">
            <div x-show="openEducationModal"
                x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-xl transition-all my-8 w-full max-w-lg border border-slate-100">
                <div class="px-6 py-4 flex justify-between items-center border-b border-slate-100">
                    <h3 class="text-xl font-bold text-[#1e2d56] tracking-tight" x-text="modalType === 'edit' ? 'Edit Course & Skills' : 'Add Course & Skills'"></h3>
                    <button @click="openEducationModal = false" class="text-slate-400 hover:text-slate-600 rounded-lg p-1.5 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" /></svg>
                    </button>
                </div>
                <form class="space-y-4 p-6" action="{{ route('course_skills', $user->idno) }}" method="POST">
                    @csrf
                    <template x-if="modalType === 'edit'">
                        {{-- <input type="hidden" name="_method" value="PUT"> --}}
                    </template>
                    <div>
                        <label for="job_category" class="block text-sm font-medium text-gray-700 mb-1 mt-3">Category of Job <span class="text-red-700">*</span></label>
                        <select id="job_category" name="job_category"
                            class="block w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 {{ $errors->has('job_category') ? 'border-red-500 ring-red-500' : '' }}">
                            <option value=""></option>
                            @foreach($expertise as $item)
                                <option value="{{ $item->id }}">{{ $item->area_of_expertise }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1 mt-3">
                            Course <span class="text-red-700">*</span>
                        </label>
                        <div id="courses-container" class="block w-full rounded-md shadow-sm border bg-white max-h-48 overflow-y-auto p-3 focus-within:border-indigo-500 focus-within:ring-1 focus-within:ring-indigo-500
                            @error('course_id') border-red-500 @else border-gray-300 @enderror">
                            <p class="text-sm text-gray-400">Select a Job Category first...</p>
                        </div>
                        @error('course_id')
                            <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>
                    <div>
                        <label for="school" class="block text-xs font-semibold uppercase tracking-wider text-[#64748b] mb-1">School / University</label>
                        <input type="text" name="school" id="school" x-model="educationForm.school" required
                            class="w-full rounded-xl border border-slate-200 bg-slate-50 text-sm font-medium text-slate-800 focus:border-[#2b3a8f] focus:bg-white focus:ring-1 focus:ring-[#2b3a8f] transition-all p-2.5">
                    </div>
                    <div>
                        <label for="year" class="block text-xs font-semibold uppercase tracking-wider text-[#64748b] mb-1">Year Completed</label>
                        <input type="text" name="year" id="year" x-model="educationForm.year" placeholder="e.g. 2014" required
                            class="w-full rounded-xl border border-slate-200 bg-slate-50 text-sm font-medium text-slate-800 focus:border-[#2b3a8f] focus:bg-white focus:ring-1 focus:ring-[#2b3a8f] transition-all p-2.5">
                    </div>
                    <div>
                        <label for="course_skills" class="block text-xs font-semibold uppercase tracking-wider text-[#64748b] mb-1">Skills Gained in this Course (Comma Separated)</label>

                        <div id="skills-container" class="block w-full rounded-md shadow-sm border bg-white max-h-48 overflow-y-auto p-3 focus-within:border-indigo-500 focus-within:ring-1 focus-within:ring-indigo-500
                            @error('skills_required') border-red-500 @else border-gray-300 @enderror">
                            <p class="text-sm text-gray-400">Select a Job Category first...</p>
                        </div>
                        @error('skills_required')
                            <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>
                    <script>
                        // 1. Grab elements from the DOM
                        const jobCategoryDropdown = document.getElementById('job_category');
                        const skillsContainer = document.getElementById('skills-container');
                        const coursesContainer = document.getElementById('courses-container');

                        // Helper: Converts "computer system" to "Computer System"
                        function titleCase(str) {
                            return str.toLowerCase()
                                .split(' ')
                                .map(word => word.charAt(0).toUpperCase() + word.slice(1))
                                .join(' ');
                        }

                        // Helper: Safely generates a unique ID string by removing spaces/special characters
                        function cleanId(string) {
                            return string.replace(/[^a-zA-Z0-9]/g, '-').toLowerCase();
                        }

                        // 2. Listen for "Areas of Expertise" dropdown changes
                        jobCategoryDropdown.addEventListener('change', function() {
                            const expertiseId = this.value;

                            // If nothing is selected, display empty placeholders and stop
                            if (!expertiseId) {
                                skillsContainer.innerHTML = '<p class="text-sm text-gray-400">Select a Job Category first...</p>';
                                coursesContainer.innerHTML = '<p class="text-sm text-gray-400">Select a Job Category first...</p>';
                                return;
                            }

                            // Show a temporary loading state
                            skillsContainer.innerHTML = '<p class="text-sm text-gray-500 animate-pulse">Loading skills...</p>';
                            coursesContainer.innerHTML = '<p class="text-sm text-gray-500 animate-pulse">Loading courses...</p>';

                            // --- FETCH SKILLS (Checkboxes Intact) ---
                            fetch(`/get-skills/${expertiseId}`)
                                .then(response => response.json())
                                .then(skills => {
                                    skillsContainer.innerHTML = ''; // Clear container

                                    if (skills.length === 0) {
                                        skillsContainer.innerHTML = '<p class="text-sm text-gray-500">No skills available for this category.</p>';
                                        return;
                                    }

                                    skills.forEach((skill, index) => {
                                        const uniqueId = `skill-${cleanId(skill)}-${index}`;

                                        const div = document.createElement('div');
                                        div.className = 'flex items-center mb-2 last:mb-0';

                                        const checkbox = document.createElement('input');
                                        checkbox.type = 'checkbox';
                                        checkbox.id = uniqueId;
                                        checkbox.name = 'skills_required[]';
                                        checkbox.value = skill;
                                        checkbox.className = 'h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 cursor-pointer';

                                        const label = document.createElement('label');
                                        label.htmlFor = uniqueId;
                                        label.className = 'ml-2 text-sm text-gray-700 cursor-pointer select-none';
                                        label.textContent = titleCase(skill);

                                        div.appendChild(checkbox);
                                        div.appendChild(label);
                                        skillsContainer.appendChild(div);
                                    });
                                })
                                .catch(error => {
                                    console.error('Error fetching skills:', error);
                                    skillsContainer.innerHTML = '<p class="text-sm text-red-500">Failed to load skills.</p>';
                                });

                            // --- FETCH COURSES (Converted to Dropdown Select) ---
                            fetch(`/get-courses/${expertiseId}`)
                                .then(response => response.json())
                                .then(courses => {
                                    coursesContainer.innerHTML = ''; // Clear container

                                    if (courses.length === 0) {
                                        coursesContainer.innerHTML = '<p class="text-sm text-gray-500">No courses available for this category.</p>';
                                        return;
                                    }

                                    // Create the dropdown select element
                                    const select = document.createElement('select');
                                    select.name = 'course_id[]';
                                    select.id = 'course_id';
                                    select.className = 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm py-2 px-3';

                                    // Default placeholder option
                                    const defaultOption = document.createElement('option');
                                    defaultOption.value = '';
                                    defaultOption.textContent = '-- Select Course --';
                                    select.appendChild(defaultOption);

                                    // Group courses by their educ_level property
                                    const groupedCourses = courses.reduce((groups, course) => {
                                        const level = course.educ_level || 'Other';
                                        if (!groups[level]) {
                                            groups[level] = [];
                                        }
                                        groups[level].push(course);
                                        return groups;
                                    }, {});

                                    // Build <optgroup> elements for each education level
                                    Object.keys(groupedCourses).forEach(level => {
                                        const optGroup = document.createElement('optgroup');
                                        optGroup.label = titleCase(level);

                                        groupedCourses[level].forEach(course => {
                                            const option = document.createElement('option');
                                            option.value = course.id;
                                            option.textContent = course.display_name;
                                            optGroup.appendChild(option);
                                        });

                                        select.appendChild(optGroup);
                                    });

                                    coursesContainer.appendChild(select);
                                })
                                .catch(error => {
                                    console.error('Error fetching courses:', error);
                                    coursesContainer.innerHTML = '<p class="text-sm text-red-500">Failed to load courses.</p>';
                                });
                        });
                    </script>
                    <div class="flex items-center justify-end gap-3 pt-4 mt-6 border-t border-slate-100">
                        <button type="button" @click="openEducationModal = false" class="px-4 py-2 text-sm font-semibold text-slate-600 border border-slate-200 rounded-xl hover:bg-slate-50 active:bg-slate-100 transition-colors">Cancel</button>
                        <button type="submit" class="px-5 py-2 text-sm font-semibold text-white bg-[#2b3a8f] rounded-xl hover:bg-[#202c70] transition-colors shadow-sm">Save Course & Skills</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- PERSONAL SUMMARY MODAL -->
    <!-- ========================================================================= -->
    <div x-show="openSummaryModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;"
        x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
        <div class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm" @click="openSummaryModal = false"></div>
        <div class="flex min-h-full items-center justify-center p-4 text-center">
            <div x-show="openSummaryModal"
                x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-xl transition-all my-8 w-full max-w-xl border border-slate-100">
                <div class="px-6 py-4 flex justify-between items-center border-b border-slate-100">
                    <h3 class="text-xl font-bold text-slate-900 tracking-tight">Edit Personal Summary</h3>
                    <button @click="openSummaryModal = false" class="text-slate-400 hover:text-slate-600 rounded-lg p-1.5 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" /></svg>
                    </button>
                </div>
                <form action="{{ route('about.update', $user->idno) }}" method="POST" class="space-y-4 p-6">
                    @csrf @method('PUT')
                    <div>
                        <label for="summary" class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">Introduction / Summary</label>
                        <textarea name="about_me" id="about_me" x-model="summaryForm.summary" rows="5" required placeholder="Introduce yourself, your academic experience, or career goals..."
                            class="w-full rounded-xl border border-slate-200 bg-slate-50 text-sm font-medium text-slate-800 focus:border-indigo-500 focus:bg-white focus:ring-1 focus:ring-indigo-500 transition-all p-2.5 resize-y">{{ $user->about_me }}</textarea>
                    </div>
                    <div class="flex items-center justify-end gap-3 pt-4 mt-6 border-t border-slate-100">
                        <button type="button" @click="openSummaryModal = false" class="px-4 py-2 text-sm font-semibold text-slate-600 border border-slate-200 rounded-xl hover:bg-slate-50 active:bg-slate-100 transition-colors">Cancel</button>
                        <button type="submit" class="px-5 py-2 text-sm font-semibold text-white bg-indigo-600 rounded-xl hover:bg-indigo-700 transition-colors shadow-sm">Save Summary</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
    <form action="{{ route('jobs_apply', $job->job_id) }}" method="POST" class="mt-6">
        @csrf
        <button type="submit" class="px-6 py-3 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg shadow">
            Confirm & Submit Application
        </button>
    </form>
</x-app-layout>
