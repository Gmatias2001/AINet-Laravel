@extends('layouts.main')

@section('header-title', 'List of Students')

@section('main')
    <div class="flex justify-center">
        <div class="my-4 p-6 bg-white dark:bg-gray-900 overflow-hidden
                    shadow-sm sm:rounded-lg text-gray-900 dark:text-gray-50">
            <x-students.filter-card
                :filterAction="route('students.index')"
                :resetUrl="route('students.index')"
                :courses="$courseOptions"
                :course="old('course', $filterByCourse)"
                :name="old('name', $filterByName)"
                class="mb-6"
                />
            @can('create', App\Models\Student::class)
                <div class="flex items-center gap-4 mb-4">
                    <x-button
                        href="{{ route('students.create') }}"
                        text="Create a new student"
                        type="success"/>
                </div>
            @endcan
            <div class="font-base text-sm text-gray-700 dark:text-gray-300">
                <x-students.table :students="$students"
                    :showCourse="true"
                    :showView="true"
                    :showEdit="true"
                    :showDelete="true"
                    />
            </div>
            <div class="mt-4">
                {{ $students->links() }}
            </div>
        </div>
    </div>
@endsection
