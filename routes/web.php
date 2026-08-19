<?php

use App\Http\Controllers\AdminAccountController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\EducationalController;
use App\Http\Controllers\EmployerController;
use App\Http\Controllers\EmploymentStatusController;
use App\Http\Controllers\ExpertiseController;
use App\Http\Controllers\FourpsController;
use App\Http\Controllers\JobApplicationController;
use App\Http\Controllers\JobHistoryController;
use App\Http\Controllers\JobPostingController;
use App\Http\Controllers\JobRecommendationController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\OfwController;
use App\Http\Controllers\ProfessionalController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublicJobController;
use App\Http\Controllers\UserAboutController;
use App\Http\Controllers\UserAddressController;
use App\Http\Controllers\UserCivilController;
use App\Http\Controllers\UserDetailsController;
use App\Http\Controllers\UserGenderController;
use App\Http\Controllers\UserSexController;
use App\Http\Controllers\WorkDetailsController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public & Guest Routes
|--------------------------------------------------------------------------
*/

// Landing Page
// Route::redirect('/', '/public');
// Route::get('/', function () {
//     return view('welcome');
// });
Route::view('/', 'welcome');

// Public job views for guest users
Route::get('/public', [PublicJobController::class, 'index'])->name('public.jobs');
Route::get('/publicShow/{id}', [PublicJobController::class, 'show'])->name('public.show');
Route::get('/jobs/{id}', [PublicJobController::class, 'show'])->name('jobs.show');

/*
|--------------------------------------------------------------------------
| Onboarding & Welcome Views (Verified Users)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('welcome1', fn () => view('welcome1'))->name('welcome1');
    Route::get('welcome2', fn () => view('welcome2'))->name('welcome2');
    Route::get('welcome3', fn () => view('welcome3'))->name('welcome3');
    Route::get('/dashboard', fn () => view('dashboard'))->name('dashboard');
});

/*
|--------------------------------------------------------------------------
| Authenticated User Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {


    /* --- User Demographics & Basic Details --- */
    Route::get('/app', [UserDetailsController::class, 'index'])->name('details.index');
    Route::post('/app', [UserDetailsController::class, 'store'])->name('details.store');
    Route::put('/app/{idno}/details', [UserDetailsController::class, 'update'])->name('app.update');

    Route::get('/app/address', [UserAddressController::class, 'index'])->name('address.index');

    Route::get('/app/sex', [UserSexController::class, 'index'])->name('sex.index');
    Route::put('/app/{idno}/sex', [UserDetailsController::class, 'updatesex'])->name('sex.update');

    Route::get('/app/gender', [UserGenderController::class, 'index'])->name('gender.index');
    Route::put('/app/{idno}/gender', [UserDetailsController::class, 'updateGender'])->name('gender.update');

    Route::get('/app/civil', [UserCivilController::class, 'index'])->name('civil.index');
    Route::put('/app/{idno}/civil', [UserDetailsController::class, 'updateCivil'])->name('civil.update');

    Route::get('/app/about', [UserAboutController::class, 'index'])->name('about.index');
    Route::put('/app/{idno}/about', [UserDetailsController::class, 'updateAbout'])->name('about.update');

    Route::get('/app/profile', [UserDetailsController::class, 'profile'])->name('profile');
    Route::post('/app/profile/{idno}', [UserDetailsController::class, 'course_skills'])->name('course_skills');

    /* --- Education & Work Background --- */
    Route::get('/education', [EducationalController::class, 'index'])->name('background.index');
    Route::put('/education/{idno}', [EducationalController::class, 'updateCourse'])->name('background.update');

    Route::get('/expertise', [ExpertiseController::class, 'processMatch'])->name('expertise.process');
    Route::get('/expertise/{id}', [ExpertiseController::class, 'show'])->name('expertise.show');
    Route::put('/expertise/{idno}/skills', [WorkDetailsController::class, 'store'])->name('skills.store');

    Route::get('/professional', [ProfessionalController::class, 'index'])->name('professional.index');
    Route::put('/professional/{idno}', [WorkDetailsController::class, 'update'])->name('exp.store');

    Route::get('/job', [EmploymentStatusController::class, 'index'])->name('status.index');
    Route::put('/job/{idno}', [WorkDetailsController::class, 'employ_status'])->name('job.employment');

    Route::get('/employment', [JobHistoryController::class, 'index'])->name('employment.index');
    Route::put('/employment/{idno}', [WorkDetailsController::class, 'unemployment'])->name('unemployment');

    Route::get('/employment/ofw', [OfwController::class, 'index'])->name('ofw.index');
    Route::put('/employment/{idno}/ofw', [WorkDetailsController::class, 'ofw_update'])->name('ofw_update');

    Route::get('/employment/fourps', [FourpsController::class, 'index'])->name('fourps.index');
    Route::put('/employment/{idno}/fourps', [WorkDetailsController::class, 'fourps'])->name('fourps');

    Route::get('/job/prefocc', [WorkDetailsController::class, 'indexPrefocc'])->name('prefocc.index');
    Route::put('/job/{idno}/prefocc', [WorkDetailsController::class, 'prefocc'])->name('prefocc');

    Route::get('/job/distance', [WorkDetailsController::class, 'distance'])->name('distance.index');
    Route::put('/job/{idno}/distance', [WorkDetailsController::class, 'work_location'])->name('work_location');

    /* --- Job Recommendations & Applications --- */
    Route::get('/rec', [JobRecommendationController::class, 'index'])->name('recommended');
    Route::get('/recd/{job_id}', [JobRecommendationController::class, 'details'])->name('job_details');
    Route::post('/recd/{job_id}/save', [JobRecommendationController::class, 'toggleSave'])->name('jobs_save');
    Route::delete('/recd/{job_id}/cancel-application', [JobRecommendationController::class, 'cancel'])->name('jobs_cancel');
    Route::post('/recp/{job_id}', [JobRecommendationController::class, 'profile_review'])->name('profile_review');
    Route::post('/recp/{job_id}/apply', [JobRecommendationController::class, 'apply'])->name('jobs_apply');

    /* --- Employer Portal --- */
    Route::get('/par', [JobPostingController::class, 'index'])->name('par.index');
    Route::get('/par/emp', [JobPostingController::class, 'emp_comp'])->name('emp.comp');
    Route::put('/par/emp/{idno}', [JobPostingController::class, 'update_emp_comp'])->name('update_emp_comp');
    Route::get('/par/post', [JobPostingController::class, 'emp_post'])->name('emp.post');
    Route::post('/par/post/{idno}', [JobPostingController::class, 'job_post'])->name('job_post');
    Route::get('/par/postc/{job_id}', [JobPostingController::class, 'emp_postc'])->name('emp_postc');
    Route::put('/par/postc/{job_id}', [JobPostingController::class, 'job_postc'])->name('job_postc');
    Route::get('/par/lj', [JobPostingController::class, 'list_jobPosted'])->name('list_jobPosted');
    Route::get('/par/jd/{job_id}', [JobPostingController::class, 'parJobDetails'])->name('parJobDetails');
    Route::get('/par/la/{job_id}', [JobPostingController::class, 'parListApp'])->name('parListApp');
    Route::get('/par/app/{idno}/{job_id}', [JobPostingController::class, 'parAppProfile'])->name('parAppProfile');
    Route::post('/par/{job_id}/interview/{idno}', [JobPostingController::class, 'addToInterviewList'])->name('addToInterviewList');
    Route::delete('/par/{job_id}/interview/{idno}/remove', [JobPostingController::class, 'removeFromInterviewList'])->name('jobs.removeInterviewe');
    Route::patch('/par/{job_id}/interview/{idno}/hire', [JobPostingController::class, 'hireApplicant'])->name('jobs.hireApplicant');

    // Dynamic Helper APIs
    Route::get('/get-skills/{expertiseId}', [JobPostingController::class, 'getSkillsByExpertise']);
    Route::get('/get-courses/{expertiseId}', [EmployerController::class, 'getCourses']);
    /* --- Public APIs / Location Helpers --- */
    Route::get('/api/provinces', [LocationController::class, 'getProvinces']);
    Route::get('/api/towns', [LocationController::class, 'getTowns']);
    Route::get('/api/barangays', [LocationController::class, 'getBarangays']);

    /* --- Admin & PESO Management --- */
    Route::get('/adtv', [AdminAccountController::class, 'index'])->name('adtv.index');
    Route::get('/adtv/lu', [AdminAccountController::class, 'adtv_listUsers'])->name('adtv_listUsers');
    Route::get('/adtv/nu', [AdminAccountController::class, 'adtv_addUser'])->name('adtv_addUser');
    Route::post('/adtv/nu', [AdminAccountController::class, 'adtv_storeUser'])->name('adtv_storeUser');
    Route::get('/admin/users', [AdminAccountController::class, 'adtv_listUsers'])->name('adtv_listUsers');
    Route::get('/admin/admins', [AdminAccountController::class, 'adtv_listAdmins'])->name('adtv_listAdmins');
    Route::get('/adtv/emp', [AdminAccountController::class, 'adtv_createEmployer'])->name('adtv_createEmployer');
    Route::post('/adtv/emp', [AdminAccountController::class, 'adtv_storeEmployer'])->name('adtv_storeEmployer');
    Route::get('/adtv/loj', [AdminAccountController::class, 'listJobs'])->name('listJobs');
    Route::get('/adtv/loj/{job_id}', [AdminAccountController::class, 'jobDetails'])->name('jobDetails');
    Route::get('/adtv/loa/{job_id}', [AdminAccountController::class, 'jobApplicants'])->name('jobApplicants');
    Route::get('/adtv/appl/{idno}/{job_id}', [AdminAccountController::class, 'applProfile'])->name('applProfile');


    /* --- Admin Reports & LMI Analysis --- */
    Route::get('/adtv/rp', [AdminDashboardController::class, 'index'])->name('adtvDashboard');
    Route::get('/adtv/rp2', [AdminDashboardController::class, 'rp2'])->name('rp2');
    Route::get('/adtv/rp3', [AdminDashboardController::class, 'rp3'])->name('rp3');
    Route::get('/adtv/rp4', [AdminDashboardController::class, 'rp4'])->name('rp4');
    Route::get('/adtv/rp5', [AdminDashboardController::class, 'barangayReport'])->middleware(['auth'])->name('rp5');
    Route::get('/adtv/rp6', [AdminDashboardController::class, 'kpiReport'])->name('rp6');

    // Migration / Mobility Analysis (Cross-Barangay Matching)
    Route::get('/adtv/rp7', [AdminDashboardController::class, 'mobility'])->name('rp7');
    // Demographic & Educational Breakdown
    Route::get('/adtv/rp8', [AdminDashboardController::class, 'demographics'])->name('rp8');
    // Job Sector & Skill Demand by Barangay
    Route::get('/adtv/rp9', [AdminDashboardController::class, 'skillDemand'])->name('rp9');
    // Interactive Features & Visualizations
    Route::get('/adtv/rp10', [AdminDashboardController::class, 'analytics'])->name('rp10');

    /* --- Profile Management --- */
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
