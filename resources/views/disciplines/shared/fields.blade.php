@php
    $mode = $mode ?? 'edit';
    $readonly = $mode == 'show';
@endphp
<x-field.input name="abbreviation" label="Abbreviation" width="md"
                :readonly="$readonly"
                value="{{ old('abbreviation', $discipline->abbreviation) }}"/>
<x-field.input name="name" label="Name" :readonly="$readonly"
                value="{{ old('name', $discipline->name) }}"/>
<x-field.input name="name_pt" label="Name (Portuguese)" :readonly="$readonly"
               value="{{ old('name_pt', $discipline->name_pt) }}"/>

<x-field.select name="course" label="Course" :readonly="$readonly"
    value="{{ old('course', $discipline->course) }}"
    :options="$courses->pluck('fullName', 'abbreviation')->toArray()"/>
<div class="flex space-x-4">
    <x-field.input name="year" label="Year" width="sm"
                    :readonly="$readonly"
                    value="{{ old('year', $discipline->year) }}"/>
    <x-field.input name="semester" label="Semester" width="sm"
                    :readonly="$readonly"
                    value="{{ old('semester', $discipline->semester) }}"/>
    <x-field.select name="semester" label="Semester" width="sm"
                    :readonly="$readonly"
                    value="{{ old('semester', $discipline->semester) }}"
                    defaultValue="1"
                    :options="[
                        0 => 'Annual',
                        1 => '1st',
                        2 => '2nd',
                    ]"/>
    <x-field.input name="ECTS" label="ECTS" width="sm"
                    :readonly="$readonly"
                    value="{{ old('ECTS', $discipline->ECTS) }}"/>
    <x-field.input name="hours" label="Hours" width="sm"
                    :readonly="$readonly"
                    value="{{ old('hours', $discipline->hours) }}"/>
</div>
<div class="flex space-x-4">
    <x-field.checkbox name="optional" label="Optional" :readonly="$readonly"
                    value="{{ old('optional', $discipline->optional) }}"/>
</div>
