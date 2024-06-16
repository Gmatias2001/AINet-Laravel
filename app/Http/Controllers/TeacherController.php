<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\TeacherFormRequest;
use App\Models\Department;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class TeacherController extends \Illuminate\Routing\Controller
{
    use AuthorizesRequests;

    public function __construct()
    {
        $this->authorizeResource(Teacher::class);
    }

    public function index(Request $request): View
    {
        $departments = Department::orderBy('name')->pluck('name', 'abbreviation')->toArray();
        $departments = array_merge([null => 'Any department'], $departments);
        $filterByDepartment = $request->query('department');
        $filterByName = $request->query('name');
        $teachersQuery = Teacher::query();
        if ($filterByDepartment !== null) {
            $teachersQuery->where('department', $filterByDepartment);
        }
        // Next 3 lines are required when sorting by name:
        // ->join is necessary so that we have access to the users.name - to be able to order by "users.name"
        // ->select avoids bringing to many fields (that may conflict with each other)

        $teachersQuery
            ->join('users', 'users.id', '=', 'teachers.user_id')
            ->select('teachers.*')
            ->orderBy('users.name');

        // Since we are joining teachers and users, we can simplify the code to search by name
        if ($filterByName !== null) {
            $teachersQuery
                ->where('users.type', 'T')
                ->where('users.name', 'like', "%$filterByName%");
        }
        // Next line were used to filter by name, when there were no join clauses
        // if ($filterByName !== null) {
        //     $usersIds = User::where('type', 'T')
        //         ->where('name', 'like', "%$filterByName%")
        //         ->pluck('id')
        //         ->toArray();
        //     $teachersIds = Teacher::whereIntegerInRaw('user_id', $usersIds)
        //         ->pluck('id')
        //         ->toArray();
        //     $teachersQuery->whereIntegerInRaw('teachers.id', $teachersIds);
        // }

        $teachers = $teachersQuery
            ->with('user')
            ->with('departmentRef')
            ->paginate(20)
            ->withQueryString();
        return view(
            'teachers.index',
            compact('departments', 'teachers', 'filterByDepartment', 'filterByName')
        );
    }

    public function myTeachers(Request $request): View
    {
        $idDisciplines = $request->user()?->student?->disciplines?->pluck('id')?->toArray();
        if (empty($idDisciplines)) {
            return view('teachers.my')->with('teachers', new Collection);
        }
        $teachersQuery = Teacher::join('users', 'users.id', '=', 'teachers.user_id')
            ->select('teachers.*')
            ->orderBy('users.name');
        $teachers = $teachersQuery
            ->join('teachers_disciplines', 'teachers_disciplines.teacher_id', '=', 'teachers.id')
            ->whereIntegerInRaw('teachers_disciplines.discipline_id', $idDisciplines)
            ->with('user', 'departmentRef')
            ->get();
        return view('teachers.my',compact('teachers'));
    }


    public function show(Teacher $teacher): View
    {
        $departments = Department::orderBy('name')->pluck('name', 'abbreviation')->toArray();
        return view('teachers.show')
        ->with('departments', $departments)
            ->with('teacher', $teacher);
    }

    public function create(): View
    {
        $newTeacher = new Teacher();
        // Next 2 lines ensure that the expression $newTeacher->user->name is valid
        $newUser = new User();
        $newUser->type = 'T';
        $newTeacher->user = $newUser;
        $departments = Department::orderBy('name')->pluck('name', 'abbreviation')->toArray();
        $newTeacher->department = 'DEI';
        return view('teachers.create')
            ->with('departments', $departments)
            ->with('teacher', $newTeacher);
    }

    public function store(TeacherFormRequest $request): RedirectResponse
    {
        $validatedData = $request->validated();
        $newTeacher = DB::transaction(function () use ($validatedData, $request) {
            $newUser = new User();
            $newUser->type = 'T';
            $newUser->name = $validatedData['name'];
            $newUser->email = $validatedData['email'];
            // Only sets admin field if it has permission  to do it.
            // Otherwise, admin is false
            $newUser->admin = $request->user()?->can('createAdmin', Teacher::class)
                ? $validatedData['admin']
                : 0;
            $newUser->gender = $validatedData['gender'];
            // Initial password is always 123
            $newUser->password =bcrypt('123');
            $newUser->save();
            $newTeacher = new Teacher();
            $newTeacher->user_id = $newUser->id;
            $newTeacher->department = $validatedData['department'];
            $newTeacher->office = $validatedData['office'];
            $newTeacher->extension = $validatedData['extension'];
            $newTeacher->locker = $validatedData['locker'];
            $newTeacher->save();
            // File store is the last thing to execute!
            // files do not rollback, so the probability of having
            // a pending file (not referenced by any teacher)
            // is reduced by being the last operation
            if ($request->hasFile('photo_file')) {
                $path = $request->photo_file->store('public/photos');
                $newUser->photo_url = basename($path);
                $newUser->save();
            }
            return $newTeacher;
        });
        $newTeacher->user->sendEmailVerificationNotification();
        $url = route('teachers.show', ['teacher' => $newTeacher]);
        $htmlMessage = "Teacher <a href='$url'><u>{$newTeacher->user->name}</u></a> has been created successfully!";
        return redirect()->route('teachers.index')
            ->with('alert-type', 'success')
            ->with('alert-msg', $htmlMessage);
    }

    public function edit(Teacher $teacher): View
    {
        $departments = Department::orderBy('name')->pluck('name', 'abbreviation')->toArray();
        return view('teachers.edit')
            ->with('departments', $departments)
            ->with('teacher', $teacher);
    }

    public function update(TeacherFormRequest $request, Teacher $teacher): RedirectResponse
    {
        $validatedData = $request->validated();
        $teacher = DB::transaction(function () use ($validatedData, $teacher, $request) {
            $teacher->department = $validatedData['department'];
            $teacher->office = $validatedData['office'];
            $teacher->extension = $validatedData['extension'];
            $teacher->locker = $validatedData['locker'];
            $teacher->save();
            $teacher->user->type = 'T';
            $teacher->user->name = $validatedData['name'];
            $teacher->user->email = $validatedData['email'];
            // Only updates admin field if it has permission  to do it.
            // Otherwise, do not change it (ignore it)
            if ($request->user()?->can('updateAdmin', $teacher)) {
                $teacher->user->admin = $validatedData['admin'];
            }
            $teacher->user->gender = $validatedData['gender'];
            $teacher->user->save();
            if ($request->hasFile('photo_file')) {
                // Delete previous file (if any)
                if ($teacher->user->photo_url &&
                    Storage::fileExists('public/photos/' . $teacher->user->photo_url)) {
                        Storage::delete('public/photos/' . $teacher->user->photo_url);
                }
                $path = $request->photo_file->store('public/photos');
                $teacher->user->photo_url = basename($path);
                $teacher->user->save();
            }
            return $teacher;
        });
        $url = route('teachers.show', ['teacher' => $teacher]);
        $htmlMessage = "Teacher <a href='$url'><u>{$teacher->user->name}</u></a> has been updated successfully!";
        if ($request->user()->can('viewAny', Teacher::class)) {
            return redirect()->route('teachers.index')
                ->with('alert-type', 'success')
                ->with('alert-msg', $htmlMessage);
        }
        return redirect()->back()
        ->with('alert-type', 'success')
        ->with('alert-msg', $htmlMessage);
    }

    public function destroy(Teacher $teacher): RedirectResponse
    {
        try {
            $url = route('teachers.show', ['teacher' => $teacher]);
            $totalTeachersDisciplines = $teacher->disciplines()->count();
            if ($totalTeachersDisciplines == 0) {
                DB::transaction(function () use ($teacher) {
                    $fileToDelete = $teacher->user->photo_url;
                    $teacher->delete();
                    $teacher->user->delete();
                    if ($fileToDelete) {
                        if (Storage::fileExists('public/photos/' . $fileToDelete)) {
                            Storage::delete('public/photos/' . $fileToDelete);
                        }
                    }
                });
                $alertType = 'success';
                $alertMsg = "Teacher {$teacher->user->name} has been deleted successfully!";
            } else {
                $alertType = 'warning';
                $gender = $teacher->user->gender == 'M' ? 'he' : 'she';
                $justification = match (true) {
                    $totalTeachersDisciplines <= 0 => "",
                    $totalTeachersDisciplines == 1 => "$gender teaches 1 discipline",
                    $totalTeachersDisciplines > 1 => "$gender teaches $totalTeachersDisciplines disciplines",
                };
                $alertMsg = "Teacher <a href='$url'><u>{$teacher->user->name}</u></a> cannot be deleted because $justification.";
            }
        } catch (\Exception $error) {
            $alertType = 'danger';
            $alertMsg = "It was not possible to delete the teacher
                            <a href='$url'><u>{$teacher->user->name}</u></a>
                            because there was an error with the operation!";
        }
        return redirect()->route('teachers.index')
            ->with('alert-type', $alertType)
            ->with('alert-msg', $alertMsg);
    }

    public function destroyPhoto(Teacher $teacher): RedirectResponse
    {
        if ($teacher->user->photo_url) {
            if (Storage::fileExists('public/photos/' . $teacher->user->photo_url)) {
                Storage::delete('public/photos/' . $teacher->user->photo_url);
            }
            $teacher->user->photo_url = null;
            $teacher->user->save();
        return redirect()->back()
            ->with('alert-type', 'success')
            ->with('alert-msg', "Photo of teacher {$teacher->user->name} has been deleted.");
        }
        return redirect()->back();
    }
}
