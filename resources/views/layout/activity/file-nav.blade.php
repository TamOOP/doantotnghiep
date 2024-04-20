<ul class="activity-menu">
    <a href="/course/file?id={{ $file->id }}">
        <li class="activity-menu-item {{ request()->is('course/file') ? 'active' : '' }}">
            Tập tin
        </li>
    </a>
    @if (auth()->user()->role !== 'student')
        <a href="/course/activity/edit?type=file&id={{ $file->id }}">
            <li class="activity-menu-item {{ request()->is('course/activity/edit') ? 'active' : '' }}">
                Chỉnh sửa
            </li>
        </a>
    @endif
</ul>
