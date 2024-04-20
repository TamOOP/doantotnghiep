<?php

namespace App\Http\Controllers;

use App\Helpers\AppHelper;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\SearchStudentRequest;
use App\Http\Requests\SearchUserRequest;
use App\Models\User;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdatePasswordRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\Enrolment;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Throwable;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::all()
            ->where('role', '!=', 'admin');

        foreach ($users as $user) {
            $imageSize = getimagesize(public_path($user->avata));
            $height = $imageSize[1];
            $width = $imageSize[0];
            $user->height = $height > $width ? 'auto' : '35';
            $user->width = $height < $width ? 'auto' : '35';
        }

        return view('admin.user', [
            'users' => $users
        ]);
    }

    public function login(LoginRequest $request)
    {
        $credential = [
            'username' => $request->username,
            'password' => $request->password,
            'status' => 1
        ];
        try {
            if (Auth::attempt($credential)) {
                return response()->json([
                    'success' => 'Đăng nhập thành công',
                    'redirect' => session('redirectLogin')
                ]);
            } else {
                session()->flash('error', 'Sai tài khoản hoặc mật khẩu');
                return response()->json(['error' => 'Sai tài khoản hoặc mật khẩu']);
            }
        } catch (Exception $e) {
            session()->flash('error', $e->getMessage());
            return response()->json(['error' => $e->getMessage()]);
        }
    }

    public function register(RegisterRequest $request)
    {
        $user = User::where('username', $request->username)->first();
        if (!is_null($user)) {
            session()->flash('error', 'Tài khoản đã tồn tại');
            return response()->json(['error' => 'Tài khoản đã tồn tại']);
        }
        $user = new User();

        $user->name = $request->name;
        $user->username = $request->username;
        $user->role = 'student';
        $user->password = Hash::make($request->password);
        $user->avata = 'image/defaultAvata.jpg';

        try {
            $user->save();
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()]);
        }

        return response()->json(['success' => 'Đăng ký thành công']);
    }

    public function logout()
    {
        Auth::logout();
        session(['edit' => false]);
        session(['redirectLogin' => null]);

        return redirect('/');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        return view('admin.user-add');
    }

    public function role()
    {
        $students = User::all()
            ->where('role', 'student')
            ->where('status', '1');

        $teachers = User::all()
            ->where('role', 'teacher')
            ->where('status', '1');

        return view('admin.user-role', [
            'students' => $students,
            'teachers' => $teachers
        ]);
    }

    public function changeRole(Request $request)
    {
        $user = User::find($request->id);

        $user->role = $request->role;

        try {
            $user->save();
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()]);
        }

        return response()->json(['success' => 'Thay đổi đã lưu']);
    }

    public function search(SearchUserRequest $request)
    {
        switch ($request->input('search-condition')) {
            case 'name':
                if ($request->has('search-keyword')) {
                    $users = $this->searchUserByName($request->input('search-keyword'));
                }
                break;

            case 'email':
                if ($request->has('search-keyword')) {
                    $users = $this->searchUserByEmail($request->input('search-keyword'));
                }
                break;
            case 'role':
                if ($request->has('role-option')) {
                    $users = $this->searchUserByRole($request->input('role-option'));
                }
                break;
            default:
                $users = $this->searchUserFullCondition($request->role, $request->keyword);
                break;
        }

        if ($users instanceof Throwable) {
            return response()->json(['error' => $users->getMessage()]);
        }

        if (!isset($users)) {
            $users = User::all()->where('status', '!=', '-1');
        }

        if (!is_null($users)) {
            foreach ($users as $user) {
                $imageSize = getimagesize(public_path($user->avata));
                $height = $imageSize[1];
                $width = $imageSize[0];
                $user->height = $height > $width ? 'auto' : '35';
                $user->width = $height < $width ? 'auto' : '35';
            }

            return response()->json([
                'success' => 'ok',
                'users' => $users
            ]);
        }

        return response()->json(['error' => 'Không tìm thấy người dùng']);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreUserRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreUserRequest $request)
    {
        $user = new User();

        $user->username = $request->username;
        $user->name = $request->name;
        $user->password = Hash::make($request->password);
        $user->role = $request->role;
        $user->description = $request->has('description') ? $request->description : null;
        $user->phone = $request->has('phone') ? $request->phone : null;
        $user->avata = $request->has('image') ? AppHelper::saveImageOnServerAndRename($request->file('image')) : 'image/defaultAvata.jpg';

        try {
            $user->save();
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()]);
        }

        return response()->json(['success' => 'Thêm thành công']);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show()
    {
        $user = auth()->user();
        switch ($user->role) {
            case 'student':
                $user->role = 'Học sinh';
                break;

            case 'teacher':
                $user->role = 'Giáo viên';
                break;

            case 'admin':
                $user->role = 'Admin';
                break;
        }

        $imageSize = getimagesize(public_path($user->avata));
        $height = $imageSize[1];
        $width = $imageSize[0];

        return view('user.profile', [
            'user' => $user,
            'height' => $height > $width ? 'auto' : '120',
            'width' => $height < $width ? 'auto' : '120',
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function edit()
    {
        $user = auth()->user();

        $imageSize = getimagesize(public_path($user->avata));
        $height = $imageSize[1];
        $width = $imageSize[0];

        return view('user.edit', [
            'user' => $user,
            'height' => $height > $width ? 'auto' : '120',
            'width' => $height < $width ? 'auto' : '120',
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateUserRequest  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateUserRequest $request)
    {
        $user = User::find(auth()->user()->id);

        $user->name = $request->name;
        $user->description = $request->description;
        $user->avata = $request->has('image') ? AppHelper::saveImageOnServerAndRename($request->file('image')) : $user->avata;
        $user->phone = $request->phone;

        try {
            $user->save();
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()]);
        }

        return response()->json(['success' => 'Sửa thành công']);
    }

    public function updatePassword(UpdatePasswordRequest $request)
    {
        $user = auth()->user();


        if (!Hash::check($request->oldPass, $user->password)) {
            return response()->json(['error' => 'Sai mật khẩu hiện tại']);
        }

        if ($request->newPass !== $request->newPassConfirm) {
            return response()->json(['error' => 'Mật khẩu mới và mật khẩu xác nhận không khớp']);
        }

        $user = User::find($user->id);
        $user->password = Hash::make($request->newPass);

        try {
            $user->save();
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()]);
        }

        return response()->json(['success' => 'Thay đổi mật khẩu thành công']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $user = User::find($request->id);

        $user->status = '-1';
        try {
            $user->save();
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()]);
        }

        return response()->json(['success' => 'Đã xóa người dùng ' . $user->name]);
    }

    public function suspend(Request $request)
    {
        $user = User::find($request->id);

        $user->status = '0';
        try {
            $user->save();
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()]);
        }

        return response()->json(['success' => 'Đã đình chỉ người dùng ' . $user->name]);
    }

    public function active(Request $request)
    {
        $user = User::find($request->id);

        $user->status = '1';
        try {
            $user->save();
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()]);
        }

        return response()->json(['success' => 'Đã kích hoạt người dùng ' . $user->name]);
    }

    protected function searchUserByName($keyword)
    {
        try {
            $users = User::where('name', 'like', '%' . $keyword . '%')
                ->where('status', '!=', '-1')
                ->where('role', '!=', 'admin')
                ->get();

            return $users;
        } catch (\Throwable $th) {
            return $th;
        }
    }

    protected function searchUserByEmail($keyword)
    {
        try {
            $users = User::where('username', 'like', '%' . $keyword . '%')
                ->where('status', '!=', '-1')
                ->where('role', '!=', 'admin')
                ->get();

            return $users;
        } catch (\Throwable $th) {
            return null;
        }
    }

    protected function searchUserByRole($role)
    {
        try {
            $users = User::where('role', $role)
                ->where('status', '!=', '-1')
                ->get();

            return $users;
        } catch (\Throwable $th) {
            return null;
        }
    }

    protected function searchUserFullCondition($role, $keyword)
    {
        try {
            $usersWithName = User::where('name', 'like', '%' . $keyword . '%')
                ->where('status', '1')
                ->where('role', $role)
                ->get();

            $usersWithEmail = User::where('username', 'like', '%' . $keyword . '%')
                ->where('status', '1')
                ->where('role', $role)
                ->get();
        } catch (\Throwable $th) {
            return $th;
        }

        $groupedUsers = $usersWithName->merge($usersWithEmail)->groupBy('id');

        $users = $groupedUsers->map(function ($group) {
            return $group->last();
        })->values()->all();

        return $users;
    }
}
