<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AcademicYearController;
use App\Http\Controllers\ClassController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\ClassSubjectController;
use App\Http\Controllers\CurriculumController;
use App\Http\Controllers\TimetableController;
use App\Http\Controllers\StudentAttendanceController;
use App\Http\Controllers\TeacherAttendanceController;
use App\Http\Controllers\ExaminationController;
use App\Http\Controllers\FeeController;
use App\Http\Controllers\FeeDashboardController;
use App\Http\Controllers\FeeTypeController;
use App\Http\Controllers\FeeStructureController;
use App\Http\Controllers\StudentFeeController;
use App\Http\Controllers\FeeReminderController;
use App\Http\Controllers\BulkFeeController;
use App\Http\Controllers\PaymentController;

// Public routes
Route::get('/', function () {
    return redirect()->route('login');
});

// Authentication routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Password reset
Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('password.request');
Route::post('/forgot-password', [AuthController::class, 'processForgotPassword'])->name('password.email');
Route::get('/reset-password/{token}', [AuthController::class, 'showResetPassword'])->name('password.reset');
Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');

// Protected routes for all authenticated users
Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [AuthController::class, 'profile'])->name('profile');
    Route::get('/profile/edit', [AuthController::class, 'editProfile'])->name('profile.edit');
    Route::post('/profile/update', [AuthController::class, 'updateProfile'])->name('profile.update');
    Route::get('/change-password', [AuthController::class, 'changePassword'])->name('password.change');
    Route::post('/update-password', [AuthController::class, 'updatePassword'])->name('password.change.update');
});

// Admin routes
Route::prefix('admin')->middleware(['auth'])->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/users', [App\Http\Controllers\AdminController::class, 'users'])->name('admin.users');
    Route::get('/users/create', [App\Http\Controllers\AdminController::class, 'createUser'])->name('admin.users.create');
    Route::post('/users', [App\Http\Controllers\AdminController::class, 'storeUser'])->name('admin.users.store');
    Route::get('/users/{id}/edit', [App\Http\Controllers\AdminController::class, 'editUser'])->name('admin.users.edit');
    Route::post('/users/{id}', [App\Http\Controllers\AdminController::class, 'updateUser'])->name('admin.users.update');
    Route::delete('/users/{id}', [App\Http\Controllers\AdminController::class, 'deleteUser'])->name('admin.users.delete');
});

// Teacher routes
Route::prefix('teacher')->middleware(['auth', 'role:teacher'])->group(function () {
    Route::get('/dashboard', [TeacherController::class, 'dashboard'])->name('teacher.dashboard');
    // More teacher routes will be added later
});

// Student routes
Route::prefix('student')->middleware(['auth', 'role:student'])->group(function () {
    Route::get('/dashboard', [StudentController::class, 'dashboard'])->name('student.dashboard');
    // More student routes will be added later
});
// Profile management routes
Route::prefix('profile')->middleware(['auth'])->group(function () {
    Route::get('/', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/update', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/change-password', [ProfileController::class, 'showChangePasswordForm'])->name('profile.password');
    Route::put('/update-password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');
});
// Academic Year Management
Route::prefix('admin')->middleware(['auth'])->group(function () {
    Route::get('/academic-years', [AcademicYearController::class, 'index'])->name('academic-years.index');
    Route::get('/academic-years/create', [AcademicYearController::class, 'create'])->name('academic-years.create');
    Route::post('/academic-years', [AcademicYearController::class, 'store'])->name('academic-years.store');
    Route::get('/academic-years/{id}/edit', [AcademicYearController::class, 'edit'])->name('academic-years.edit');
    Route::put('/academic-years/{id}', [AcademicYearController::class, 'update'])->name('academic-years.update');
    Route::delete('/academic-years/{id}', [AcademicYearController::class, 'destroy'])->name('academic-years.destroy');
});
// Class Management Routes
Route::prefix('admin')->middleware(['auth'])->group(function () {
    Route::get('/classes', [ClassController::class, 'index'])->name('classes.index');
    Route::get('/classes/create', [ClassController::class, 'create'])->name('classes.create');
    Route::post('/classes', [ClassController::class, 'store'])->name('classes.store');
    Route::get('/classes/{id}/edit', [ClassController::class, 'edit'])->name('classes.edit');
    Route::put('/classes/{id}', [ClassController::class, 'update'])->name('classes.update');
    Route::delete('/classes/{id}', [ClassController::class, 'destroy'])->name('classes.destroy');
    Route::get('/classes/{id}/students', [ClassController::class, 'students'])->name('classes.students');
});
// Subject Management Routes
Route::prefix('admin')->middleware(['auth'])->group(function () {
    Route::get('/subjects', [SubjectController::class, 'index'])->name('subjects.index');
    Route::get('/subjects/create', [SubjectController::class, 'create'])->name('subjects.create');
    Route::post('/subjects', [SubjectController::class, 'store'])->name('subjects.store');
    Route::get('/subjects/{id}/edit', [SubjectController::class, 'edit'])->name('subjects.edit');
    Route::put('/subjects/{id}', [SubjectController::class, 'update'])->name('subjects.update');
    Route::delete('/subjects/{id}', [SubjectController::class, 'destroy'])->name('subjects.destroy');
});
// Class Subject Assignment Routes
Route::prefix('admin')->middleware(['auth'])->group(function () {
    Route::get('/classes/{classId}/subjects', [ClassSubjectController::class, 'index'])->name('classes.subjects.index');
    Route::get('/classes/{classId}/subjects/create', [ClassSubjectController::class, 'create'])->name('classes.subjects.create');
    Route::post('/classes/{classId}/subjects', [ClassSubjectController::class, 'store'])->name('classes.subjects.store');
    Route::get('/classes/{classId}/subjects/{id}/edit', [ClassSubjectController::class, 'edit'])->name('classes.subjects.edit');
    Route::put('/classes/{classId}/subjects/{id}', [ClassSubjectController::class, 'update'])->name('classes.subjects.update');
    Route::delete('/classes/{classId}/subjects/{id}', [ClassSubjectController::class, 'destroy'])->name('classes.subjects.destroy');
});
// Curriculum Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/curriculum', [CurriculumController::class, 'index'])->name('curriculum.index');
    Route::get('/curriculum/create', [CurriculumController::class, 'create'])->name('curriculum.create');
    Route::post('/curriculum', [CurriculumController::class, 'store'])->name('curriculum.store');
    Route::get('/curriculum/{id}', [CurriculumController::class, 'show'])->name('curriculum.show');
    Route::get('/curriculum/{id}/edit', [CurriculumController::class, 'edit'])->name('curriculum.edit');
    Route::put('/curriculum/{id}', [CurriculumController::class, 'update'])->name('curriculum.update');
    Route::delete('/curriculum/{id}', [CurriculumController::class, 'destroy'])->name('curriculum.destroy');
});

// Timetable Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/timetable', [TimetableController::class, 'index'])->name('timetable.index');
    Route::get('/timetable/class/{classId}/create', [TimetableController::class, 'create'])->name('timetable.create');
    Route::post('/timetable/class/{classId}', [TimetableController::class, 'store'])->name('timetable.store');
    Route::get('/timetable/class/{classId}/edit/{id}', [TimetableController::class, 'edit'])->name('timetable.edit');
    Route::put('/timetable/class/{classId}/{id}', [TimetableController::class, 'update'])->name('timetable.update');
    Route::delete('/timetable/class/{classId}/{id}', [TimetableController::class, 'destroy'])->name('timetable.destroy');
});
// Student Attendance Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/attendance/student', [StudentAttendanceController::class, 'index'])->name('student.attendance.index');
    Route::post('/attendance/student', [StudentAttendanceController::class, 'store'])->name('student.attendance.store');
    Route::get('/attendance/student/report', [StudentAttendanceController::class, 'report'])->name('student.attendance.report');
    Route::get('/my-attendance', [StudentAttendanceController::class, 'studentReport'])->name('student.attendance.my-report');
});

// Teacher Attendance Routes
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/attendance/teacher', [TeacherAttendanceController::class, 'index'])->name('teacher.attendance.index');
    Route::post('/attendance/teacher', [TeacherAttendanceController::class, 'store'])->name('teacher.attendance.store');
    Route::get('/attendance/teacher/report', [TeacherAttendanceController::class, 'report'])->name('teacher.attendance.report');
});

// Examination Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/examinations', [ExaminationController::class, 'index'])->name('examinations.index');
    Route::get('/examinations/create', [ExaminationController::class, 'create'])->name('examinations.create');
    Route::post('/examinations', [ExaminationController::class, 'store'])->name('examinations.store');
    Route::get('/examinations/{id}', [ExaminationController::class, 'show'])->name('examinations.show');
    Route::get('/examinations/{id}/edit', [ExaminationController::class, 'edit'])->name('examinations.edit');
    Route::put('/examinations/{id}', [ExaminationController::class, 'update'])->name('examinations.update');
    Route::delete('/examinations/{id}', [ExaminationController::class, 'destroy'])->name('examinations.destroy');
    
    // Exam Schedule Routes
    Route::get('/examinations/{id}/schedules', [ExaminationController::class, 'schedules'])->name('examinations.schedules');
    Route::post('/examinations/{id}/schedules', [ExaminationController::class, 'storeSchedule'])->name('examinations.schedules.store');
    Route::get('/examinations/{id}/schedules/{scheduleId}/edit', [ExaminationController::class, 'editSchedule'])->name('examinations.schedules.edit');
    Route::put('/examinations/{id}/schedules/{scheduleId}', [ExaminationController::class, 'updateSchedule'])->name('examinations.schedules.update');
    Route::delete('/examinations/{id}/schedules/{scheduleId}', [ExaminationController::class, 'destroySchedule'])->name('examinations.schedules.destroy');
    
    // Exam Results Routes
    Route::get('/examinations/{id}/results', [ExaminationController::class, 'results'])->name('examinations.results');
    Route::get('/examinations/{id}/results/class/{classId}', [ExaminationController::class, 'classResults'])->name('examinations.class_results');
    Route::get('/examinations/{id}/results/class/{classId}/subject/{subjectId}', [ExaminationController::class, 'enterResults'])->name('examinations.enter_results');
    Route::post('/examinations/{id}/results/class/{classId}/subject/{subjectId}', [ExaminationController::class, 'storeResults'])->name('examinations.results.store');
    Route::get('/my-results', [ExaminationController::class, 'studentResults'])->name('examinations.student_results');
});
Route::middleware(['auth', 'role:admin'])->prefix('fees')->name('fees.')->group(function () {
    // Fee Dashboard
    Route::get('/dashboard', [FeeDashboardController::class, 'index'])->name('dashboard');
    
    // Fee Types
    Route::get('/types', [FeeTypeController::class, 'index'])->name('types.index');
    Route::get('/types/create', [FeeTypeController::class, 'create'])->name('types.create');
    Route::post('/types', [FeeTypeController::class, 'store'])->name('types.store');
    Route::get('/types/{id}/edit', [FeeTypeController::class, 'edit'])->name('types.edit');
    Route::put('/types/{id}', [FeeTypeController::class, 'update'])->name('types.update');
    Route::delete('/types/{id}', [FeeTypeController::class, 'destroy'])->name('types.destroy');
    
    // Fee Structures
    Route::get('/structures', [FeeStructureController::class, 'index'])->name('structures.index');
    Route::get('/structures/create', [FeeStructureController::class, 'create'])->name('structures.create');
    Route::post('/structures', [FeeStructureController::class, 'store'])->name('structures.store');
    Route::get('/structures/{id}/edit', [FeeStructureController::class, 'edit'])->name('structures.edit');
    Route::put('/structures/{id}', [FeeStructureController::class, 'update'])->name('structures.update');
    Route::delete('/structures/{id}', [FeeStructureController::class, 'destroy'])->name('structures.destroy');
    Route::get('/structures/get-classes/{academicYearId}', [FeeStructureController::class, 'getClasses'])->name('structures.get-classes');
    Route::get('/structures/{id}/apply-to-students', [FeeStructureController::class, 'applyToStudents'])->name('structures.apply-to-students');
    Route::post('/structures/{id}/process-apply', [FeeStructureController::class, 'processApplyToStudents'])->name('structures.process-apply');
    
    // Student Fees
    Route::get('/students', [StudentFeeController::class, 'index'])->name('students.index');
    Route::get('/students/{id}', [StudentFeeController::class, 'show'])->name('students.show');
    Route::get('/students/{id}/edit', [StudentFeeController::class, 'edit'])->name('students.edit');
    Route::put('/students/{id}', [StudentFeeController::class, 'update'])->name('students.update');
    Route::delete('/students/{id}', [StudentFeeController::class, 'destroy'])->name('students.destroy');
    Route::get('/students/{id}/record-payment', [StudentFeeController::class, 'recordPayment'])->name('students.record-payment');
    Route::post('/students/{id}/store-payment', [StudentFeeController::class, 'storePayment'])->name('students.store-payment');
});

// Payment routes
Route::middleware(['auth', 'role:admin'])->prefix('payments')->name('payments.')->group(function () {
    Route::get('/', [PaymentController::class, 'index'])->name('index');
    Route::get('/{id}', [PaymentController::class, 'show'])->name('show');
    Route::get('/{id}/receipt', [PaymentController::class, 'receipt'])->name('receipt');
    Route::get('/{id}/download-receipt', [PaymentController::class, 'downloadReceipt'])->name('download-receipt');
    Route::get('/{id}/delete', [PaymentController::class, 'delete'])->name('delete');
    Route::delete('/{id}', [PaymentController::class, 'destroy'])->name('destroy');
});

// Keep these admin-prefixed routes for the remaining menu items
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    // Payment reports
    Route::get('/payments/reports', [PaymentController::class, 'reports'])->name('payments.reports');
    Route::get('/payments/reports/export', [PaymentController::class, 'exportReport'])->name('payments.export-report');
    Route::get('/payments/reports/print', [PaymentController::class, 'printReport'])->name('payments.print-report');
    Route::get('/payments/reports/pdf', [PaymentController::class, 'pdfReport'])->name('payments.pdf-report');
    
    // Fee reminders
    Route::prefix('fees/reminders')->name('fees.reminders.')->group(function () {
        Route::get('/', [FeeReminderController::class, 'index'])->name('index');
        Route::post('/send', [FeeReminderController::class, 'send'])->name('send');
        Route::get('/log', [FeeReminderController::class, 'log'])->name('log');
        Route::get('/student/{id}', [FeeReminderController::class, 'sendIndividual'])->name('student');
        Route::post('/student/{id}', [FeeReminderController::class, 'processIndividual'])->name('student.process');
    });
    
    // Bulk Operations
    Route::prefix('fees/bulk')->name('fees.bulk.')->group(function () {
        Route::get('/', [BulkFeeController::class, 'index'])->name('index');
        Route::post('/', [BulkFeeController::class, 'process'])->name('process');
        Route::get('/import', [BulkFeeController::class, 'importForm'])->name('import-form');
        Route::post('/import', [BulkFeeController::class, 'importProcess'])->name('import-process');
    });
});

// Admin Payment Routes
Route::middleware(['auth', 'role:admin'])->prefix('admin/payments')->name('admin.payments.')->group(function () {
    Route::get('/', 'PaymentController@index')->name('index');
    Route::get('/{id}', 'PaymentController@show')->name('show');
    Route::get('/{id}/receipt', 'PaymentController@receipt')->name('receipt');
    Route::get('/{id}/download-receipt', 'PaymentController@downloadReceipt')->name('download-receipt');
    Route::get('/{id}/delete', 'PaymentController@delete')->name('delete');
    Route::delete('/{id}', 'PaymentController@destroy')->name('destroy');
    Route::get('/reports', 'PaymentController@reports')->name('reports');
    Route::get('/reports/export', 'PaymentController@exportReport')->name('export-report');
    Route::get('/reports/print', 'PaymentController@printReport')->name('print-report');
    Route::get('/reports/pdf', 'PaymentController@pdfReport')->name('pdf-report');
});

// Student Fee Routes
Route::middleware(['auth', 'role:student'])->prefix('student')->name('student.')->group(function () {
    Route::get('/fees', 'StudentFeeController@studentFees')->name('fees.index');
    Route::get('/payments/history', 'PaymentController@studentPayments')->name('payments.history');
});

// Student-specific fee management routes for admin
Route::middleware(['auth', 'role:admin'])->prefix('admin/students')->name('admin.students.')->group(function () {
    Route::get('/{id}/fees', 'StudentController@fees')->name('fees');
    Route::get('/{id}/add-fee', 'StudentController@addFee')->name('add-fee');
    Route::post('/{id}/store-fee', 'StudentController@storeFee')->name('store-fee');
    Route::get('/{id}/fee-statement', 'StudentController@feeStatement')->name('fee-statement');
    Route::get('/{id}/payment-history', 'StudentController@paymentHistory')->name('payment-history');
});