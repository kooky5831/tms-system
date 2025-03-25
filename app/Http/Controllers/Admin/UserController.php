<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserUpdateRequest;
use App\Http\Requests\AdminStoreRequest;
use App\Http\Requests\TrainerStoreRequest;
use App\Http\Requests\PasswordUpdateRequest;
use App\Services\UserService;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Gate;
use App\Services\CommonService;
use Illuminate\Http\Request;
use DataTables;

class UserController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(UserService $userService)
    {
        $this->middleware('auth');
        $this->userService = $userService;
    }

    /**
     * Show the admin users.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function adminUsers(Request $request)
    {
        if (! Gate::allows('manage-staff-users')) { return abort(403); }
        return view('admin.user.admin');
    }

    public function adminUsersDatatable(Request $request)
    {
        if (! Gate::allows('manage-staff-users')) { return abort(403); }
        $admins = $this->userService->getAllAdmins();
        return Datatables::of($admins)
                ->addIndexColumn()
                ->editColumn('status', function($row) {
                    if( $row->status ) { return '<span class="badge badge-soft-success">Active</span>'; }
                    else { return '<span class="badge badge-soft-danger">Inactive</span>'; }
                })
                ->filterColumn('status', function($query, $keyword) {
                    $len = strlen($keyword);
                    if( (substr('active', 0, $len) === strtolower($keyword)) ) {
                        $query->where('status', 1);
                    }
                    if( (substr('inactive', 0, $len) === strtolower($keyword)) ) {
                        $query->where('status', 0);
                    }
                })
                ->addColumn('action', function($row) {
                    $btn = '<a href="'.route('admin.admin.edit',$row->id).'" data-toggle="tooltip" data-placement="bottom" title="Edit" class="mr-2 edit-back"><i class="fas fa-pencil-alt text-info font-16"></i></a>';
                    // $btn .= '<a href="'.route('admin.admin.edit',$row->id).'" data-toggle="tooltip" data-placement="bottom" title="Delete" class="delete-back"><i class="far fa-trash-alt text-info font-16"></i></a>';
                    return $btn;
                })
                ->rawColumns(['action','status'])
                ->make(true);
    }

    public function adminAdd(AdminStoreRequest $request)
    {
        if (! Gate::allows('manage-staff-users')) { return abort(403); }
        if( $request->method() == 'POST') {
            $admin = $this->userService->registerAdmin($request);
            if( $admin ) {
                setflashmsg(trans('msg.adminCreated'), 1);
                return redirect()->route('admin.user.admin');
            }
        }
        $timezones = CommonService::timezoneList();
        return view('admin.user.admin-add', compact('timezones'));
    }

    public function adminEdit($id, AdminStoreRequest $request)
    {
        if (! Gate::allows('manage-staff-users')) { return abort(403); }
        if( $request->method() == 'POST') {
            $admin = $this->userService->updateAdmin($id, $request);
            if( $admin ) {
                setflashmsg(trans('msg.adminUpdated'), 1);
                return redirect()->route('admin.user.admin');
            }
        }
        $data = $this->userService->getUserById($id);
        $timezones = CommonService::timezoneList();
        return view('admin.user.admin-edit', compact('data', 'timezones'));
    }

    // Super Admin Users
    /**
     * Show the admin users.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function superadminUsers(Request $request)
    {
        if (\Auth::user()->role != 'superadmin') { return abort(403); }
        return view('admin.user.superadmin.admin');
    }

    public function superadminUsersDatatable(Request $request)
    {
        if (\Auth::user()->role != 'superadmin') { return abort(403); }
        $admins = $this->userService->getAllSuperAdmins();
        return Datatables::of($admins)
                ->addIndexColumn()
                ->editColumn('status', function($row) {
                    if( $row->status ) { return '<span class="badge badge-soft-success">Active</span>'; }
                    else { return '<span class="badge badge-soft-danger">Inactive</span>'; }
                })
                ->filterColumn('status', function($query, $keyword) {
                    $len = strlen($keyword);
                    if( (substr('active', 0, $len) === strtolower($keyword)) ) {
                        $query->where('status', 1);
                    }
                    if( (substr('inactive', 0, $len) === strtolower($keyword)) ) {
                        $query->where('status', 0);
                    }
                })
                ->addColumn('action', function($row) {
                    $btn = '<a href="'.route('admin.user.superadmin.edit',$row->id).'" data-toggle="tooltip" data-placement="bottom" title="Edit" class="mr-2 edit-back"><i class="fas fa-pencil-alt text-info font-16"></i></a>';
                    // $btn .= '<a href="'.route('admin.admin.edit',$row->id).'" data-toggle="tooltip" data-placement="bottom" title="Delete" class="delete-back"><i class="far fa-trash-alt text-info font-16"></i></a>';
                    return $btn;
                })
                ->rawColumns(['action','status'])
                ->make(true);
    }

    public function superadminAdd(AdminStoreRequest $request)
    {
        if (\Auth::user()->role != 'superadmin') { return abort(403); }
        if( $request->method() == 'POST') {
            $admin = $this->userService->registerSuperAdmin($request);
            if( $admin ) {
                setflashmsg(trans('msg.superadminCreated'), 1);
                return redirect()->route('admin.user.superadmin');
            }
        }
        $timezones = CommonService::timezoneList();
        return view('admin.user.superadmin.admin-add', compact('timezones'));
    }

    public function superadminEdit($id, AdminStoreRequest $request)
    {
        if (\Auth::user()->role != 'superadmin') { return abort(403); }
        if( $request->method() == 'POST') {
            $admin = $this->userService->updateAdmin($id, $request);
            if( $admin ) {
                setflashmsg(trans('msg.superadminUpdated'), 1);
                return redirect()->route('admin.user.superadmin');
            }
        }
        $data = $this->userService->getUserById($id);
        $timezones = CommonService::timezoneList();
        return view('admin.user.superadmin.admin-edit', compact('data', 'timezones'));
    }

    public function profile(UserUpdateRequest $request)
    {
        if( $request->method() == 'POST') {
            $user = $this->userService->updateProfile($request);
            if( $user ) {
                setflashmsg(trans('msg.profileUpdated'), 1);
                return redirect()->route('admin.profile');
            }
        }
        $timezones = CommonService::timezoneList();
        return view('admin.profile', compact('timezones'));
    }

    public function trainerUsers(Request $request)
    {
        if (! Gate::allows('trainer-list')) { return abort(403); }
        return view('admin.user.trainer');
    }

    public function trainerUsersDatatable(Request $request)
    {
        if (! Gate::allows('trainer-list')) { return abort(403); }
        $trainers = $this->userService->getAllTrainers();
        return Datatables::of($trainers)
                ->addIndexColumn()
                ->editColumn('created_at', function($row) {
                    return $row->registrationDate;
                })
                ->editColumn('status', function($row) {
                    if( $row->status ) { return '<span class="badge badge-soft-success">Active</span>'; }
                    else { return '<span class="badge badge-soft-danger">Inactive</span>'; }
                })
                ->filterColumn('status', function($query, $keyword) {
                    $len = strlen($keyword);
                    if( (substr('active', 0, $len) === strtolower($keyword)) ) {
                        $query->where('status', 1);
                    }
                    if( (substr('inactive', 0, $len) === strtolower($keyword)) ) {
                        $query->where('status', 0);
                    }
                })
                ->addColumn('action', function($row) {
                    if (! Gate::allows('trainer-edit')) {
                        $btn = "";
                    } else {
                        $btn = '<a href="'.route('admin.trainer.edit',$row->id).'" data-toggle="tooltip" data-placement="bottom" title="Edit" class="mr-2 edit-back"><i class="fas fa-pencil-alt text-info font-16"></i></a>';
                        // $btn .= '<a href="'.route('admin.trainer.edit',$row->id).'" data-toggle="tooltip" data-placement="bottom" title="Delete" class="delete-back"><i class="far fa-trash-alt text-info font-16"></i></a>';
                    }
                    return $btn;
                })
                ->rawColumns(['action','status'])
                ->make(true);
    }

    public function trainerAdd(TrainerStoreRequest $request)
    {
        if (! Gate::allows('trainer-add')) { return abort(403); }
        if( $request->method() == 'POST') {
            $trainer = $this->userService->registerTrainer($request);
            if( $trainer ) {
                setflashmsg(trans('msg.trainerCreated'), 1);
                return redirect()->route('admin.user.trainer');
            }
        }
        $timezones = CommonService::timezoneList();
        $qualificationsList = CommonService::trainerQualificationsList();
        return view('admin.user.trainer-add', compact('timezones', 'qualificationsList'));
    }

    public function trainerEdit($id, TrainerStoreRequest $request)
    {
        if (! Gate::allows('trainer-edit')) { return abort(403); }
        if( $request->method() == 'POST') {
            $trainer = $this->userService->updateTrainer($id, $request);
            if( $trainer ) {
                //setflashmsg(trans('msg.trainerUpdated'), 1);
                return redirect()->route('admin.user.trainer');
            }
        }
        $data = $this->userService->getUserByIdWithTrainer($id);
        $timezones = CommonService::timezoneList();
        $qualificationsList = CommonService::trainerQualificationsList();
        return view('admin.user.trainer-edit', compact('data', 'timezones', 'qualificationsList'));
    }

    public function trainerView($id)
    {
        if (! Gate::allows('trainer-view')) { return abort(403); }
        dd('pending');
        $doctor = $this->doctorService->getDoctorById($id);
        return view('backend.doctors.view', compact('doctor'));
    }

    public function adminPermission()
    {
        if ( \Auth::user()->role != 'superadmin' ) { return abort(403); }
        $role = Role::where('name', 'staff')->first();
        $permissions = Permission::get()->pluck('name', 'name');
        $userpermissions = $role->permissions()->pluck('name')->toArray();
        return view('admin.permissions.admin', compact('role', 'permissions','userpermissions'));
    }

    public function adminPermissionPost(Request $request, Role $role)
    {
        $role = Role::where('name', 'staff')->first();
        // $role->update($request->except('permission'));
        $permissions = $request->input('permission') ? $request->input('permission') : [];
        $role->syncPermissions($permissions);
        setflashmsg(trans('msg.permissionUpdated'), 1);
        return redirect()->route('admin.user.admin.permission');
    }

    public function changePassword(PasswordUpdateRequest $request)
    {
        if( $request->method() == 'POST') {
            $data = $this->userService->updatePassword($request);
            if( !$data['success'] ) {
                setflashmsg($data['message'], 0);
            } else {
                setflashmsg($data['message'], 1);
            }
            return redirect()->route('admin.changepassword');
        }
        return view('admin.user.changepassword');
    }

    public function getTrainerResponseView(Request $request)
    {
        $trainer_id = $request->get('id');
        $records = $this->userService->getUserByIdWithTrainer($trainer_id);
        $view = view('admin.partial.trainer-response-view', compact('records'))->render();
        $data = [ 'status' => true, 'html' => $view ];
        return response()->json($data);
    }

}
