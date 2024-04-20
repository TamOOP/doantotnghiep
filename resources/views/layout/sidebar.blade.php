@if(!$errors->any() && isset($course))
    <aside>
        <div id="btn-open-sidebar">
            <i class="fa fa-bars" aria-hidden="true"></i>
        </div>
        <div class="sidebar {{ session('sidebar') == true ? 'sidebar-show' : '' }} ">
            <div class="p-3" align="right">
                <i class="fa fa-times" id="btn-close-sidebar" aria-hidden="true"></i>
            </div>
            <div class="sidebar-container overflow-y">
                @foreach ($course->topics as $topic)
                    <div class="sidebar-topic">
                        <div class="sidebar-topic-name sidebar-item">
                            <i class="fa fa-angle-down mr-3 expand-icon" aria-hidden="true"></i>
                            <a href="{{ env('APP_URL') }}/course/view?id={{ $course->id }}#topic{{ $topic->id }}"
                                class="text-ellipsis topic-name">
                                {{ $topic->name }}
                            </a>
                        </div>
                        <ul class="topic-content">
                            @foreach ($topic->activities as $activity)
                                <li class="sidebar-activity sidebar-item">
                                    <i class="fa fa-circle mr-3 icon-activity"></i>
                                    {{ $activity->name }}
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endforeach

            </div>
        </div>
    </aside>
@endif
