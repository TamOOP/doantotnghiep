<ul class="activity-menu">
    <a href="/course/assign?id={{ $assign->id }}">
        <li class="activity-menu-item {{ request()->is('course/assign') ? 'active' : '' }}">
            Bài tập
        </li>
    </a>
    @if (auth()->user()->role !== 'student')
        <a href="/course/activity/edit?type=assign&id={{ $assign->id }}">
            <li class="activity-menu-item {{ request()->is('course/activity/edit') ? 'active' : '' }}">
                Chỉnh sửa
            </li>
        </a>
        <a href="/course/assign/submission?id={{ $assign->id }}">
            <li class="activity-menu-item {{ request()->is('course/assign/submission') ? 'active' : '' }}">
                Điểm số
            </li>
        </a>
    @endif

</ul>
