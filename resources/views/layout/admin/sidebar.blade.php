@if (auth()->user()->role == 'admin')
    <aside>
        <div id="btn-open-sidebar">
            <i class="fa fa-bars" aria-hidden="true"></i>
        </div>
        <div class="sidebar {{ session('sidebar') == true ? 'sidebar-show' : '' }} ">
            <div class="p-3" align="right">
                <i class="fa fa-times" id="btn-close-sidebar" aria-hidden="true"></i>
            </div>
            <div class="sidebar-container overflow-y">
                <ul>
                    <li>
                        <div
                            class="text-ellipsis topic-name line sidebar-item {{ strpos(request()->path(), 'admin/user') === 0 ? 'active' : '' }}">
                            <i class="fa fa-angle-down mr-3 expand-icon" aria-hidden="true"></i>
                            <span>Người dùng</span>
                        </div>
                        <ul class="topic-content">
                            <a href="/admin/user">
                                <li
                                    class="sidebar-activity line text-ellipsis 
                                    {{ request()->path() == 'admin/user' ? 'active' : '' }}">

                                    <i class="fa fa-circle mr-3 icon-activity"></i>
                                    <span>Danh sách người dùng</span>
                                </li>
                            </a>
                            <a href="/admin/user/add">
                                <li
                                    class="sidebar-activity line text-ellipsis {{ request()->path() == 'admin/user/add' ? 'active' : '' }}">
                                    <i class="fa fa-circle mr-3 icon-activity"></i>
                                    <span>Thêm người dùng mới</span>
                                </li>
                            </a>
                            <a href="/admin/user/role">
                                <li class="sidebar-activity line text-ellipsis {{ request()->path() == 'admin/user/role' ? 'active' : '' }}">
                                    <i class="fa fa-circle mr-3 icon-activity"></i>
                                    <span>Chỉ định vai trò người dùng</span>
                                </li>
                            </a>
                        </ul>
                    </li>
                    <li>
                        <div
                            class="text-ellipsis topic-name line sidebar-item
                            {{ strpos(request()->path(), 'course') !== false ? 'active' : '' }}">
                            <i class="fa fa-angle-down mr-3 expand-icon" aria-hidden="true"></i>
                            <span>Khóa học</span>
                        </div>
                        <ul class="topic-content">
                            <a href="/admin/course">
                                <li
                                    class="sidebar-activity line text-ellipsis {{ request()->path() == 'admin/course' ? 'active' : '' }}">
                                    <i class="fa fa-circle mr-3 icon-activity"></i>
                                    <span>Danh sách khóa học</span>
                                </li>
                            </a>
                            <a href="/course/add?type=course">
                                <li
                                    class="sidebar-activity line text-ellipsis {{ request()->path() == 'course/add' ? 'active' : '' }}">
                                    <i class="fa fa-circle mr-3 icon-activity"></i>
                                    <span>Thêm khóa học mới</span>
                                </li>
                            </a>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </aside>
@endif
