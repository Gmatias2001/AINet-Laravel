@php
    $mode = $mode ?? 'edit';
    $readonly = $mode == 'show';
    $adminReadonly = $readonly;
    if (!$adminReadonly) {
        if ($mode == 'create') {
            $adminReadonly = Auth::user()?->cannot('createAdmin', App\Models\Teacher::class);
        } elseif ($mode == 'edit') {
            $adminReadonly = Auth::user()?->cannot('updateAdmin', $teacher);
        } else {
            $adminReadonly = true;
        }
    }
@endphp

<div class="flex flex-wrap space-x-8">
    <div class="grow mt-6 space-y-4">
        <x-field.input name="name" label="Name" :readonly="$readonly"
                        value="{{ old('name', $teacher->user->name) }}"/>
        <x-field.input name="email" type="email" label="Email" :readonly="$readonly"
                        value="{{ old('email', $teacher->user->email) }}"/>
        <x-field.radiogroup name="gender" label="Gender" :readonly="$readonly"
            value="{{ old('gender', $teacher->user->gender) }}"
            :options="['M' => 'Masculine', 'F' => 'Feminine']"/>
        <x-field.select name="department" label="Department" :readonly="$readonly"
            value="{{ old('department', $teacher->department) }}"
            :options="$departments"/>
        <div class="flex space-x-4">
            <x-field.input name="office" label="Office" :readonly="$readonly"
                        value="{{ old('office', $teacher->office) }}"/>
            <x-field.input name="extension" label="Extension" :readonly="$readonly"
                        value="{{ old('extension', $teacher->extension) }}"/>
            <x-field.input name="locker" label="Locker" :readonly="$readonly"
                        value="{{ old('locker', $teacher->locker) }}"/>
        </div>
        <x-field.checkbox name="admin" label="Administrator" :readonly="$adminReadonly"
                        value="{{ old('admin', $teacher->user->admin) }}"/>
    </div>
    <div class="pb-6">
        <x-field.image
            name="photo_file"
            label="Photo"
            width="md"
            :readonly="$readonly"
            deleteTitle="Delete Photo"
            :deleteAllow="($mode == 'edit') && ($teacher->user->photo_url)"
            deleteForm="form_to_delete_photo"
            :imageUrl="$teacher->user->photoFullUrl"/>
    </div>
</div>
