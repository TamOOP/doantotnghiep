@if (!$errors->any())
    <nav class="course-nav mt-3">
        <ul class="course-menu">
            <a href="/course/view?id={{ $course->id }}">
                <li class="course-menu-item {{ request()->is('course/view') ? 'active' : '' }}">
                    Khóa học
                </li>
            </a>
            @if (auth()->user()->role !== 'student')
                <a href="/course/edit?type=course&id={{ $course->id }}">
                    <li class="course-menu-item {{ request()->is('course/edit') ? 'active' : '' }}">
                        Cài đặt
                    </li>
                </a>
            @endif
            <a href="/course/member?id={{ $course->id }}">
                <li class="course-menu-item {{ request()->is('course/member') ? 'active' : '' }}">
                    Danh sách thành viên
                </li>
            </a>
            {{-- @if (auth()->user()->role !== 'student')
                <a href="/course/grade?id={{ $course->id }}">
                    <li class="course-menu-item {{ request()->is('course/grade') ? 'active' : '' }}">
                        Điểm số
                    </li>
                </a>
            @endif --}}
        </ul>
    </nav>
@endif
