<?php


use App\Http\Controllers\CourseController;
use App\Http\Controllers\EnrollmentController;
use App\Http\Controllers\ProgramController;
use App\Http\Controllers\Admin\ProgramAdminController;
use App\Http\Controllers\Admin\CourseAdminController;
use App\Http\Controllers\Admin\UnitAdminController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\AboutController;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');



require __DIR__.'/auth.php';

// Static pages
Route::get('/help', [App\Http\Controllers\StaticPageController::class, 'help'])->name('help');
Route::get('/contact', [App\Http\Controllers\StaticPageController::class, 'contact'])->name('contact');
Route::get('/faq', [App\Http\Controllers\StaticPageController::class, 'faq'])->name('faq');
Route::get('/privacy', [App\Http\Controllers\StaticPageController::class, 'privacy'])->name('privacy');
Route::get('/terms', [App\Http\Controllers\StaticPageController::class, 'terms'])->name('terms');
Route::get('/cookies', [App\Http\Controllers\StaticPageController::class, 'cookies'])->name('cookies');

// Public browsing
Route::get('/about', [AboutController::class, 'index'])->name('about.index');
Route::get('/programs', [ProgramController::class, 'index'])->name('programs.index');
Route::get('/programs/{program}', [ProgramController::class, 'show'])->name('programs.show');
Route::get('/courses', [CourseController::class, 'index'])->name('courses.index');
Route::get('/courses/{course}', [CourseController::class, 'show'])->name('courses.show');

// Enrollment actions (students only)
Route::middleware(['auth'])->group(function () {
    Route::post('/courses/{course}/enroll', [EnrollmentController::class, 'store'])->name('enrollments.store');
    Route::delete('/enrollments/{enrollment}', [EnrollmentController::class, 'destroy'])->name('enrollments.destroy');
    Route::post('/courses/{course}/favorite', [FavoriteController::class, 'store'])->name('favorites.store');
    Route::delete('/courses/{course}/favorite', [FavoriteController::class, 'destroy'])->name('favorites.destroy');
});

        // Admin Program CRUD
        Route::middleware(['auth'])->group(function () {
            Route::get('/admin/programs', [ProgramAdminController::class, 'index'])->name('admin.programs.index');
            Route::get('/admin/programs/create', [ProgramAdminController::class, 'create'])->name('admin.programs.create');
            Route::post('/admin/programs', [ProgramAdminController::class, 'store'])->name('admin.programs.store');
            Route::get('/admin/programs/{program}', [ProgramAdminController::class, 'show'])->name('admin.programs.show');
            Route::get('/admin/programs/{program}/edit', [ProgramAdminController::class, 'edit'])->name('admin.programs.edit');
            Route::put('/admin/programs/{program}', [ProgramAdminController::class, 'update'])->name('admin.programs.update');
            Route::delete('/admin/programs/{program}', [ProgramAdminController::class, 'destroy'])->name('admin.programs.destroy');
            Route::post('/admin/programs/{program}/delete-course', [ProgramAdminController::class, 'deleteCourse'])->name('admin.programs.delete-course');
            Route::post('/admin/programs/{program}/reassign-course', [ProgramAdminController::class, 'reassignCourse'])->name('admin.programs.reassign-course');
        });

        // Admin Course CRUD
        Route::middleware(['auth'])->group(function () {
            Route::get('/admin/courses', [CourseAdminController::class, 'index'])->name('admin.courses.index');
            Route::get('/admin/courses/create', [CourseAdminController::class, 'create'])->name('admin.courses.create');
            Route::post('/admin/courses', [CourseAdminController::class, 'store'])->name('admin.courses.store');
            Route::get('/admin/courses/{course}', [CourseAdminController::class, 'show'])->name('admin.courses.show');
            Route::get('/admin/courses/{course}/edit', [CourseAdminController::class, 'edit'])->name('admin.courses.edit');
            Route::put('/admin/courses/{course}', [CourseAdminController::class, 'update'])->name('admin.courses.update');
            Route::delete('/admin/courses/{course}', [CourseAdminController::class, 'destroy'])->name('admin.courses.destroy');
            Route::post('/admin/courses/{course}/assign-unit', [CourseAdminController::class, 'assignUnit'])->name('admin.courses.assign-unit');
            Route::post('/admin/courses/{course}/remove-unit', [CourseAdminController::class, 'removeUnit'])->name('admin.courses.remove-unit');
        });

// Admin Unit CRUD
Route::middleware(['auth'])->group(function () {
    Route::get('/admin/units', [UnitAdminController::class, 'index'])->name('admin.units.index');
    Route::get('/admin/units/create', [UnitAdminController::class, 'create'])->name('admin.units.create');
    Route::post('/admin/units', [UnitAdminController::class, 'store'])->name('admin.units.store');
    Route::get('/admin/units/{unit}/edit', [UnitAdminController::class, 'edit'])->name('admin.units.edit');
    Route::put('/admin/units/{unit}', [UnitAdminController::class, 'update'])->name('admin.units.update');
    Route::delete('/admin/units/{unit}', [UnitAdminController::class, 'destroy'])->name('admin.units.destroy');
    Route::post('/admin/units/assign-tutor', [UnitAdminController::class, 'assignTutor'])->name('admin.units.assign-tutor');
    Route::post('/admin/units/remove-tutor', [UnitAdminController::class, 'removeTutor'])->name('admin.units.remove-tutor');
});

// Admin About Management
Route::middleware(['auth'])->group(function () {
    Route::get('/admin/about', [\App\Http\Controllers\Admin\AboutController::class, 'index'])->name('admin.about.index');
    Route::post('/admin/about/edit-content', [\App\Http\Controllers\Admin\AboutController::class, 'editContent'])->name('admin.about.edit-content');
});

// Reporting Routes - Admin Only - Demonstrates Service Layer and Strategy Pattern
Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::post('/reports/generate', [ReportController::class, 'generate'])->name('reports.generate');
    Route::post('/reports/export', [ReportController::class, 'export'])->name('reports.export');
    Route::get('/reports/types', [ReportController::class, 'getReportTypes'])->name('reports.types');
    Route::post('/reports/preview', [ReportController::class, 'preview'])->name('reports.preview');
    Route::get('/reports/test', [ReportController::class, 'test'])->name('reports.test');
});

// API Routes for dropdown data - Admin Only
Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/api/programs', function () {
        $programs = \App\Models\Program::select('id', 'title')->get();
        return response()->json($programs);
    });
    
    Route::get('/api/courses', function () {
        $courses = \App\Models\Course::select('id', 'title', 'code')->get();
        return response()->json($courses);
    });
    
    Route::get('/api/courses/by-program/{programId}', function ($programId) {
        $courses = \App\Models\Course::where('program_id', $programId)
            ->select('id', 'title', 'code')
            ->get();
        return response()->json($courses);
    });
    
    Route::get('/api/students', function () {
        $students = \App\Models\Student::with('user')
            ->select('id', 'user_id', 'first_name', 'last_name')
            ->get()
            ->map(function ($student) {
                return [
                    'id' => $student->id,
                    'name' => $student->first_name . ' ' . $student->last_name,
                    'email' => $student->user->email
                ];
            });
        return response()->json($students);
    });
    
    Route::get('/api/test-report', function () {
        try {
            $service = new \App\Services\ReportService();
            $result = $service->generateStudentReport([], 'json');
            return response()->json([
                'success' => true,
                'count' => count($result),
                'sample' => array_slice($result, 0, 2)
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    });
});
