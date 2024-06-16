<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AdministrativeController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\DisciplineController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\CartController;
use App\Models\Discipline;
use App\Models\Teacher;
use App\Models\Student;
use App\Models\Course;
use App\Models\User;
use Illuminate\Support\Facades\Route;

/* ----- PUBLIC ROUTES ----- */

Route::view('/', 'home')->name('home');

Route::get('courses/showcase', [CourseController::class, 'showCase'])
    ->name('courses.showcase')
    ->can('viewShowCase', Course::class);

Route::get('courses/{course}/curriculum', [CourseController::class, 'showCurriculum'])
    ->name('courses.curriculum')
    ->can('viewCurriculum', Course::class);


/* ----- Non-Verified users ----- */
Route::middleware('auth')->group(function () {
    Route::get('/password', [ProfileController::class, 'editPassword'])->name('profile.edit.password');
});

/* ----- Verified users ----- */
Route::middleware('auth', 'verified')->group(function () {


// CHECK THIS -------- -------- -------- --------
    /* ----- Non-Verified users ----- */
    // Route::middleware('auth')->group(function () {
    //     Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    //     Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    //     Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    // });
// CHECK THIS -------- -------- -------- --------


    Route::view('/dashboard', 'dashboard')->name('dashboard');

    Route::delete('courses/{course}/image', [CourseController::class, 'destroyImage'])
        ->name('courses.image.destroy')
        ->can('update', Course::class);

    //Course resource routes are protected by CoursePolicy on the controller
    // The route 'show' is public (for anonymous user)
    Route::resource('courses', CourseController::class)->except(['show']);

    //Department resource routes are protected by DepartmentPolicy on the controller
    Route::resource('departments', DepartmentController::class);

    Route::get('disciplines/my', [DisciplineController::class, 'myDisciplines'])
        ->name('disciplines.my')
        ->can('viewMy', Discipline::class);

    //Discipline resource routes are protected by DisciplinePolicy on the controller
    //Disciplines index and show are public
    Route::resource('disciplines', DisciplineController::class)->except(['index', 'show']);

    Route::get('teachers/my', [TeacherController::class, 'myTeachers'])
        ->name('teachers.my')
        ->can('viewMy', Teacher::class);

    Route::delete('teachers/{teacher}/photo', [TeacherController::class, 'destroyPhoto'])
        ->name('teachers.photo.destroy')
        ->can('update', 'teacher');

    //Teacher resource routes are protected by TeacherPolicy on the controller
    Route::resource('teachers', TeacherController::class);

    Route::get('students/my', [StudentController::class, 'myStudents'])
        ->name('students.my')
        ->can('viewMy', Student::class);
    Route::delete('students/{student}/photo', [StudentController::class, 'destroyPhoto'])
        ->name('students.photo.destroy')
        ->can('update', 'student');
    //Student resource routes are protected by StudentPolicy on the controller
    Route::resource('students', StudentController::class);

    Route::delete('administratives/{administrative}/photo', [AdministrativeController::class, 'destroyPhoto'])
        ->name('administratives.photo.destroy')
        ->can('update', 'administrative');

    //Admnistrative resource routes are protected by AdministrativePolicy on the controller
    Route::resource('administratives', AdministrativeController::class);

    // Confirm (store) the cart and save disciplines registration on the database:
    Route::post('cart', [CartController::class, 'confirm'])
        ->name('cart.confirm')
        ->can('confirm-cart');
});

/* ----- OTHER PUBLIC ROUTES ----- */

// Use Cart routes should be accessible to the public */
Route::middleware('can:use-cart')->group(function () {
    // Add a discipline to the cart:
    Route::post('cart/{discipline}', [CartController::class, 'addToCart'])
        ->name('cart.add');
    // Remove a discipline from the cart:
    Route::delete('cart/{discipline}', [CartController::class, 'removeFromCart'])
        ->name('cart.remove');
    // Show the cart:
    Route::get('cart', [CartController::class, 'show'])->name('cart.show');
    // Clear the cart:
    Route::delete('cart', [CartController::class, 'destroy'])->name('cart.destroy');
});


//Course show is public.
Route::resource('courses', CourseController::class)->only(['show']);

//Disciplines index and show are public
Route::resource('disciplines', DisciplineController::class)->only(['index', 'show']);

require __DIR__ . '/auth.php';
