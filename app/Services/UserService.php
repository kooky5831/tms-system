<?php

namespace App\Services;

use Auth;
use App\Models\User;
use App\Models\Trainer;
use App\Services\CommonService;
use App\Services\TPGatewayService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use App\Notifications\AccountCreationEmail;

class UserService
{
    protected $user_model;

    public function __construct()
    {
        $this->user_model = new User;
    }

    public function getAllAdmins()
    {
        return $this->user_model->select()->where('role', 'staff');
    }

    public function getAllSuperAdmins()
    {
        return $this->user_model->select()->where('role', 'superadmin')->orderBy('created_at', 'desc');
    }

    public function getAllTrainers()
    {
        return $this->user_model->select()->where('role', 'trainer')->orderBy('created_at', 'desc');;
    }

    public function getAllTrainersList()
    {
        return $this->user_model->userTrainer()->active()->get();
    }

    public function getUserById($id)
    {
        return $this->user_model->find($id);
    }

    public function getUserByIdWithTrainer($id)
    {
        return $this->user_model->with(['trainer'])->find($id);
    }

    public function getTrainerByUserId($id)
    {
        return Trainer::where('user_id', $id)->first();
    }

    public function registerAdmin($request)
    {
        $admin = $this->user_model;
        $admin->name = $request->get('name');
        $admin->email = $request->get('email');
        $admin->phone_number = $request->get('phone_number');
        $admin->role = 'staff';
        $userPwd = generateRandomString(6);
        $admin->password = Hash::make($userPwd);
        $admin->timezone = $request->get('timezone');
        $admin->status = $request->has('status') ? 1 : 0;
        if( $request->hasfile('profile_avatar') ) {
            // upload file
            $uploadedFile = $request->file('profile_avatar');
            $filename = "staff_".rand(1000,9999)."_".time().".".$uploadedFile->getClientOriginalExtension();

            Storage::disk('public_user_uploads_storage')->putFileAs(
                config('uploadpath.user_profile_storage'),
                $uploadedFile,
                $filename
            );
            $admin->profile_avatar = $filename;
        } else {
            $admin->profile_avatar = 'admin_default.png';
        }

        $admin->save();
        // assign admin role to newly created user to give permission
        $admin->assignRole('staff');
        $admin->notify(new AccountCreationEmail($admin, $userPwd));
        return $admin;
    }

    public function registerSuperAdmin($request)
    {
        $admin = $this->user_model;
        $admin->name = $request->get('name');
        $admin->email = $request->get('email');
        $admin->username = $request->get('username');
        $admin->phone_number = $request->get('phone_number');
        $admin->role = 'superadmin';
        $userPwd = generateRandomString(6);
        $admin->password = Hash::make($userPwd);
        $admin->timezone = $request->get('timezone');
        $admin->status = $request->has('status') ? 1 : 0;
        if( $request->hasfile('profile_avatar') ) {
            // upload file
            $uploadedFile = $request->file('profile_avatar');
            $filename = "staff_".rand(1000,9999)."_".time().".".$uploadedFile->getClientOriginalExtension();

            Storage::disk('public_user_uploads_storage')->putFileAs(
                config('uploadpath.user_profile_storage'),
                $uploadedFile,
                $filename
            );
            $admin->profile_avatar = $filename;
        } else {
            $admin->profile_avatar = 'admin_default.png';
        }

        $admin->save();
        // assign admin role to newly created user to give permission
        $admin->assignRole('superadmin');
        $admin->notify(new AccountCreationEmail($admin, $userPwd));
        return $admin;
    }

    public function updateAdmin($id, $request)
    {
        $admin = $this->getUserById($id);
        if( $admin ) {
            $admin->name = $request->get('name');
            $admin->username = $request->get('username');
            $admin->phone_number = $request->get('phone_number');
            $admin->timezone = $request->get('timezone');
            $admin->status = $request->has('status') ? 1 : 0;
            if( $request->hasfile('profile_avatar') ) {
                // upload file
                $uploadedFile = $request->file('profile_avatar');
                $filename = "staff_".rand(1000,9999)."_".time().".".$uploadedFile->getClientOriginalExtension();

                Storage::disk('public_user_uploads_storage')->putFileAs(
                    config('uploadpath.user_profile_storage'),
                    $uploadedFile,
                    $filename
                );
                $admin->profile_avatar = $filename;
            }

            $admin->save();
            return $admin;
        }
        return false;
    }

    public function registerTrainer($request)
    {
        $record = $this->user_model;
        $trainerName = $request->get('name');
        $record->name = $request->get('name');
        $record->email = $request->get('email');
        $record->phone_number = $request->get('phone_number');
        $record->username = $request->get('id_number');
        $record->role = 'trainer';  
        $trainerFirstName = explode(' ', $trainerName);
        $userPwd = $trainerFirstName[0];
        $record->password = Hash::make($userPwd."!99");
        $record->timezone = $request->get('timezone');
        $record->status = $request->has('status') ? 1 : 0;
        if( $request->hasfile('profile_avatar') ) {
            // upload file
            $uploadedFile = $request->file('profile_avatar');
            $filename = "trainer_".rand(1000,9999)."_".time().".".$uploadedFile->getClientOriginalExtension();

            Storage::disk('public_user_uploads_storage')->putFileAs(
                config('uploadpath.user_profile_storage'),
                $uploadedFile,
                $filename
            );
            $record->profile_avatar = $filename;
        } else {
            $record->profile_avatar = 'default.jpg';
        }

        // Store Trainer Signature Start
        if( $request->hasfile('trainer_signature') ) {
            // upload file
            $uploadedFile = $request->file('trainer_signature');
            $filename = "trainer_".rand(1000,9999)."_".time().".".$uploadedFile->getClientOriginalExtension();

            Storage::disk('public_user_uploads_storage')->putFileAs(
                config('uploadpath.trainer_sign_storage'),
                $uploadedFile,
                $filename
            );
            $record->trainer_signature = $filename;
        } else {
            $record->trainer_signature = 'default.jpg';
        }
        // Store Trainer Signature End

        $record->save();
        // assign trainer role to newly created user to give permission
        $record->assignRole('trainer');
        //$record->notify(new AccountCreationEmail($record, $userPwd));
        // add to trainer
        $trainer = new Trainer;
        $trainer->user_id               = $record->id;
        // $trainer->type                  = $request->get('type');
        $trainer->experience            = $request->get('experience');
        $trainer->linkedInURL           = $request->get('linkedInURL');
        $trainer->salutationId          = $request->get('salutationId');
        $trainer->qualifications        = json_encode($request->get('qualifications'));
        $trainer->domainAreaOfPractice  = $request->get('domainArea');
        $trainer->created_by            = Auth::Id();
        $trainer->updated_by            = Auth::Id();

        /* New Fields Start */
        $trainer->id_number = $request->get('id_number');
        $trainer->id_type        = $request->get('id_type');
        $trainer->role_type        = json_encode($request->get('role_type'));
        /* New Fields End */

        $trainer->save();

        // add to TP Gateway

        //Encode profile avatar
        // $full_profile_image_path = (file_exists( public_path('/').config('uploadpath.user_profile')."/". $record->profile_avatar)) ? public_path('/').config('uploadpath.user_profile')."/".$record->profile_avatar : public_path('/').config('uploadpath.user_profile')."/default.jpg" ;

        $full_profile_image_path = (file_exists(Storage::path('public/users/') . $record->profile_avatar)) 
                                        ? Storage::path('public/users/') . $record->profile_avatar
                                        : Storage::path('public/users/') . "default.jpg";

        $profile_image = file_get_contents($full_profile_image_path);
        $profile_image_content = base64_encode($profile_image);

        $req_data = [];
        $req_data['trainer']['name'] = $record->name;
        $req_data['trainer']['email'] = $record->email;
        // $req_data['trainer']['type']['code'] = $trainer->type;
        $req_data['trainer']['photo']['name'] = $record->profile_avatar;
        $req_data['trainer']['photo']['content'] = $profile_image_content;
        $req_data['trainer']['experience'] = $trainer->experience;
        $req_data['trainer']['linkedInURL'] = $trainer->linkedInURL;
        $req_data['trainer']['salutationId'] = $trainer->salutationId;
        $qual = [];
        $qualfy = $request->get('qualifications');
        if( count($qualfy) ) {
            foreach ($qualfy as $key => $qualification) {
                $qual[$key]['level']['code'] = $qualification['level'];
                $qual[$key]['description'] = $qualification['description'];
            }
        }
        $req_data['trainer']['qualifications'] = $qual;
        $req_data['trainer']['domainAreaOfPractice'] = $trainer->domainAreaOfPractice;

        /* New Fields Data Start */
        
        $type = $request->get('id_type');
        $type_desc = getTrainerIdType($type);

        $req_data['trainer']['idNumber'] = $trainer->id_number;

        $req_data['trainer']['idType']['code'] = $type;
        $req_data['trainer']['idType']['description'] = $type_desc;
        
        
        $roles = [];
        $role = $request->get('role_type');
        if( count($role) ) {
            foreach ($role as $key => $r) {
                $roles[$key]['role']['id'] = $r;
                $roles[$key]['role']['description'] =getTrainerRoles($r);
            }
        }

        $req_data['trainer']['roles'] = $roles;
        /* New Fields Data End */
        $tpgatewayReq = new TPGatewayService;

        $trainerRes = $tpgatewayReq->addTrainerToTpGateway($req_data);
        $trainer->tpgResponse = json_encode($trainerRes);
        $trainer->save();
        if( isset($trainerRes->status) && $trainerRes->status == 200 ) {
            $trainer->tpgateway_id = $trainerRes->data->trainer->id;
            $trainer->save();
        }
        return $record;
    }

    public function updateTrainer($id, $request)
    {
        $record = $this->getUserById($id);
        if( $record ) {
            $trainerName = $request->get('name');
            $record->name = $trainerName;
            $record->phone_number = $request->get('phone_number');
            $record->username = $request->get('id_number');
            $record->timezone = $request->get('timezone');
            $record->status = $request->has('status') ? 1 : 0;
            $trainerFirstName = explode(' ', $trainerName);
            $userPwd = $trainerFirstName[0];
            $record->password = Hash::make($userPwd."!99");
            if( $request->hasfile('profile_avatar') ) {
                // upload file
                $uploadedFile = $request->file('profile_avatar');
                $filename = "trainer_".rand(1000,9999)."_".time().".".$uploadedFile->getClientOriginalExtension();

                Storage::disk('public_user_uploads_storage')->putFileAs(
                    config('uploadpath.user_profile_storage'),
                    $uploadedFile,
                    $filename
                );
                $record->profile_avatar = $filename;
            }

            // Update trainer Signature start
            if( $request->hasfile('trainer_signature') ) {
                // upload file
                $uploadedFile = $request->file('trainer_signature');
                $filename = "trainer_".rand(1000,9999)."_".time().".".$uploadedFile->getClientOriginalExtension();

                Storage::disk('public_user_uploads_storage')->putFileAs(
                    config('uploadpath.trainer_sign_storage'),
                    $uploadedFile,
                    $filename
                );
                $record->trainer_signature = $filename;
            }
            // Update trainer signature end

            $record->save();
            // update to trainer
            $trainer = $this->getTrainerByUserId($record->id);
            // $trainer->type                  = $request->get('type');
            $trainer->experience            = $request->get('experience');
            $trainer->linkedInURL           = $request->get('linkedInURL');
            $trainer->salutationId          = $request->get('salutationId');
            $trainer->qualifications        = json_encode($request->get('qualifications'));
            $trainer->domainAreaOfPractice  = $request->get('domainArea');
            $trainer->updated_by            = Auth::Id();

            /* New Fields Start */
            $trainer->id_number = $request->get('id_number');
            $trainer->id_type        = $request->get('id_type');
            $trainer->role_type        = json_encode($request->get('role_type'));
            /* New Fields End */


            // add to TP Gateway
            $req_data = [];
            
            // Encode profile avatar
            
            
            // $full_profile_image_path = (file_exists( public_path('/').config('uploadpath.user_profile')."/". $record->profile_avatar)) ? public_path('/').config('uploadpath.user_profile')."/".$record->profile_avatar : public_path('/').config('uploadpath.user_profile')."/default.jpg" ;
            $full_profile_image_path = (file_exists(Storage::path('public/users/') . $record->profile_avatar)) 
                                        ? Storage::path('public/users/') . $record->profile_avatar
                                        : Storage::path('public/users/') . "default.jpg";
            
            $profile_image = file_get_contents($full_profile_image_path);
            $profile_image_content = base64_encode($profile_image);

            $req_data['trainer']['name'] = $record->name;
            $req_data['trainer']['email'] = $record->email;
            $req_data['trainer']['experience'] = $trainer->experience;
            $req_data['trainer']['photo']['name'] = $record->profile_avatar;
            $req_data['trainer']['photo']['content'] = $profile_image_content;
            $req_data['trainer']['linkedInURL'] = $trainer->linkedInURL;
            $req_data['trainer']['salutationId'] = $trainer->salutationId;
            $qual = [];
            $qualfy = $request->get('qualifications');
            if( count($qualfy) ) {
                foreach ($qualfy as $key => $qualification) {
                    $qual[$key]['level']['code'] = $qualification['level'];
                    $qual[$key]['description'] = $qualification['description'];
                }
            }
            $req_data['trainer']['qualifications'] = $qual;
            $req_data['trainer']['domainAreaOfPractice'] = $trainer->domainAreaOfPractice;

            /* New Fields Data Start */
        
            $type = $request->get('id_type');
            $type_desc = getTrainerIdType($type);

            $req_data['trainer']['idNumber'] = $trainer->id_number;

            $req_data['trainer']['idType']['code'] = $type;
            $req_data['trainer']['idType']['description'] = $type_desc;
            
            
            $roles = [];
            $role = $request->get('role_type');
            if( count($role) ) {
                foreach ($role as $key => $r) {
                    $roles[$key]['role']['id'] = $r;
                    $roles[$key]['role']['description'] =getTrainerRoles($r);
                }
            }

            $req_data['trainer']['roles'] = $roles;
            /* New Fields Data End */
            $tpgatewayReq = new TPGatewayService;
            $trainer_id = $trainer->tpgateway_id;

            if(isset($trainer_id) && !empty($trainer_id)){
                $req_data['trainer']['action'] = "update";
                $trainerRes = $tpgatewayReq->updateTrainerToTpGateway($trainer_id, $req_data);
                $trainer->tpgResponse = json_encode($trainerRes);
                $trainer->save();
            }
            else{
                $trainerRes = $tpgatewayReq->addTrainerToTpGateway($req_data);
                $trainer->tpgResponse = json_encode($trainerRes);
                $trainer->save();
                if( isset($trainerRes->status) && $trainerRes->status == 200 ) {
                    $trainer->tpgateway_id = $trainerRes->data->trainer->id;
                    $trainer->save();
                }
            }

            if( isset($trainerRes->status) && $trainerRes->status == 200 ) {
                setflashmsg(trans('msg.trainerSyncUpdated'), 1);
            }
            else{
                setflashmsg(trans('msg.trainerUpdatedNotSync'), 0);
            }
            
            return $record;
        }
        return false;
    }

    public function updateProfile($request)
    {
        $user = $this->user_model->find(Auth::id());
        $user->name         = $request->get('name');
        $user->phone_number = $request->get('phone_number');
        $user->address      = $request->get('address', NULL);
        $user->timezone     = $request->get('timezone');
        if( $request->hasfile('profile_avatar') ) {
            // upload file
            $uploadedFile = $request->file('profile_avatar');
            $filename = "admin_".rand(1000,9999)."_".time().".".$uploadedFile->getClientOriginalExtension();

            Storage::disk('public_user_uploads')->putFileAs(
                config('uploadpath.user_profile'),
                $uploadedFile,
                $filename
            );
            $user->profile_avatar = $filename;
        }
        $user->save();
        return true;
    }

    public function updateTrainerProfile($request)
    {
        $user = $this->user_model->find(Auth::id());
        $user->name         = $request->get('name');
        $user->phone_number = $request->get('phone_number');
        $user->address      = $request->get('address', NULL);
        $user->timezone     = $request->get('timezone');
        if( $request->hasfile('profile_avatar') ) {
            // upload file
            $uploadedFile = $request->file('profile_avatar');
            $filename = "trainer_".rand(1000,9999)."_".time().".".$uploadedFile->getClientOriginalExtension();

            Storage::disk('public_user_uploads')->putFileAs(
                config('uploadpath.user_profile'),
                $uploadedFile,
                $filename
            );
            $user->profile_avatar = $filename;
        }
        $user->save();
        return true;
    }

    public function updatePassword($request)
    {
        if (!(Hash::check($request->get('old_password'), Auth::user()->password))) {
            // The passwords matches
            $data = [ 'success' => false, 'message' => trans('msg.pwdNotMatch') ];
            $data['response'] = new \stdClass;
            return $data;
        }

        if(strcmp($request->get('old_password'), $request->get('new_password')) == 0){
            //Current password and new password are same
            $data = [ 'success' => false, 'message' => trans('msg.pwdSame') ];
            $data['response'] = new \stdClass;
            return $data;
        }

        //Change Password
        $user = Auth::user();
        $user->password = Hash::make( $request->get('new_password') );
        $user->save();

        // now update the password for login user
        if( $user ) {
            $data = [ 'success' => true, 'message' => trans('msg.pwdUpdate') ];
        } else {
            $data = [ 'success' => false, 'message' => trans('msg.someError') ];
        }
        $data['response'] = new \stdClass;
        return $data;
    }

}
