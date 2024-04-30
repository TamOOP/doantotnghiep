<header>
    <nav class="navbar fixed-top">
        <a href="/" class="navbar-brand mr-4 p-0">
            Learn 360
        </a>
        <div class="primary-nav">
            <ul class="navbar-nav">
                <li class="nav-item ">
                    <a href="/" class="nav-link {{ request()->is('/') ? 'active' : '' }}">
                        Trang chủ
                    </a>
                </li>
                @if (auth()->check())
                    <li class="nav-item">
                        <a href="/course"
                            class="nav-link {{ request()->is('course') || request()->is('course/*') ? 'active' : '' }}">
                            Khóa học của tôi
                        </a>
                    </li>
                    @if (auth()->user()->role == 'admin')
                        <li class="nav-item">
                            <a href="/admin/user" class="nav-link {{ request()->is('admin') ? 'active' : '' }}">
                                Quản trị trang web
                            </a>
                        </li>
                    @endif
                @endif
            </ul>
        </div>

        <div class="user-nav ml-auto">
            @if (auth()->check())
                <div class="member-block">
                    <div class="notification">
                        <div id="notify-icon">
                            <i class="fas fa-bell"></i>
                            <div class="alert-circle">2</div>
                        </div>
                        <div class="notification-dropdown">
                            <div class="notify-icon-pointer">
                                <div class="triangle-border triangle">

                                </div>
                                <div class="triangle-layout triangle">

                                </div>
                            </div>
                            <div class="notification-container">
                                <div class="notify-header">
                                    <span>Thông báo</span>
                                    <a href="/notification" class="setting-icon">
                                        <i class="fa fa-cog"></i>
                                    </a>
                                </div>
                                <div class="notify-body h-100">
                                    <div class="notify-item p-2">
                                        <i class="fa fa-lightbulb d-flex align-items-center" aria-hidden="true"></i>
                                        <div class="notify-content">
                                            <p class="notify-title">Bạn tham gia thành công khóa học "My first course"
                                            </p>
                                            <div class="d-flex align-items-center">
                                                <span class="notify-time">
                                                    35 days 4 hours ago
                                                </span>
                                                <a href="" class="notify-link">Xem</a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="notify-item p-2">
                                        <i class="fa fa-lightbulb d-flex align-items-center" aria-hidden="true"></i>
                                        <div class="notify-content">
                                            <p class="notify-title">Bạn tham gia thành công khóa học "My first course"
                                            </p>
                                            <div class="d-flex align-items-center">
                                                <span class="notify-time">
                                                    35 days 4 hours ago
                                                </span>
                                                <a href="" class="notify-link">Xem</a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="notify-item p-2">
                                        <i class="fa fa-lightbulb d-flex align-items-center" aria-hidden="true"></i>
                                        <div class="notify-content">
                                            <p class="notify-title">Bạn tham gia thành công khóa học "My first course"
                                            </p>
                                            <div class="d-flex align-items-center">
                                                <span class="notify-time">
                                                    35 days 4 hours ago
                                                </span>
                                                <a href="" class="notify-link">Xem</a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="notify-item p-2">
                                        <i class="fa fa-lightbulb d-flex align-items-center" aria-hidden="true"></i>
                                        <div class="notify-content">
                                            <p class="notify-title">Bạn tham gia thành công khóa học "My first course"
                                            </p>
                                            <div class="d-flex align-items-center">
                                                <span class="notify-time">
                                                    35 days 4 hours ago
                                                </span>
                                                <a href="" class="notify-link">Xem</a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="notify-item p-2">
                                        <i class="fa fa-lightbulb d-flex align-items-center" aria-hidden="true"></i>
                                        <div class="notify-content">
                                            <p class="notify-title">Bạn tham gia thành công khóa học "My first course"
                                            </p>
                                            <div class="d-flex align-items-center">
                                                <span class="notify-time">
                                                    35 days 4 hours ago
                                                </span>
                                                <a href="" class="notify-link">Xem</a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="notify-item p-2">
                                        <i class="fa fa-lightbulb d-flex align-items-center" aria-hidden="true"></i>
                                        <div class="notify-content">
                                            <p class="notify-title">Bạn tham gia thành công khóa học "My first course"
                                            </p>
                                            <div class="d-flex align-items-center">
                                                <span class="notify-time">
                                                    35 days 4 hours ago
                                                </span>
                                                <a href="" class="notify-link">Xem</a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="notify-item p-2">
                                        <i class="fa fa-lightbulb d-flex align-items-center" aria-hidden="true"></i>
                                        <div class="notify-content">
                                            <p class="notify-title">Bạn tham gia thành công khóa học "My first course"
                                            </p>
                                            <div class="d-flex align-items-center">
                                                <span class="notify-time">
                                                    35 days 4 hours ago
                                                </span>
                                                <a href="" class="notify-link">Xem</a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="notify-item p-2">
                                        <i class="fa fa-lightbulb d-flex align-items-center" aria-hidden="true"></i>
                                        <div class="notify-content">
                                            <p class="notify-title">Bạn tham gia thành công khóa học "My first course"
                                            </p>
                                            <div class="d-flex align-items-center">
                                                <span class="notify-time">
                                                    35 days 4 hours ago
                                                </span>
                                                <a href="" class="notify-link">Xem</a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="notify-item p-2">
                                        <i class="fa fa-lightbulb d-flex align-items-center" aria-hidden="true"></i>
                                        <div class="notify-content">
                                            <p class="notify-title">Bạn tham gia thành công khóa học "My first course"
                                            </p>
                                            <div class="d-flex align-items-center">
                                                <span class="notify-time">
                                                    35 days 4 hours ago
                                                </span>
                                                <a href="" class="notify-link">Xem</a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="notify-item p-2">
                                        <i class="fa fa-lightbulb d-flex align-items-center" aria-hidden="true"></i>
                                        <div class="notify-content">
                                            <p class="notify-title">Bạn tham gia thành công khóa học "My first course"
                                            </p>
                                            <div class="d-flex align-items-center">
                                                <span class="notify-time">
                                                    35 days 4 hours ago
                                                </span>
                                                <a href="" class="notify-link">Xem</a>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                                <div class="dropdown-footer">
                                    <a href="" class="view-all p-1">Tất cả</a>
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="message">
                        <div id="message-icon">
                            <i class="fas fa-comment"></i>
                            <div class="alert-circle">2</div>
                        </div>
                    </div>
                    <div class="user-nav">
                        <div id="avata">
                            <div class="user-circle">
                                <img src="{{ asset($user->avata) }}" width="{{ $width }}"
                                    height="{{ $height }}">
                            </div>
                            <i class="fas fa-angle-down"></i>
                        </div>

                        <div class="user-dropdown">
                            <div class="user-menu">
                                <a class="menu-item" href="/user/profile">Thông tin cá nhân</a>
                                <a class="menu-item" href="/user/edit?type=password">Thay đổi mật khẩu</a>

                                <div class="divide-nav"></div>
                                <a class="menu-item" href="#">Tin nhắn</a>
                                <a class="menu-item" href="#">Thông báo</a>

                                @if (auth()->user()->role == 'teacher')
                                    <div class="divide-nav"></div>
                                    <p class="menu-text">Số dư: {{ number_format(auth()->user()->cash) }} VNĐ</p>
                                    <a class="menu-item" href="/user/withdraw">Rút tiền</a>
                                    <a class="menu-item" href="/user/bank">Tài khoản ngân hàng</a>
                                @endif

                                <div class="divide-nav"></div>
                                <a class="menu-item" href="/logout">
                                    Đăng xuất
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                @if (request()->is('course/*') && auth()->user()->role != 'student')
                    <div class="divide mr-3 ml-3"></div>
                    <form id="form-editmode" class="d-flex align-items-center">
                        @csrf
                        <p style="{{ session('edit') ? 'color:#0f6cbf' : '' }}">Chế độ sửa</p>
                        <label class="switch">
                            <input type="checkbox" onchange="toggleMode()" id="toggle-mode-cb" name="edit"
                                {{ session('edit') ? 'checked' : '' }}>
                            <span class="slider"></span>
                        </label>
                    </form>
                @endif
            @else
                <a href="/login">
                    Đăng nhập
                </a>
            @endif
        </div>
    </nav>
    @if (auth()->check())
        <div class="message-drawer">
            <div align="right">
                <button class="close-icon">
                    <i class="fa fa-times" aria-hidden="true"></i>
                </button>
            </div>
            <div class="message-header d-flex p-2 align-items-center">
                <div class="search-bar d-flex align-items-center w-100">
                    <input class=" form-control search-input p-2" type="text" name="conversation"
                        id="conversation" placeholder="Tìm kiếm hội thoại">
                    <i class="fa fa-search search-icon" aria-hidden="true"></i>
                </div>
                <a href="/message" class="setting-icon" style="margin-left:10px">
                    <i class="fa fa-cog"></i>
                </a>
            </div>
            <div class="message-body h-100">
                <div class="conversation-item p-2 =">
                    <div class="user-circle">
                        <img class="user-avata" src="{{ asset('image/avata.jpg') }}" alt="">
                    </div>
                    <div class="conversation-content d-flex flex-column w-100">
                        <h6 class="conversation-title">
                            Hoàng Minh Tâm
                        </h6>
                        <div class="conversation-message d-flex">
                            <span class="message-sender not-seen">You:</span>
                            <span class="last-message not-seen">Hello world!</span>
                            <span class="message-time">17:43</span>
                        </div>
                    </div>
                    <i class="fa fa-angle-right seen" aria-hidden="true"></i>
                </div>
                <div class="conversation-item p-2 =">
                    <div class="user-circle">
                        <img class="user-avata" src="{{ asset('image/avata.jpg') }}" alt="">
                    </div>
                    <div class="conversation-content d-flex flex-column w-100">
                        <h6 class="conversation-title">
                            Nhóm 7
                        </h6>
                        <div class="conversation-message d-flex">
                            <span class="message-sender seen">You:</span>
                            <div class="last-message seen">Hello world!Hello world!Hello world!Hello world!</div>
                            <span class="message-time">17:43</span>
                        </div>
                    </div>
                    <i class="fa fa-angle-right seen" aria-hidden="true"></i>
                </div>
            </div>
            <div class="dropdown-footer">
                <a href="" class="view-all p-1">Tất cả</a>
            </div>
        </div>
    @endif
</header>
