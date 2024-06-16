@php
    $mode = $mode ?? 'edit';
    $readonly = $mode == 'show';
@endphp

<div class="flex flex-wrap space-x-8">
    <div class="grow mt-6 space-y-4">
        <x-field.input name="name" label="Name" :readonly="$readonly"
                        value="{{ old('name', $student->user->name) }}"/>
        <x-field.input name="email" type="email" label="Email" :readonly="$readonly"
                        value="{{ old('email', $student->user->email) }}"/>
        <x-field.radiogroup name="gender" label="Gender" :readonly="$readonly"
            value="{{ old('gender', $student->user->gender) }}"
            :options="['M' => 'Masculine', 'F' => 'Feminine']"/>
        <x-field.select name="course" label="Course" :readonly="$readonly"
            value="{{ old('course', $student->course) }}"
            :options="$courses->pluck('fullName', 'abbreviation')->toArray()"/>
            <x-field.input name="number" label="Student Number" :readonly="$readonly"
                        value="{{ old('number', $student->number) }}"/>
    </div>
    <div class="pb-6">
        <x-field.image
            name="photo_file"
            label="Photo"
            width="md"
            :readonly="$readonly"
            deleteTitle="Delete Photo"
            :deleteAllow="($mode == 'edit') && ($student->user->photo_url)"
            deleteForm="form_to_delete_photo"
            :imageUrl="$student->user->photoFullUrl"/>
    </div>

</div>
