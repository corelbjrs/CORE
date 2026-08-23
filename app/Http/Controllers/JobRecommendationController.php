<?php

namespace App\Http\Controllers;

use App\Models\JobPosting;
use App\Models\Expertise;
use App\Models\EducationalDetail;
use App\Models\UserDetails;
use App\Models\WorkDetails;
use App\Models\Employer;
use App\Http\Requests\StoreJobApplyRequest;
use App\Http\Requests\StoreJobSaveRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
class JobRecommendationController extends Controller
{
    /**
     * Display the recommended job listings.
     */

    public function index(Request $request)
    {
        // 1. Fetch user and validation checks
        $applicant = UserDetails::where('idno', Auth::user()->idno)->first();
        $workDetail = WorkDetails::where('idno', Auth::user()->idno)->first();

        // Fetch educational profile to retrieve course fields
        $educational = DB::table('educational_details')
            ->where('idno', Auth::user()->idno)
            ->get();

        if (!$applicant) {
            return redirect()->route('address.index')->with('error', 'Please complete your profile first.');
        }
        if (!$educational) {
            return redirect()->route('background.index')->with('error', 'Please complete your educational profile first.');
        }
        if (!$workDetail) {
            return redirect()->route('expertise.process')->with('error', 'Please complete your skills profile first.');
        }
        if (is_null($workDetail->latitude) || is_null($workDetail->longitude)) {
            return redirect()->route('distance.index')->with('error', 'Please update your coordinates.');
        }

        $applicantLat = $workDetail->latitude;
        $applicantLng = $workDetail->longitude;
        $showAll = $request->has('show_all');
        $isSearching = !$showAll && $request->hasAny(['job_type', 'course', 'job_category', 'province', 'town']);

        // ====================================================
        // COLUMN 1 (RED SECTION): PROFILE & GEOSPATIAL MATRIX
        // ====================================================
        $query = JobPosting::select('job_postings.*', 'employers.company_name')
            ->leftJoin('employers', 'job_postings.idno', '=', 'employers.idno')
            ->selectRaw("
                ( 6371 * acos( cos( radians(?) ) * cos( radians( job_postings.latitude ) )
                * cos( radians( job_postings.longitude ) - radians(?) ) + sin( radians(?) )
                * sin( radians( job_postings.latitude ) ) ) ) AS distance, employers.company_logo
            ", [$applicantLat, $applicantLng, $applicantLat]);

        // Apply Active Filters (Manual Search Mode)
        if (!$showAll && $isSearching) {
            if ($request->filled('job_type')) {
                $query->where('job_postings.job_type', $request->input('job_type'));
            }
            if ($request->filled('course')) {
                $searchCourse = $request->input('course');
                $query->where('job_postings.course', 'LIKE', '%' . $searchCourse . '%');
            }
            if ($request->filled('job_category')) {
                $query->where('job_postings.job_category', $request->input('job_category'));
            }
            if ($request->filled('province')) {
                $query->where('job_postings.province', $request->input('province'));
            }
            if ($request->filled('town')) {
                $query->where('job_postings.town', $request->input('town'));
            }
        }

        // Apply Automatic Content Matching (Standard Load Mode)
        $educationDetails = EducationalDetail::where('idno', Auth::user()->idno)->get();

        if (!$showAll && !$isSearching) {
            // Step A: Pluck applicant's courses
            $applicantCourses = $educationDetails
                ->pluck('course_name')
                ->filter()
                ->map(fn($course) => trim($course))
                ->unique()
                ->values()
                ->toArray();

            // Step B: Resolve expertise_ids (Job Categories) linked to user's courses
            // courses.display_name = user_details.educational_level / educational_details.course_name
            $applicantCategoryIds = [];
            if (!empty($applicantCourses)) {
                $applicantCategoryIds = DB::table('courses')
                    ->whereIn('display_name', $applicantCourses)
                    ->pluck('expertise_id')
                    ->filter()
                    ->unique()
                    ->toArray();
            }

            // Step C: Match against course JSON OR job_category
            if (!empty($applicantCourses) || !empty($applicantCategoryIds)) {
                $query->where(function ($q) use ($applicantCourses, $applicantCategoryIds) {
                    // Match by Course (JSON Contains)
                    if (!empty($applicantCourses)) {
                        foreach ($applicantCourses as $course) {
                            $q->orWhereJsonContains('job_postings.course', $course);
                        }
                    }

                    // OR Match by Job Category ID (expertises.id = job_postings.job_category)
                    if (!empty($applicantCategoryIds)) {
                        $q->orWhereIn('job_postings.job_category', $applicantCategoryIds);
                    }
                });
            } else {
                $query->whereRaw('1 = 0');
            }
        }

        // Fetch Content-Geospatial Result Pipeline
        $profileMatchedJobs = $query->orderBy('distance', 'asc')->get();


        // ====================================================
        // COLUMN 2 (GREEN SECTION): COLLABORATIVE FILTERING
        // ====================================================
        $collaborativeJobs = collect();
        $userIdno = Auth::user()->idno;

        $savedJobIds = DB::table('job_saves')->where('user_id', $userIdno)->pluck('job_id')->toArray();
        $interviewJobIds = DB::table('job_interviewees')->where('user_id', $userIdno)->pluck('job_id')->toArray();
        $appliedJobIds = DB::table('job_applications')->where('user_id', $userIdno)->pluck('job_id')->toArray();

        $userHistoryJobIds = array_unique(array_merge($savedJobIds, $interviewJobIds, $appliedJobIds));

        if (!empty($userHistoryJobIds)) {
            $unifiedInteractions = DB::table('job_saves')
                ->select('user_id', 'job_id', DB::raw('1 as score'))
                ->unionAll(
                    DB::table('job_interviewees')->select('user_id', 'job_id', DB::raw('3 as score'))
                )
                ->unionAll(
                    DB::table('job_applications')->select('user_id', 'job_id', DB::raw('5 as score'))
                );

            $recommendedItemScores = DB::table(DB::raw("({$unifiedInteractions->toSql()}) as target_history"))
                ->mergeBindings($unifiedInteractions)
                ->join(DB::raw("({$unifiedInteractions->toSql()}) as peer_history"), 'target_history.user_id', '=', 'peer_history.user_id')
                ->mergeBindings($unifiedInteractions)
                ->select('peer_history.job_id', DB::raw('SUM(peer_history.score) as co_occurrence_score'))
                ->whereIn('target_history.job_id', $userHistoryJobIds)
                ->whereNotIn('peer_history.job_id', $userHistoryJobIds)
                ->groupBy('peer_history.job_id')
                ->orderByDesc('co_occurrence_score')
                ->take(4)
                ->pluck('job_id')
                ->toArray();

            if (!empty($recommendedItemScores)) {
                $orderedIdsString = "'" . implode("','", $recommendedItemScores) . "'";

                $collaborativeJobs = JobPosting::whereIn('job_id', $recommendedItemScores)
                    ->orderByRaw("FIELD(job_id, {$orderedIdsString})")
                    ->get();
            }
        }


        // ====================================================
        // COLUMN 2 ADDITIONS: NEW ADDED JOBS & MOST SAVED JOBS
        // ====================================================

        // 1. Newly Added Jobs
        $newlyAddedJobs = JobPosting::select('job_postings.*', 'employers.company_name', 'employers.company_logo')
            ->leftJoin('employers', 'job_postings.idno', '=', 'employers.idno')
            ->where('job_postings.created_at', '>=', now()->subWeek())
            ->latest('job_postings.created_at')
            ->take(5)
            ->get();

        // 2. Most Saved Jobs
        $mostSavedJobIds = DB::table('job_saves')
            ->select('job_id', DB::raw('COUNT(id) as total_saves'))
            ->groupBy('job_id')
            ->orderByDesc('total_saves')
            ->take(5)
            ->pluck('job_id')
            ->toArray();

        $mostSavedJobs = collect();
        if (!empty($mostSavedJobIds)) {
            $orderedSavedIds = "'" . implode("','", $mostSavedJobIds) . "'";
            $mostSavedJobs = JobPosting::select('job_postings.*', 'employers.company_name', 'employers.company_logo')
                ->leftJoin('employers', 'job_postings.idno', '=', 'employers.idno')
                ->whereIn('job_postings.job_id', $mostSavedJobIds)
                ->orderByRaw("FIELD(job_postings.job_id, {$orderedSavedIds})")
                ->get();
        }


        // ====================================================
        // FINAL DATA PACKAGING
        // ====================================================
        $expertise = Expertise::orderBy('id', 'desc')
            ->with(['courses' => fn($q) => $q->orderBy('id', 'desc')])
            ->get();

        $courses = DB::table('courses')->select('display_name')->distinct()->get();

        return view('rec', [
            'jobs'              => $profileMatchedJobs,
            'collaborativeJobs' => $collaborativeJobs,
            'newlyAddedJobs'    => $newlyAddedJobs,
            'mostSavedJobs'     => $mostSavedJobs,
            'courses'           => $courses,
            'expertise'         => $expertise,
        ]);
    }
    public function details($job_id)
    {
        // 1. Fetch user details to get their latitude and longitude coordinates
        $applicant = UserDetails::where('idno', Auth::user()->idno)->first();
        $jobPreference = WorkDetails::where('idno', Auth::user()->idno)->first();

        // Set fallback coordinates if the user profile doesn't exist to avoid breaks
        $applicantLat = $jobPreference->latitude;
        $applicantLng = $jobPreference->longitude;
        // 2. Fetch the job details while calculating the distance on the fly
        $job = JobPosting::select('job_postings.*', 'expertises.area_of_expertise')
            ->join('expertises', 'job_postings.job_category', '=', 'expertises.id')
            ->selectRaw("
                ( 6371 * acos( cos( radians(?) ) * cos( radians( job_postings.latitude ) )
                * cos( radians( job_postings.longitude ) - radians(?) ) + sin( radians(?) )
                * sin( radians( job_postings.latitude ) ) ) ) AS distance
            ", [$applicantLat, $applicantLng, $applicantLat])
            ->where('job_postings.job_id', $job_id)
            ->firstOrFail();

        // Return the full details view with the computed job data
        return view('recd', compact('job'));
    }
    /**
     * Handle the user applying for/saving a job.
     */
    public function applyJob(StoreJobApplyRequest $request, $jobId)
    {
        // 1. Fetch the job posting using your custom unique 'job_id' column
        $job = JobPosting::where('job_id', $jobId)->firstOrFail();
        $user = Auth::user();

        // 2. Check the job_applications table directly using appliedJobs()
        $alreadyApplied = $user->appliedJobs()
            ->where('job_applications.job_id', $jobId)
            ->exists();

        if ($alreadyApplied) {
            return redirect()->back()->with('info', 'You have already applied for this job.');
        }

        // 3. Attach the record to the job_applications pivot table
        // Use $job->job_id to match your database column name property
        $user->appliedJobs()->syncWithoutDetaching([$job->job_id => ['status' => 'applied']]);

        // 4. Typically, you want to redirect back with flash messages instead of returning a view directly.
        // This allows the page to refresh cleanly and show your Tailwind alerts.

        return view('recd', compact('job'));
        // return redirect()->back()->with('success', 'Application submitted successfully!');
    }

    /**
     * Toggle the "Save Job" state (Save / Unsave).
     */
    public function toggleSave(StoreJobSaveRequest $request, $job_id)
    {
        // Find the job using the ID passed from the route
        $job = JobPosting::where('job_id', $job_id)->firstOrFail();

        $user = Auth::user();

        // Check using the true primary key 'id'
        $isSaved = $user->savedJobs()->where('job_saves.job_id', $job->job_id)->exists();

        if ($isSaved) {
            // Detach using the true primary key 'id'
            $user->savedJobs()->detach($job->job_id);
            return redirect()->back()->with('success', 'Job removed from your saved list.');
        }

        // Attach using the true primary key 'id'
        // This inserts the actual database primary key ($job->id) into job_saves.job_id
        $user->savedJobs()->attach($job->job_id, ['status' => 'saved']);

        return redirect()->back()->with('success', 'Job saved successfully!');
    }

    public function cancel(StoreJobApplyRequest $request, $job_id)
    {
        $user = Auth::user();

        // Detach the job to cancel the application
        $user->appliedJobs()->detach($job_id);

        return redirect()->back()->with('success', 'Application withdrawn successfully.');
    }

    public function profile_review($job_id)
    {
        $userEmail = Auth::user();
        $job = JobPosting::where('job_id', $job_id)->first();
        $user = UserDetails::where('idno', auth()->user()->idno)->first();
        $educationalDetails = EducationalDetail::where('idno', auth()->user()->idno)->get();
        $work = WorkDetails::where('idno', auth()->user()->idno)->first();
        $expertise = Expertise::all();
        return view('recp', compact('user', 'job', 'work', 'educationalDetails', 'expertise', 'userEmail'));
    }
}
