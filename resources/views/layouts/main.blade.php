<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>CineMagic</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts AND CSS Fileds -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased">
    <div class="min-h-screen bg-gray-100 dark:bg-gray-800">

        <!-- Navigation Menu -->
        <nav class="bg-white dark:bg-gray-900 border-b border-gray-100 dark:border-gray-800">
            <!-- Navigation Menu Full Container -->
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Logo + Menu Items + Hamburger -->
                <div class="relative flex flex-col sm:flex-row px-6 sm:px-0 grow justify-between">
                    <!-- Logo -->
                    <div class="shrink-0 -ms-4">
                        <a href="{{ route('home')}}">
                            <div class="h-16 w-64 bg-cover bg-[url('../img/cinemagic_h.svg')] dark:bg-[url('../img/cinemagic_h_white.svg')]"></div>
                        </a>
                    </div>

                    <!-- Menu Items -->
                    <div id="menu-container" class="grow flex flex-col sm:flex-row items-stretch
                    invisible h-0 sm:visible sm:h-auto">
                        <!-- Menu Item: Courses -->
                        @can('viewShowcase', App\Models\Course::class)
                            <x-menus.menu-item
                                content="Courses"
                                href="{{ route('courses.showcase') }}"
                                selected="{{ Route::currentRouteName() == 'courses.showcase'}}"
                            />
                        @endcan

                        <!-- Menu Item: Curricula -->
                        @can('viewCurriculum', App\Models\Course::class)
                            <x-menus.submenu-full-width
                                content="Curricula"
                                selectable="1"
                                selected="0"
                                uniqueName="submenu_curricula">
                                @foreach ($courses as $course)
                                    <x-menus.submenu-item
                                    :content="$course->fullName"
                                    selectable="1"
                                    selected="0"
                                    href="{{ route('courses.curriculum', ['course' => $course]) }}"/>
                                @endforeach
                            </x-menus.submenu-full-width>
                        @endcan
                        <!-- Menu Item: Disciplines -->
                        @can('viewAny', App\Models\Discipline::class)
                        <x-menus.menu-item
                            content="Disciplines"
                            selectable="1"
                            href="{{ route('disciplines.index') }}"
                            selected="{{ Route::currentRouteName() == 'disciplines.index'}}"
                            />
                        @endcan

                        <!-- Menu Item: Teachers -->
                        @can('viewAny', App\Models\Teacher::class)
                            <x-menus.menu-item
                                content="Teachers"
                                selectable="1"
                                href="{{ route('teachers.index') }}"
                                selected="{{ Route::currentRouteName() == 'teachers.index'}}"
                                />
                        @endcan

                        {{-- If user has any of the 4 menu options previlege, then it should show the submenu --}}
                        @if(
                            Gate::check('viewAny', App\Models\Student::class) ||
                            Gate::check('viewAny', App\Models\User::class) ||
                            Gate::check('viewAny', App\Models\Department::class) ||
                            Gate::check('viewAny', App\Models\Course::class)
                            )
                        <!-- Menu Item: Others -->
                        <x-menus.submenu
                            selectable="0"
                            uniqueName="submenu_others"
                            content="More">
                                @can('viewAny', App\Models\Student::class)
                                <x-menus.submenu-item
                                    content="Students"
                                    selectable="0"
                                    href="{{ route('students.index') }}" />
                                @endcan
                                @can('viewAny', App\Models\User::class)
                                <x-menus.submenu-item
                                    content="Administratives"
                                    selectable="0"
                                    href="{{ route('administratives.index') }}" />
                                @endcan
                                <hr>
                                @can('viewAny', App\Models\Department::class)
                                <x-menus.submenu-item
                                    content="Departments"
                                    selectable="0"
                                    href="{{ route('departments.index') }}"/>
                                @endcan
                                @can('viewAny', App\Models\Course::class)
                                <x-menus.submenu-item
                                    content="Course Management"
                                    href="{{ route('courses.index') }}"/>
                                @endcan
                        </x-menus.submenu>
                        @endif

                        <div class="grow"></div>

                        <!-- Menu Item: Cart -->
                        @if (session('cart'))
                            @can('use-cart')
                            <x-menus.cart
                                :href="route('cart.show')"
                                selectable="1"
                                selected="{{ Route::currentRouteName() == 'cart.show'}}"
                                :total="session('cart')->count()"/>
                            @endcan
                        @endif

                        @auth
                        <x-menus.submenu
                            selectable="0"
                            uniqueName="submenu_user"
                            >
                            <x-slot:content>
                                <div class="pe-1">
                                    <img src="{{ Auth::user()->photoFullUrl}}" class="w-11 h-11 min-w-11 min-h-11 rounded-full">
                                </div>
                                {{-- ATENÇÃO - ALTERAR FORMULA DE CALCULO DAS LARGURAS MÁXIMAS QUANDO O MENU FOR ALTERADO --}}
                                <div class="ps-1 sm:max-w-[calc(100vw-39rem)] md:max-w-[calc(100vw-41rem)] lg:max-w-[calc(100vw-46rem)] xl:max-w-[34rem] truncate">
                                    {{ Auth::user()->name }}
                                </div>
                            </x-slot>
                            @can('viewMy', App\Models\Discipline::class)
                            <x-menus.submenu-item
                                content="My Disciplines"
                                selectable="0"
                                href="{{ route('disciplines.my') }}"/>
                            @endcan
                            @can('viewMy', App\Models\Teacher::class)
                            <x-menus.submenu-item
                                content="My Teachers"
                                selectable="0"
                                href="{{ route('teachers.my') }}"/>
                            @endcan
                            @can('viewMy', App\Models\Student::class)
                                <x-menus.submenu-item
                                    content="My Students"
                                    selectable="0"
                                    href="{{ route('students.my') }}"/>
                                <hr>
                            @endcan
                            @auth
                            <hr>
                            <x-menus.submenu-item
                                content="Profile"
                                selectable="0"
                                :href="match(Auth::user()->type) {
                                    'A' => route('administratives.edit', ['administrative' => Auth::user()]),
                                    'T' => route('teachers.edit', ['teacher' => Auth::user()->teacher]),
                                    'S' => route('students.edit', ['student' => Auth::user()->student]),
                                }"/>
                            <x-menus.submenu-item
                                content="Change Password"
                                selectable="0"
                                href="{{ route('profile.edit.password') }}"/>
                            @endauth
                            <hr>
                            <form id="form_to_logout_from_menu" method="POST" action="{{ route('logout') }}" class="hidden">
                                @csrf
                            </form>
                            <x-menus.submenu-item
                                content="Log Out"
                                selectable="0"
                                form="form_to_logout_from_menu"/>
                        </x-menus.submenu>
                        @else
                        <!-- Menu Item: Login -->
                        <x-menus.menu-item
                            content="Login"
                            selectable="1"
                            href="{{ route('login') }}"
                            selected="{{ Route::currentRouteName() == 'login'}}"
                            />
                        @endauth
                    </div>
                    <!-- Hamburger -->
                    <div class="absolute right-0 top-0 flex sm:hidden pt-3 pe-3 text-black dark:text-gray-50">
                        <button id="hamburger_btn">
                            <svg class="h-8 w-8" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                <path id="hamburger_btn_open" stroke-linecap="round" stroke-linejoin="round"
                                stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                                <path class="invisible" id="hamburger_btn_close" stroke-linecap="round"
                                stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </nav>

        <main>
            <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
                @if (session('alert-msg'))
                    <x-alert type="{{ session('alert-type') ?? 'info' }}">
                        {!! session('alert-msg') !!}
                    </x-alert>
                @endif
                @if (!$errors->isEmpty())
                    <x-alert type="warning" message="Operation failed because there are validation errors!" />
                @endif
                @yield('main')
            </div>
        </main>

        <!-- Page Footer -->
        <footer class="bg-white dark:bg-gray-900 shadow">
            <div class="flex justify-between gro max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8 ">
                <div>
                    <h4 class="mb-1 text-base text-gray-500 dark:text-gray-400 leading-tight">
                        Projeto AINet 2024
                    </h4>
                    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                        Grupo X
                    </h2>
                </div>
                <div class="columns-2 ">
                    <a class=" text-blue-300 no-underline bg-pu hover:text-pink-500 hover:text-underline text-center transform hover:scale-125 duration-300 ease-in-out"
                        href="https://twitter.com/intent/tweet?url=#">
                        <svg class="fill-current h-6" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32">
                            <path
                                d="M30.063 7.313c-.813 1.125-1.75 2.125-2.875 2.938v.75c0 1.563-.188 3.125-.688 4.625a15.088 15.088 0 0 1-2.063 4.438c-.875 1.438-2 2.688-3.25 3.813a15.015 15.015 0 0 1-4.625 2.563c-1.813.688-3.75 1-5.75 1-3.25 0-6.188-.875-8.875-2.625.438.063.875.125 1.375.125 2.688 0 5.063-.875 7.188-2.5-1.25 0-2.375-.375-3.375-1.125s-1.688-1.688-2.063-2.875c.438.063.813.125 1.125.125.5 0 1-.063 1.5-.25-1.313-.25-2.438-.938-3.313-1.938a5.673 5.673 0 0 1-1.313-3.688v-.063c.813.438 1.688.688 2.625.688a5.228 5.228 0 0 1-1.875-2c-.5-.875-.688-1.813-.688-2.75 0-1.063.25-2.063.75-2.938 1.438 1.75 3.188 3.188 5.25 4.25s4.313 1.688 6.688 1.813a5.579 5.579 0 0 1 1.5-5.438c1.125-1.125 2.5-1.688 4.125-1.688s3.063.625 4.188 1.813a11.48 11.48 0 0 0 3.688-1.375c-.438 1.375-1.313 2.438-2.563 3.188 1.125-.125 2.188-.438 3.313-.875z">
                            </path>
                        </svg>
                    </a>
                    <a class="text-blue-300 no-underline hover:text-pink-500 hover:text-underline text-center transform hover:scale-125 duration-300 ease-in-out"
                        href="https://www.facebook.com/sharer/sharer.php?u=#">
                        <svg class="fill-current h-6" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32">
                            <path d="M19 6h5V0h-5c-3.86 0-7 3.14-7 7v3H8v6h4v16h6V16h5l1-6h-6V7c0-.542.458-1 1-1z">
                            </path>
                        </svg>
                    </a>
                </div>
            </div>
        </footer>
    </div>
</body>

</html>
