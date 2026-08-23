<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UserDetails;
use App\Models\WorkDetails;
use App\Models\Educational;
use App\Models\Expertise;
use App\Models\Course;
use App\Models\EducationalDetail;
use App\Http\Requests\StoreUserDetailsRequest;
use App\Http\Requests\UpdateUserDetailsRequest;
use App\Http\Requests\UpdateUserContactDetailsRequest;
use App\Http\Requests\UpdateUserSexRequest;
use App\Http\Requests\UpdateUserGenderRequest;
use App\Http\Requests\UpdateUserAboutRequest;
use App\Http\Requests\UpdateUserCivilRequest;

class UserDetailsController extends Controller
{
    public function contact()
    {
        // return view('user.sex');
        $user = User::where('idno', auth()->user()->idno)->first();
        return view('app.contact', compact('user'));
    }
    public function updateContact(UpdateUserContactDetailsRequest $request, $idno)
    {
        $validatedData = $request->validated();
        $userContact = UserDetails::where('idno', $idno)->firstOrFail();
        $userContact->update($validatedData);
        return redirect()->route('civil.index')->with('success', 'User contact information saved successfully.');

    }
    public function profile()
    {
        $user = UserDetails::where('idno', auth()->user()->idno)->first();
        $userB = DB::table('user_details')
        ->leftJoin('barangays', 'user_details.brgy', '=', 'barangays.id') // adjust primary key column if needed
        ->leftJoin('towns', 'user_details.town', '=', 'towns.id')         // adjust primary key column if needed
        ->where('user_details.idno', auth()->user()->idno)
        ->select(
            'user_details.*',
            'barangays.barangay as barangay_name',
            'towns.town as town_name'
        )
        ->first();


        $work = WorkDetails::where('idno', auth()->user()->idno)->first();
        $educationalDetails = EducationalDetail::where('idno', auth()->user()->idno)->get();
        $expertise = Expertise::all();
        return view('app.profile', compact('user', 'userB', 'work', 'educationalDetails', 'expertise'));
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('app.details');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserDetailsRequest $request)
    {
        $user = User::where('email', auth()->user()->email)->first();
        UserDetails::create(array_merge($request->validated(), [
            'idno' => $user->idno,
        ]));

        return redirect()->route('address.index')->with('success', 'User details saved successfully.');
    }
    public function contact_details(UpdateUserContactDetailsRequest $request, $idno)
    {
        $validatedData = $request->validated();

        $contactDetails = UserDetails::where('idno', $idno)->firstOrFail();

        $contactDetails->update($validatedData);
        
        $jobId = $request->input('job_id');

        // If job_id exists, redirect to profile_review with the parameter
        if ($jobId) {
            return redirect()->route('profile_review', ['job_id' => $jobId])
                            ->with('success', 'User details updated.');
        }

        return back()->with('success', 'User details updated.');
    }

    /**
     * Display the specified resource.
     */
    public function show(UserDetails $userDetails)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(UserDetails $userDetails)
    {
        //
    }
    /**
     * Update the specified resource in storage. Without coordinates fetching and barangay update
     */
    // public function update(UpdateUserDetailsRequest $request, $idno)
    // {
    //     // 1. Get the validated form data
    //     $validatedData = $request->validated();

    //     // 2. Find the user record or fail with a 404 if not found
    //     $userAddress = UserDetails::where('idno', $idno)->firstOrFail();

    //     // 3. Directly update user details with the form data
    //     $userAddress->update($validatedData);

    //     // 4. Redirect to the index page with a success message
    //     return redirect()->route('sex.index')->with('success', 'User address details saved.');
    // }

    /**
     * Update the specified resource in storage. Fetching coordinates from barangay table and updating user details with those coordinates
     */
    public function update(UpdateUserDetailsRequest $request, $idno)
    {
        // 1. Get the validated form data (e.g., province, town, brgy)
        $validatedData = $request->validated();

        // 2. Query the barangay table to fetch its coordinates
        // Assuming your input field name is 'brgy' and the table column is 'name'
        $barangayCoordinates = DB::table('barangays')
            ->where('id', $validatedData['brgy'])
            ->select('latitude', 'longitude')
            ->first();

        // 3. Inject coordinates into the data array if the barangay exists
        if ($barangayCoordinates) {
            $validatedData['latitude'] = $barangayCoordinates->latitude;
            $validatedData['longitude'] = $barangayCoordinates->longitude;
        } else {
            // Optional: Handle case where coordinates aren't found
            $validatedData['latitude'] = null;
            $validatedData['longitude'] = null;
        }

        // 4. Find the user record or fail with a 404
        $userAddress = UserDetails::where('idno', $idno)->firstOrFail();

        // 5. Update user details (now including the fetched coordinates)
        $userAddress->update($validatedData);

        // 6. Redirect to the index page with a success message
        return redirect()->route('contact.index')->with('success', 'User address and matching coordinates updated.');
    }


    /**
     * Update the specified resource in storage. With coordinates fetching and barangay update
     */
    // public function update(UpdateUserDetailsRequest $request, $idno)
    // {
    //     $validatedData = $request->validated();

    //     // 1. Retrieve the selected Barangay and its parent Town
    //     $barangay = DB::table('barangays')->where('id', $request->input('brgy'))->first();

    //     $latitude  = null;
    //     $longitude = null;

    //     if ($barangay) {
    //         $town = DB::table('towns')->where('id', $barangay->town_id)->first();

    //         // 2. Construct the precise address search string
    //         $searchAddress = "{$barangay->barangay}, {$town->town}, {$town->province}, Philippines";

    //         // 3. Query the Geocoding API
    //         try {
    //             $response = Http::withHeaders([
    //                 'User-Agent' => 'ARC_Application/1.0 (contact@yourdomain.com)'
    //             ])->timeout(7)->get('https://nominatim.openstreetmap.org/search', [
    //                 'q'      => $searchAddress,
    //                 'format' => 'json',
    //                 'limit'  => 1
    //             ]);

    //             if ($response->successful() && !empty($response->json())) {
    //                 $geoData   = $response->json()[0];
    //                 $latitude  = $geoData['lat'];
    //                 $longitude = $geoData['lon'];
    //             }
    //         } catch (\Exception $e) {
    //             report($e); // Logs the error silently if the connection drops
    //         }
    //     }

    //     // 4. Update user details with the form data and the precise coordinates (or null if not found)
    //     $userAddress = UserDetails::where('idno', $idno)->firstOrFail();
    //     $userAddress->update(array_merge($validatedData, [
    //         'latitude'  => $latitude,
    //         'longitude' => $longitude
    //     ]));

    //     return redirect()->route('sex.index')->with('success', 'User address details saved.');
    // }





    /**
     * Update the specified resource in storage. With coordinates fetching and barangay update, with coordinates in barangays table
     */
    // public function update(UpdateUserDetailsRequest $request, $idno)
    // {
    //     $validatedData = $request->validated();

    //     // 1. Retrieve the selected Barangay and its parent Town
    //     $barangay = DB::table('barangays')->where('id', $request->input('brgy'))->first();

    //     $latitude  = null;
    //     $longitude = null;

    //     if ($barangay) {
    //         $town = DB::table('towns')->where('id', $barangay->town_id)->first();

    //         // 2. Construct the precise address search string
    //         $searchAddress = "{$barangay->barangay}, {$town->town}, {$town->province}, Philippines";

    //         // 3. Query the Geocoding API
    //         try {
    //             $response = Http::withHeaders([
    //                 'User-Agent' => 'ARC_Application/1.0 (contact@yourdomain.com)'
    //             ])->timeout(7)->get('https://nominatim.openstreetmap.org/search', [
    //                 'q'      => $searchAddress,
    //                 'format' => 'json',
    //                 'limit'  => 1
    //             ]);

    //             if ($response->successful() && !empty($response->json())) {
    //                 $geoData   = $response->json()[0];
    //                 $latitude  = $geoData['lat'];
    //                 $longitude = $geoData['lon'];
    //             }
    //         } catch (\Exception $e) {
    //             report($e); // Logs the error silently if the connection drops
    //         }
    //     }

    //     // 4. Update both UserDetails and Barangays tables securely inside a transaction
    //     DB::transaction(function () use ($idno, $validatedData, $latitude, $longitude, $barangay) {
    //         // Update user details with the form data and the precise coordinates
    //         $userAddress = UserDetails::where('idno', $idno)->firstOrFail();
    //         $userAddress->update(array_merge($validatedData, [
    //             'latitude'  => $latitude,
    //             'longitude' => $longitude
    //         ]));

    //         // NEW: Update the coordinates in the barangays table if coordinates were found
    //         if ($barangay && $latitude && $longitude) {
    //             DB::table('barangays')
    //                 ->where('id', $barangay->id)
    //                 ->update([
    //                     'latitude'   => $latitude, // Ensure these column names match your DB schema
    //                     'longitude'  => $longitude,
    //                     'updated_at' => now()       // Good practice if you track timestamps manually here
    //                 ]);
    //         }
    //     });

    //     return redirect()->route('sex.index')->with('success', 'User address and barangay details saved.');
    // }
    public function updatesex(UpdateUserSexRequest $request, $idno)
    {
        // $validatedData = $request->validate([
        //     'sex' => 'required|string|max:1',
        // ]);

        $validatedData = $request->validated();
        $userAddress = UserDetails::where('idno', $idno)->firstOrFail();
        $userAddress->update($validatedData);
        return redirect()->route('civil.index')->with('success', 'User details saved successfully.');

    }
    public function updateGender(UpdateUserGenderRequest $request, $idno)
    {
        if ($request->filled('custom_gender')) {
            $request->merge(['gender' => $request->custom_gender]);
        }

        // $validatedData = $request->validate([
        //     'gender' => 'required|string|max:15',
        // ]);

        $validatedData = $request->validated();
        $userAddress = UserDetails::where('idno', $idno)->firstOrFail();
        $userAddress->update($validatedData);
        return redirect()->route('civil.index')->with('success', 'User details saved successfully.');

    }

    public function updateCivil(UpdateUserCivilRequest $request, $idno)
    {
        // $validatedData = $request->validate([
        //     'civil_status' => 'required|string|max:15',
        // ]);

        $validatedData = $request->validated();
        $userDetail = UserDetails::where('idno', $idno)->firstOrFail();
        $userDetail->update($validatedData);
        return redirect()->route('background.index')->with('success', 'User details saved successfully.');

    }
    public function updateAbout(UpdateUserAboutRequest $request, $idno)
    {
        $validatedData = $request->validated();
        $userAddress = UserDetails::where('idno', $idno)->firstOrFail();
        $userAddress->update($validatedData);
        
        return back()->with('success', 'User details updated.');
    }
    /**
     * Remove the specified resource from storage.
     */
    public function course_skills(Request $request)
    {
        // 1. Validate payload
        $validated = $request->validate([
            'job_category'    => 'required|integer',
            'course_id'       => 'required|array',
            'course_id.*'     => 'integer',
            'school'          => 'required|string|max:255',
            'year'            => 'required|digits:4',
            'skills_required' => 'nullable|array',
        ]);

        $idno = Auth::user()->idno ?? Auth::id();

        // Format skills array into a clean string (e.g., "Laravel, VueJS, MySQL")
        $skillsString = !empty($validated['skills_required'])
            ? implode(', ', $validated['skills_required'])
            : null;

        // 2. Fetch the selected courses from database
        $courses = Course::whereIn('id', $validated['course_id'])->get();

        // 3. Insert a NEW ROW for each course added
        foreach ($courses as $course) {
            EducationalDetail::create([
                'idno'           => $idno,
                'educ_level'     => strtolower($course->educ_level ?? 'Other'),
                'school'         => $validated['school'],
                'course_name'    => $course->display_name ?? $course->name,
                'year_graduated' => $validated['year'],
                'skills'         => $skillsString,
            ]);
        }

        return redirect()->back()->with('success', 'Educational record added successfully!');
    }
}
