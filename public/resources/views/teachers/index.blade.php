@extends('layouts.main')

@section('header-title', 'List of Teachers')

@section('main')
    <div class="flex justify-center">
        <div class="my-4 p-6 bg-white dark:bg-gray-900 overflow-hidden
                    shadow-sm sm:rounded-lg text-gray-900 dark:text-gray-50">
            <x-teachers.filter-card
                :filterAction="route('teachers.index')"
                :resetUrl="route('teachers.index')"
                :departments="$departments"
                :department="old('department', $filterByDepartment)"
                :name="old('name', $filterByName)"
                class="mb-6"
                />
            @can('create', App\Models\Teacher::class)
                <div class="flex items-center gap-4 mb-4">
                    <x-button
                        href="{{ route('teachers.create') }}"
                        text="Create a new teacher"
                        type="success"/>
                </div>
            @endcan
            <div class="font-base text-sm text-gray-700 dark:text-gray-300">
                <x-teachers.table :teachers="$teachers"
                    :showDepartment="true"
                    :showView="true"
                    :showEdit="true"
                    :showDelete="true"
                    />
            </div>
            <div class="mt-4">
                {{ $teachers->links() }}
            </div>
        </div>
    </div>
@endsection
