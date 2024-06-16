@extends('layouts.main')

@section('header-title', 'Student "' . $student->user->name . '"')

@section('main')
<div class="flex flex-col space-y-6">
    <div class="p-4 sm:p-8 bg-white dark:bg-gray-900 shadow sm:rounded-lg">
        <div class="max-full">
            <section>
                <div class="flex flex-wrap justify-end items-center gap-4 mb-4">
                    @can('create', App\Models\Student::class)
                    <x-button
                        href="{{ route('students.create') }}"
                        text="New"
                        type="success"/>
                    @endcan
                    @can('update', $student)
                    <x-button
                        href="{{ route('students.edit', ['student' => $student]) }}"
                        text="Edit"
                        type="primary"/>
                    @endcan
                    @can('delete', $student)
                    <form method="POST" action="{{ route('students.destroy', ['student' => $student]) }}">
                        @csrf
                        @method('DELETE')
                        <x-button
                            element="submit"
                            text="Delete"
                            type="danger"/>
                    </form>
                    @endcan
                </div>
                <header>
                    <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                        Student "{{ $student->user->name }}"
                    </h2>
                </header>
                @include('students.shared.fields', ['mode' => 'show'])

                @can('viewAny', App\Models\Discipline::class)
                    <h3 class="pt-16 pb-4 text-2xl font-medium text-gray-900 dark:text-gray-100">
                        Disciplines
                    </h3>
                    <x-disciplines.table :disciplines="$student->disciplines"
                        :showView="true"
                        :showEdit="false"
                        :showDelete="false"
                        :showAddToCart="true"
                        class="pt-4"
                        />
                @endcan
            </section>
        </div>
    </div>
</div>
@endsection
