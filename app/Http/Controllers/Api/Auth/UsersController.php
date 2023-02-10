<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Auth\AdminRequest;
use App\Http\Requests\Api\Auth\ChangePasswordRequest;
use App\Http\Requests\Api\Auth\RegisterRequest;
use App\Http\Requests\Api\Auth\SetPasswordRequest;
use App\Http\Requests\Api\Auth\StoreRequest;
use App\Http\Requests\Api\Auth\UpdateRequest;
use App\Models\Access\User\User;
use App\Repositories\Frontend\Access\User\UserRepository;
use Dingo\Api\Routing\Helpers;
use Illuminate\Http\Request;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class UsersController extends Controller
{
    use Helpers;

    /**
     * @var UserRepository
     */
    protected $user;

    /**
     * ChangePasswordController constructor.
     *
     * @param UserRepository $user
     */
    public function __construct(UserRepository $user)
    {
        $this->user = $user;
    }


    public function index(AdminRequest $request)
    {
        $page_size = $request->get('page_size');
        $current_page = $request->get('page');
        $search = $request->get('search');
        $role = $request->get('role');
        $roles = $request->get('roles');
        $confirmed = $request->get('confirmed');
        $departments = $request->get('departments');
        $query = User::query()->with("department")->orderBy('created_at', 'DESC');
        if (isset($role)) {
            $query->whereHas('roles', function ($q) use ($role) {
                if ($role === 2 || $role === '2') {
                    $q->whereIn('role_id', [2, 4]);
                } else {
                    $q->where('role_id', $role);
                }
            });
        }

        if (isset($roles)) {
            $query->whereHas('roles', function ($q) use ($roles) {
                $q->whereIn('role_id', $roles);
            });
        }

        if (isset($confirmed)) {
            $query->where('confirmed', '=', $confirmed);
        }

        if (isset($search)) {
            $query->where(function ($q) use ($search) {
                $q->orWhere('name', 'iLike', '%' . $search . '%')
                    ->orWhere('mobile', 'iLike', '%' . $search . '%')
                    ->orWhere('email', 'iLike', '%' . $search . '%');
            });
        }
        if (isset($departments)) {
            $query->whereIn('department_id', $departments);
        }
        $query->with('roles');
        return response()->json($query->paginate($page_size, ['*'], 'page', $current_page), 200);
    }

    public function store(StoreRequest $request)
    {
        $user = $this->user->create($request, true);
        $user->department;
        if ($user) {
            return response()->json([
                'status_code' => 200,
                'message' => 'Account have been created.',
                'data' => $user
            ]);
        }
        return $this->response->errorBadRequest();
    }

    public function register(RegisterRequest $request)
    {
        if ($this->user->create($request, false)) {
            return response()->json([
                'status_code' => 200,
                'message' => 'Your account have been created. Please contact with admin to active your account',
            ]);
        }
        return $this->response->errorBadRequest();
    }

    public function changePassword(ChangePasswordRequest $request)
    {
        $this->user->changePassword($request->only('old_password', 'password'));

        return response()->json([
            'status_code' => 200,
            'message' => trans('Change password success')
        ]);
    }

    //ADMIN RULE
    public function update(UpdateRequest $request, $id)
    {
        $user = User::query()->findOrFail($id);
        $user->update($request->only(['name', 'mobile', 'email', 'department_id', 'district']));
        $user->save();
        $department = $user->department;
        if ($user) {
            return response()->json([
                'status_code' => 200,
                'message' => 'User updated',
                'data' => $user
            ]);
        }
    }

    //ADMIN RULE
    public function approve($cf_code, AdminRequest $request)
    {
        $this->user->confirmAccount($cf_code);
        return response()->json([
            'status_code' => 200,
            'message' => 'Account have been approved',
        ]);
    }

    //ADMIN RULE
    public function deleteUser($id, AdminRequest $request)
    {
        $user = User::query()->findOrFail($id);
        User::destroy($id);
        return response()->json([
            'status_code' => 200,
            'message' => 'Account have been deleted',
            'data' => $user
        ]);
    }

    //ADMIN RULE
    public function setPasswordUser($id, SetPasswordRequest $request)
    {
        $user = User::query()->findOrFail($id);
        $user->password = bcrypt($request->get('password'));
        $user->save();

        return response()->json([
            'status_code' => 200,
            'message' => 'Password had been updated',
            'data' => $user
        ]);
    }
}
