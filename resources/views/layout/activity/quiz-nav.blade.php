<ul class="activity-menu">
    <a href="/course/quiz?id={{ $exam->id }}">
        <li class="activity-menu-item {{ request()->is('course/quiz') ? 'active' : '' }}">
            Tổng quan
        </li>
    </a>
    @if (auth()->user()->role !== 'student')
        <a href="/course/activity/edit?type=quiz&id={{ $exam->id }}">
            <li class="activity-menu-item {{ request()->is('course/activity/edit') ? 'active' : '' }}">
                Chỉnh sửa
            </li>
        </a>
        <a href="/course/quiz/question?id={{ $exam->id }}">
            <li
                class="activity-menu-item {{ request()->is('course/quiz/question') || request()->is('course/quiz/question/*') ? 'active' : '' }}">
                Câu hỏi
            </li>
        </a>
        <a href="/course/quiz/result?id={{ $exam->id }}">
            <li class="activity-menu-item {{ request()->is('course/quiz/result') ? 'active' : '' }}">
                Kết quả
            </li>
        </a>
    @endif
</ul>
