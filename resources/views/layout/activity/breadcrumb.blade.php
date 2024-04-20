<ul class="breadcrumbs">
    @if (isset($course))
        <li>
            <a href="/course/view?id={{ $course->id }}">
                {{ $course->name }}
            </a>
        </li>
    @endif

    @yield('breadcrumb-item')

</ul>
