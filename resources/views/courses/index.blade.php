@extends('layouts.main')

@section('header-title', 'List of Courses')

@section('main')
    <div class="flex justify-center">
        <div class="my-4 p-6 bg-white dark:bg-gray-900 overflow-hidden
                    shadow-sm sm:rounded-lg text-gray-900 dark:text-gray-50">
            @can('create', App\Models\Course::class)
                <div class="flex items-center gap-4 mb-4">
                    <x-button
                        href="{{ route('courses.create') }}"
                        text="Create a new course"
                        type="success"/>
                </div>
            @endcan
            <div class="font-base text-sm text-gray-700 dark:text-gray-300">
                <x-courses.table :courses="$allCourses"
                    :showView="true"
                    :showEdit="true"
                    :showDelete="true"
                    />
            </div>
            <div class="mt-4">
                {{ $allCourses->links() }}
            </div>
        </div>
    </div>
@endsection
