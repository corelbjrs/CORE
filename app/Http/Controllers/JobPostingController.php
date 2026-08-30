<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Requests\StoreJobPostingRequest;
use App\Http\Requests\UpdateJobPostingRequest;
use App\Http\Requests\UpdateEmployerRequest;
use App\Models\JobPosting;
use App\Models\User;
use App\Models\EducationalDetail;
use App\Models\UserDetails;
use App\Models\Employer;
use App\Models\Expertise;
use App\Models\JobApplication;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class JobPostingController extends Controller
{

    public function index()
    {
        $user = User::where('idno', auth()->user()->idno)->first();
        $expertise = Expertise::all();
        $jobs = JobPosting::where('idno', $user->idno)->latest()->get();

        // FIX: Redirect to the SHOW route, passing the 'code'
        return view('par.lj', compact('user', 'expertise', 'jobs'));
    }

    public function job_post(StoreJobPostingRequest $request)
    {
        $idno = auth()->user()->idno;

        // 1. Get the validated data from your request
        $validatedData = $request->validated();

        // 2. Look up the Town name using the submitted town ID
        $townRecord = \Illuminate\Support\Facades\DB::table('towns')
            ->where('id', $validatedData['town'] ?? null)
            ->select('town')
            ->first();

        // 3. Fetch the coordinates AND the barangay name from the barangays table
        $barangayRecord = \Illuminate\Support\Facades\DB::table('barangays')
            ->where('id', $validatedData['barangay'] ?? null)
            ->select('barangay', 'latitude', 'longitude')
            ->first();

        // 4. UPDATED: Fetch multiple course display names using the submitted course IDs array
        $courseIds = $validatedData['course_id'] ?? [];

        // Query all matching courses and pluck their display names
        $courseNames = \Illuminate\Support\Facades\DB::table('courses')
            ->whereIn('id', is_array($courseIds) ? $courseIds : [$courseIds])
            ->pluck('display_name')
            ->toArray();

        // 5. Overwrite the IDs with text names and merge everything into the final array
        $jobData = array_merge($validatedData, [
            'idno'      => $idno,
            'town'      => $townRecord ? $townRecord->town : null,
            'barangay'  => $barangayRecord ? $barangayRecord->barangay : null,
            'latitude'  => $barangayRecord ? $barangayRecord->latitude : null,
            'longitude' => $barangayRecord ? $barangayRecord->longitude : null,

            // Save the array of course names as a JSON string (recommended)
            // Alternatively, use: implode(', ', $courseNames) if you prefer a comma-separated string
            'course'    => !empty($courseNames) ? json_encode($courseNames) : null,
        ]);

        // 6. Create the Job Posting
        $job = JobPosting::create($jobData);

        return redirect()->route('emp_postc', ['job_id' => $job->job_id])
                        ->with('success', 'Job details, courses, location names, and coordinates saved successfully.');
    }

    // public function getSkillsByExpertise($expertiseId)
    // {
    //     // 1. Find the row matching the selected Area of Expertise ID
    //     $expertise = Expertise::find($expertiseId);

    //     if (!$expertise || empty($expertise->skills)) {
    //         return response()->json([]);
    //     }

    //     // 2. Turn the JSON array string ["skill1", "skill2"] into a clean PHP array
    //     // If your Expertise model already casts 'skills' to an array, you can skip json_decode
    //     $skillsArray = is_array($expertise->skills)
    //         ? $expertise->skills
    //         : json_decode($expertise->skills, true);

    //     // Safety fallback if JSON decoding fails
    //     if (!is_array($skillsArray)) {
    //         $skillsArray = array_map('trim', explode(',', $expertise->skills));
    //     }

    //     // 3. Return the clean array to your JavaScript fetch
    //     return response()->json(array_values($skillsArray));
    // }
    // For your /get-skills/{id} route
    public function getSkillsByExpertise($expertiseId)
    {
        $expertise = Expertise::find($expertiseId);

        if (!$expertise) {
            return response()->json([]);
        }

        // Safely unpack the JSON skills column
        $skillsArray = is_array($expertise->skills)
            ? $expertise->skills
            : json_decode($expertise->skills, true);

        if (!is_array($skillsArray)) {
            $skillsArray = [];
        }

        // Return the flat array directly so JS loops over it smoothly
        return response()->json(array_values($skillsArray));
    }

    // For your /get-courses/{id} route
    public function getCoursesByExpertise($expertiseId)
    {
        $expertise = Expertise::with('courses')->find($expertiseId);

        if (!$expertise) {
            return response()->json([]);
        }

        // Return the plain collection (the updated frontend JavaScript handles grouping)
        return response()->json($expertise->courses);
    }
    public function emp_comp()
    {
        $user = User::where('idno', auth()->user()->idno)->first();

        // Retrieve the employer record corresponding to the authenticated user
        $employer = Employer::where('idno', auth()->user()->idno)->first();

        // Fallback if record doesn't exist yet
        if (!$employer) {
            $employer = new Employer();
        }

        return view('par.emp', compact('user', 'employer'));
    }

    /**
     * Store or update the employer details.
     */
    public function update_emp_comp(UpdateEmployerRequest $request)
    {
        $validatedData = $request->validated();


        if (!empty($validatedData['town'])) {
            $townRecord = DB::table('towns')
                ->where('id', $validatedData['town'])
                ->select('town')
                ->first();

            // Replace the ID with the string name
            $validatedData['town'] = $townRecord ? $townRecord->town : null;
        }

        // 3. Fetch the barangay name and coordinates from the barangays table
        if (!empty($validatedData['brgy'])) {
            $barangayRecord = DB::table('barangays')
                ->where('id', $validatedData['brgy'])
                ->select('barangay')
                ->first();

            if ($barangayRecord) {
                $validatedData['brgy'] = $barangayRecord->barangay;

            }
        }

        // 4. Fetch existing employer record or initialize a new instance
        $employer = Employer::firstOrNew(['idno' => auth()->user()->idno]);

        // 5. Handle file upload for company_logo if present
        if ($request->hasFile('company_logo')) {
            // Delete old logo file if it exists
            if ($employer->company_logo && Storage::disk('public')->exists($employer->company_logo)) {
                Storage::disk('public')->delete($employer->company_logo);
            }

            $idno = auth()->user()->idno;
            $file = $request->file('company_logo');

            // Get original file extension (e.g., png, jpg)
            $extension = $file->getClientOriginalExtension();

            // Construct new filename (e.g., "EMP-1001.png")
            $fileName = $idno . '.' . $extension;

            // Save with the custom filename to 'storage/app/public/logos'
            $path = $file->storeAs('logos', $fileName, 'public');

            $validatedData['company_logo'] = $path;
        }

        // 6. Fill and save database record
        $employer->fill($validatedData);
        $employer->idno = auth()->user()->idno; // Ensure ID link is maintained
        $employer->save();

        return redirect()->back()->with('success', 'Employer profile updated successfully.');
    }
    public function emp_post()
    {
        $expertise = Expertise::all();
        $user = User::where('idno', auth()->user()->idno)->first();
        return view('par.post', compact('user', 'expertise'));
    }
    public function emp_postc($job_id)
    {
        $job = JobPosting::where('job_id', $job_id)->firstOrFail();

        // Pass it to the view
        return view('par.postc', compact('job_id'));
    }
    public function job_postc(UpdateJobPostingRequest $request, $job_id)
    {
        $validatedData = $request->validated();

        $jobdetails = JobPosting::where('job_id', $job_id)->firstOrFail();
        $jobdetails->update($validatedData);

        return redirect()->route('emp_postc', ['job_id' => $job_id])
                 ->with('success', 'User details saved successfully.');
    }
    public function list_jobPosted()
    {
        $idno = auth()->user()->idno;
        $jobs = JobPosting::where('idno', $idno)->latest()->get();
        return view('par.lj', compact('jobs'));
    }
    public function parJobDetails($job_id)
    {
        $jobApp = JobApplication::where('job_id', $job_id)->count();
        $job = JobPosting::where('job_id', $job_id)->firstOrFail();
        return view('par.jd', compact('job','jobApp'));
    }
    public function parListApp($job_id)
    {
        $job = JobPosting::where('job_id', $job_id)->firstOrFail();
        $applicants = $job->applicants;
        return view('par.la', compact('job', 'applicants'));
    }
    public function parAppProfile($idno, $job_id)
    {
        // Queries the exact application instance using both identifier strings
        $application = JobApplication::with(['user.details'])
            ->where('user_id', $idno)
            ->where('job_id', $job_id)
            ->firstOrFail();

        // $user = $application->user;
        $user = User::where('idno', $idno)->first();

        $userB = DB::table('user_details')
        ->leftJoin('barangays', 'user_details.brgy', '=', 'barangays.id') // adjust primary key column if needed
        ->leftJoin('towns', 'user_details.town', '=', 'towns.id')         // adjust primary key column if needed
        ->where('user_details.idno', $idno)
        ->select(
            'user_details.*',
            'barangays.barangay as barangay_name',
            'towns.town as town_name'
        )
        ->first();

        $userDetails = $user->details;
        $educationalDetails = EducationalDetail::where('idno', $idno)->get();
        return view('par.app', compact('application', 'user', 'userDetails', 'userB', 'educationalDetails'));
    }
    public function addToInterviewList(Request $request, $job_id, $idno)
    {
        // Find the job to ensure it exists
        $job = JobPosting::where('job_id', $job_id)->firstOrFail();

        // Use syncWithoutDetaching to link them in the pivot table without creating duplicate records
        $job->interviewees()->syncWithoutDetaching([
            $idno => ['status' => 'interviewee']
        ]);

        return redirect()->back()->with('success', 'Applicant successfully added to the interview list!');
    }
    public function removeFromInterviewList($job_id, $idno)
    {
        $job = JobPosting::where('job_id', $job_id)->firstOrFail();

        // Detach removes the record matching this idno from the pivot table
        $job->interviewees()->detach($idno);

        return redirect()->back()->with('success', 'Applicant successfully removed from the interview list!');
    }
    public function hireApplicant($job_id, $idno)
    {
        $job = JobPosting::where('job_id', $job_id)->firstOrFail();

        // Updates the specific pivot row status field cleanly
        $job->interviewees()->updateExistingPivot($idno, [
            'status' => 'hired'
        ]);

        return redirect()->back()->with('success', 'Applicant status updated to Hired!');
    }
}
