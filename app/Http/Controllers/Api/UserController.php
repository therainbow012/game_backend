<?php

namespace App\Http\Controllers\Api;

use Lang;
use App\Models\User;
use App\Models\Wallet;
use App\Helpers\Helper;
use App\Models\AdminAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Api\BaseController as BaseController;

class UserController extends BaseController
{
    public function register(Request $request) {

        $validator = Validator::make($request->all(),
        [
            'first_name' => 'required',
            'last_name' => 'required',
            'username' => 'required|unique:users',
            'email' => 'required|email|unique:users',
            'password' => 'required',
            'mobile_number' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors()->first());
        }

        $userDetails = $request->all();

        $maxId = User::withTrashed()->max('reference_code');
        $referenceId = empty($maxId) ? '1001' : $maxId + 1;

        $userDetails['reference_code'] = $referenceId;
        $userDetails['password'] = Hash::make($userDetails['password']);


        // Check if an image already exists and delete it
        if($request->has('image')) {
            // Store the new image and save image information to the database
            $uploadedFile = $request->file('image');
            $newName = time() . '.' . $uploadedFile->getClientOriginalExtension();
            $uploadedFile->storeAs('users/', $newName, 'public');
            $userDetails['image'] = $newName;
        }

        // Create new user
        $user = User::create($userDetails);
        $success = $user;
        $success['token'] = $user->createToken(env('APP_NAME'))->accessToken;

        return $this->sendResponse($success, Lang::get('messages.USER_REGISTERED_SUCCESSFULLY_MSG'));
    }

    public function login(Request $request)
    {
        $token = $request->username;

        try
        {
            $validator = Validator::make($request->all(),
            [
                'password' => 'required',
                'username' => 'required'
            ]);

            if ($validator->fails()) {
                return $this->sendError($validator->errors()->first());
            }

            $isUserExists = User::where('username', $token)->count();

            if ($isUserExists === 0) {

                return $this->sendError(Lang::get('messages.USER_NOT_EXISTS_IN_SYSTEM'), [] ,201);
            }

            $userDetails = null;

            // Check login user is social media user or not
            if (Auth::attempt(['username' => $request->username, 'password' => $request->password, 'role' => config('enums.USER_TYPE')['USER'] ])) {

                $userDetails = Auth::user();
            }

            if ($userDetails) {
                    $success = $userDetails;
                    // To revoke all the existing token of specific user
                    Helper::revokeUserTokens($userDetails->id);
                    $success['token'] = $userDetails->createToken(env('APP_NAME'))->accessToken;
                    return $this->sendResponse($success, Lang::get('messages.USER_LOGIN_SUCCESSFULLY_MSG'));
            }
            return $this->sendError(Lang::get('messages.INVALID_USERNAME_OR_PASSOWRD'), [], 201);
        }
        catch (\Exception $ex)
        {
            return $this->sendError(Lang::get('messages.SOMTHING_WENT_WRONG'), []);
        }
    }

    public function userList(Request $request) {
        $userData = User::all();

        if ($userData) {
            $success = $userData;
            return $this->sendResponse($success, Lang::get('messages.RECORD_FOUND'));
        } else {
            return $this->sendError(Lang::get('messages.SOMETHING_WENT_WRONG_MSG'), []);
        }
    }

    public function userEdit(Request $request) {
        try {

            $validator = Validator::make($request->all(),
            [
                'id' => 'required',
                'first_name' => 'required',
                'last_name' => 'required',
                'username' => 'required|unique:users,username,'.$request->id,
                'email' => 'required|email|unique:users,email,'.$request->id,
                'mobile_number' => 'required',
                'reference_code' => 'nullable'
            ]);

            if ($validator->fails()) {
                return $this->sendError($validator->errors()->first());
            }

            // Trim all the request data.
            $input = array_map('trim', $request->all());

            if(!empty($request->reference_code)) {
                $user = User::where('reference_code', $request->reference_code)->first();
                if($user) {
                    $wallet = Wallet::where('user_id', $user->id)->first();
                    if($wallet) {
                        $wallet->update(['amount' => $wallet->amount + config('enums.REFERECE_CODE')]);
                    } else {
                        Wallet::create([
                            'user_id' => $request->user_id,
                            'amount' => config('enums.REFERECE_CODE'),
                            'payment_mode' => null,
                            'user_payment_id' => null,
                        ]);
                    }
                }
            }


            // Update user data
            $userDetails = Auth::user();

            // Check if an image already exists and delete it
            if($request->has('image')) {
                // Store the new image and save image information to the database
                $uploadedFile = $request->file('image');
                $newName = time() . '.' . $uploadedFile->getClientOriginalExtension();
                $uploadedFile->storeAs('users/', $newName, 'public');
                $userDetails->image = $newName;
            }

            $userDetails->first_name =$input['first_name'];
            $userDetails->last_name = $input['last_name'];
            $userDetails->username = $input['username'];
            $userDetails->email = $input['email'];
            $userDetails->mobile_number = $input['mobile_number'];
            $userDetails->save();

            if ($userDetails) {
                $success = $userDetails;
                return $this->sendResponse($success, Lang::get('messages.UPDATE_PROFILE_SUCCESSFULLY_MSG'));
            } else {
                return $this->sendError(Lang::get('messages.SOMETHING_WENT_WRONG_MSG'), []);
            }
        } catch (\Exception $ex)
        {
            return $this->sendError(Lang::get('messages.SOMTHING_WENT_WRONG'), []);
        }
    }

    public function userDelete($id) {
        try {
            $userData = User::find($id);

            if($userData) {
                $userData = $userData->delete();
                return $this->sendResponse($userData, Lang::get('messages.DELETE_PROFILE_SUCCESSFULLY_MSG'));
            }
        } catch (\Exception $ex)
        {

            return $this->sendError(Lang::get('messages.SOMTHING_WENT_WRONG'), []);
        }
    }

     /**
     * logout API
     *
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request)
    {
        $request->user()->token()->revoke();
        $success = Auth::user();
        return $this->sendResponse($success, Lang::get('messages.LOGOUT_SUCCESSFULLY'));
    }

    public function forgotPassword(Request $request) {
        try {
            $userName = $request->username;
            $userData = User::where('username', $userName)->first();
            if($userData) {
                return $this->sendResponse($userData, Lang::get('messages.RECORD_FOUND'));
            } else {
                return $this->sendError(Lang::get('messages.SOMETHING_WENT_WRONG_MSG'), []);
            }
        } catch (\Exception $ex)
        {
            return $this->sendError(Lang::get('messages.SOMTHING_WENT_WRONG'), []);
        }
    }

    public function changePassword(Request $request) {
        try {
            $validator = Validator::make($request->all(), [
                'username' => 'required',
                'password' => 'required',
            ]);

            if ($validator->fails()) {
                return $this->sendError($validator->errors()->first());
            }

            $userDetails = User::where('username', $request->username)->first();

            if ($userDetails) {
                $success = [];
                $userDetails->password= Hash::make($request->password);
                $userDetails->save();
                return $this->sendResponse($success, Lang::get('messages.PASSWORD_CHANGE_SUCCESSFULLY_MSG'));
            }
            else {
                return $this->sendError(Lang::get('messages.NO_RECORD_FOUND'), []);
            }
        } catch (\Exception $ex)
        {
            return $this->sendError(Lang::get('messages.SOMTHING_WENT_WRONG'), []);
        }
    }

    public function adminDetail(Request $request) {
        $accountDetails = AdminAccount::where('status', '1')->orderBy('id', 'DESC')->first();

        if($accountDetails) {
            // Read the image file
            $imageContents = Storage::disk('public')->get('admin_account/'.$accountDetails->image);

            // Encode the image contents as a Base64 string
            $base64Image = base64_encode($imageContents);
            $accountDetails->image = "data:image/jpeg;base64". $base64Image;
            return $this->sendResponse($accountDetails, Lang::get('messages.RECORD_FOUND'));
        }
        return $this->sendResponse((Object)[], Lang::get('messages.NO_RECORD_FOUND'));
    }
}
