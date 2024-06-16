@php
    $mode = $mode ?? 'edit';
    $readonly = $mode == 'show';
@endphp
<div class="flex flex-wrap space-x-8">
    <div class="grow mt-6 space-y-4">
        <x-field.input name="abbreviation" label="Abbreviation" width="md"
                        :readonly="$readonly || ($mode == 'edit')"
                        value="{{ old('abbreviation', $course->abbreviation) }}"/>
        <x-field.radio-group name="type" label="Type of course" :readonly="$readonly"
                        value="{{ old('type', $course->type) }}"
                        :options="[
                            'Degree' => 'Degree',
                            'Master' => 'Master',
                            'TESP' => 'TESP'
                        ]"/>
        <x-field.input name="name" label="Name" :readonly="$readonly"
                        value="{{ old('name', $course->name) }}"/>
        <x-field.input name="name_pt" label="Name (Portuguese)" :readonly="$readonly"
                    value="{{ old('name_pt', $course->name_pt) }}"/>
        <div class="flex space-x-4">
            <x-field.input name="semesters" label="Nº Semesters" width="sm"
                            :readonly="$readonly"
                            value="{{ old('semesters', $course->semesters) }}"/>
            <x-field.input name="ECTS" label="Nº ECTS" width="sm" :readonly="$readonly"
                            value="{{ old('ECTS', $course->ECTS) }}"/>
            <x-field.input name="places" label="Nº Places" width="sm" :readonly="$readonly"
                            value="{{ old('places', $course->places) }}"/>
        </div>
        <x-field.input name="contact" label="Contact" :readonly="$readonly"
                            value="{{ old('contact', $course->contact) }}"/>
        <x-field.text-area name="objectives" label="Objectives" :readonly="$readonly"
                            value="{{ old('objectives', $course->objectives) }}"/>
        <x-field.text-area name="objectives_pt" label="Objectives (Portuguese)"
                        :readonly="$readonly"
                        value="{{ old('objectives_pt', $course->objectives_pt) }}"/>
    </div>
    <div class="pb-6">
        <x-field.image
            name="image_file"
            label="Course Image"
            width="md"
            :readonly="$readonly"
            deleteTitle="Delete Image"
            :deleteAllow="($mode == 'edit') && ($course->imageExists)"
            deleteForm="form_to_delete_image"
            :imageUrl="$course->imageUrl"/>
    </div>
</div>
