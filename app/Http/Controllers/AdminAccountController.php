<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

use App\Models\User;
use App\Models\UserDetails;
use App\Models\Employer;
use App\Models\JobPosting;
use App\Models\JobApplication;
use App\Models\EducationalDetail;
use App\Models\Barangay;

class AdminAccountController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = User::where('idno', auth()->user()->idno)->first();
        return view('adtv.index', compact('user'));
    }
    public function adtv_addUser()
    {
        $user = User::where('idno', auth()->user()->idno)->first();
        return view('adtv.nu', compact('user'));
    }
    public function adtv_createEmployer()
    {
        $user = User::where('idno', auth()->user()->idno)->first();
        return view('adtv.emp', compact('user'));
    }

    public function adtv_listUsers(): View
    {
        // Fetch and separate users by their account usertype
        $admins = UserDetails::whereHas('account', function ($query) {
            $query->where('usertype', 'admin');
        })->with('account')->get();

        $employers = Employer::whereHas('account', function ($query) {
            $query->where('usertype', 'employer');
        })->with('account')->get();

        $users = UserDetails::whereHas('account', function ($query) {
            $query->where('usertype', 'user');
        })->with('account')->get();

        // Pass all three distinct collections to the blade view
        return view('adtv.lu', compact('admins', 'employers', 'users'));
    }

    /**
     * Optional: Display the filtered list of administrators.
     */
    public function adtv_listAdmins(): View
    {
        // Filters UserDetails where the related account has 'usertype' = 'admin'
        $admins = UserDetails::whereHas('account', function ($query) {
            $query->where('usertype', 'admin');
        })->with('account')->get();

        return view('adtv.la', compact('admins'));
    }
    public function adtv_storeUser(Request $request)
    {
        // 1. Validate both the user credentials and the profile details together
        $validatedData = $request->validate([
            'firstname'     => ['required', 'string', 'max:255'],
            'middlename'    => ['nullable', 'string', 'max:255'],
            'lastname'      => ['required', 'string', 'max:255'],
            'ext'           => ['nullable', 'string', 'max:50'],
            'date_of_birth' => ['required', 'date'],
            'email'         => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password'      => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // 2. Use a transaction to safely save to both tables
        DB::transaction(function () use ($request, $validatedData) {

            // Create the brand new user first
            $newUser = User::create([
                'email'    => $request->email,
                'password' => Hash::make($request->password),
                'usertype' => 'admin',
            ]);

            // Create the user details and link them via the newly generated 'idno'
            UserDetails::create([
                'idno'          => $newUser->idno, // Pulls the automatically generated idno from the model's booted method
                'firstname'     => $request->firstname,
                'middlename'    => $request->middlename,
                'lastname'      => $request->lastname,
                'ext'           => $request->ext,
                'date_of_birth' => $request->date_of_birth,
            ]);
        });

        // 3. Redirect back to your list view
        return redirect()
            ->route('adtv_listUsers')
            ->with('success', 'User and personal details saved successfully.');
    }
    public function store(Request $request): RedirectResponse
    {
        // 1. Validate the combined form data
        $request->validate([
            'firstname'     => ['required', 'string', 'max:255'],
            'middlename'    => ['nullable', 'string', 'max:255'],
            'lastname'      => ['required', 'string', 'max:255'],
            'ext'           => ['nullable', 'string', 'max:50'],
            'date_of_birth' => ['required', 'date'],
            'email'         => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password'      => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // 2. Use a database transaction to execute queries atomically
        DB::transaction(function () use ($request) {

            // Write to 'users' table
            $user = User::create([
                'email'    => $request->email,
                'password' => Hash::make($request->password),
                'usertype' => 'user', // Defaults the new account to standard user
            ]);

            // Write to 'user_details' table using the relationship link
            $user->details()->create([
                'firstname'     => $request->firstname,
                'middlename'    => $request->middlename,
                'lastname'      => $request->lastname,
                'ext'           => $request->ext,
                'date_of_birth' => $request->date_of_birth,
            ]);
        });

        // 3. Return back or to an index table with success status
        return redirect()
            ->route('adtv_listUsers') // Change this route to wherever you want the admin to go next
            ->with('status', 'User and personal details created successfully!');
    }
    public function adtv_storeEmployer(Request $request)
    {
        // 1. Validate inputs (Removed 'password' from request rules since it's auto-generated)
        $validatedData = $request->validate([
            'email'               => ['required', 'email', 'max:50', 'unique:users,email'],
            'company_name'        => ['required', 'string', 'max:50'],
            'type_of_business'    => ['nullable', 'string', 'max:50'],
            'province'            => ['required', 'string', 'max:20'],
            'town'                => ['required'], // ID passed from dropdown
            'brgy'                => ['required'], // ID passed from dropdown
            'address_details'     => ['nullable', 'string', 'max:50'],
            'representative_name' => ['required', 'string', 'max:50'],
            'mobile'              => ['required', 'string', 'max:50'],
            'designation'         => ['required', 'string', 'max:50'],
            'tin'                 => ['nullable', 'string', 'max:15'],
            'about'               => ['nullable', 'string'],
        ]);

        // 2. Fetch town and barangay names
        $town = DB::table('towns')->where('id', $validatedData['town'])->value('town');
        $barangay = DB::table('barangays')->where('id', $validatedData['brgy'])->value('barangay');

        $validatedData['town'] = $town ?? $validatedData['town'];
        $validatedData['brgy'] = $barangay ?? $validatedData['brgy'];

        // 3. Execute Transaction
        DB::transaction(function () use ($validatedData) {
            // Generate a single random password
            // $plainPassword = Str::password(10);

            // temporary password generation for employer account creation
            $plainPassword = '12345678';

            // Create User
            $user = User::create([
                'email'    => $validatedData['email'],
                'password' => Hash::make($plainPassword),
                'usertype' => 'employer',
                // 'idno'  => 'EMP-' . Str::random(6), // <-- Ensure idno is populated if not auto-generated in model
            ]);

            // Create Employer profile
            Employer::create([
                'idno'                => $user->idno, // Make sure User model generates/has idno
                'email'               => $validatedData['email'],
                'company_name'        => $validatedData['company_name'],
                'type_of_business'    => $validatedData['type_of_business'] ?? null,
                'province'            => $validatedData['province'],
                'town'                => substr($validatedData['town'], 0, 20), // Truncated to fit varchar(20)
                'brgy'                => substr($validatedData['brgy'], 0, 20), // Truncated to fit varchar(20)
                'address_details'     => $validatedData['address_details'] ?? null,
                'representative_name' => $validatedData['representative_name'],
                'mobile'              => $validatedData['mobile'],
                'designation'         => $validatedData['designation'],
                'tin'                 => $validatedData['tin'] ?? null,
                'about'               => $validatedData['about'] ?? null,
            ]);

            // TODO: Send $plainPassword to $user->email via Mail/Notification
        });

        return redirect()
            ->back()
            ->with('success', 'Employer details stored successfully!');
    }
    public function listJobs()
    {
        // Fetch all job postings sorted by newest
        $jobs = JobPosting::orderBy('created_at', 'desc')->get();

        // Get total count for the upper badge indicator
        $totalJobs = $jobs->count();

        return view('adtv.loj', compact('jobs', 'totalJobs'));
    }

    public function jobDetails($job_id)
    {
        $jobApp = JobApplication::where('job_id', $job_id)->count();
        $job = JobPosting::where('job_id', $job_id)->firstOrFail();

        return view('adtv.lojd', compact('job', 'jobApp'));
    }
    public function jobApplicants($job_id)
    {
        $job = JobPosting::where('job_id', $job_id)->firstOrFail();
        $applicants = $job->applicants;
        return view('adtv.loa', compact('job', 'applicants'));
    }

    public function applProfile($idno, $job_id)
    {
        // Queries the exact application instance using both identifier strings
        $application = JobApplication::with(['user.details'])
            ->where('user_id', $idno)
            ->where('job_id', $job_id)
            ->firstOrFail();

        $user = $application->user;
        $userDetails = $user->details;
        $educationalDetails = EducationalDetail::where('idno', $idno)->get();

        return view('adtv.appl', compact('application', 'user', 'userDetails', 'educationalDetails'));
    }
}
