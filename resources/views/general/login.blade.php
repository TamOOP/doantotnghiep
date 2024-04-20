<!doctype html>
<html lang="en">

<head>
    <title>Đăng nhập</title>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <!-- Bootstrap CSS v5.2.1 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-iYQeCzEYFbKjA/T2uDLTpkwGzCiq6soy8tYaI1GyVh/UjpbCx/TYkiZhlZB6+fzT" crossorigin="anonymous">

    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>

<body style="background-color: #f0f2f5">
    <main>
        <div class="login-layout">
            <div class="login-container">
                <div class="login-content">
                    <h2 class="login-title">
                        Đăng nhập Learn 360
                    </h2>
                    <form class="login-form" method="post">
                        @csrf
                        <div>
                            <input class="form-control form-control-lg mt-3 login-input" type="text" name="username"
                                id="username" placeholder="Tài khoản">
                        </div>
                        <div>
                            <input class="form-control form-control-lg mt-3 login-input" type="password" name="password"
                                id="password" placeholder="Mật khẩu">
                        </div>
                        @include('layout.error', [
                            'style' =>
                                'margin-left: 0 !important; margin-right:0 !important; margin-bottom: 0 !important',
                        ])
                        <button class="login-button btn btn-primary btn-lg mt-3 mb-3">Đăng nhập</button>
                    </form>
                    <a class="forgot-password" href="">Quên mật khẩu</a>
                    <div class="login-divide mt-3"></div>
                    <a class="register" href="/register" style="text-decoration: none !important">
                        <button class="register-button btn btn-success mt-3 btn-lg">Tạo tài khoản mới</button>
                    </a>
                </div>
            </div>
        </div>
    </main>

    <script src="{{ asset('js/jquery.min.js') }}"></script>

    <script src="{{ asset('js/course/edit.js') }}"></script>
    <script src="{{ asset('js/general/login.js') }}"></script>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"
        integrity="sha384-oBqDVmMz9ATKxIep9tiCxS/Z9fNfEXiDAYTujMAeBAsjFuCZSmKbSSUnQlmh/jp3" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/js/bootstrap.min.js"
        integrity="sha384-7VPbUDkoPSGFnVtYi0QogXtr74QeVeeIs99Qfg5YCF+TidwNdjvaKZX19NZ/e6oz" crossorigin="anonymous">
    </script>
</body>

</html>
