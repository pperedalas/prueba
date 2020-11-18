<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\User;

class UserController extends Controller {

    public function test(Request $request) {
        return "test UserController";
    }

    public function register(Request $request) {

        // Get data by POST
        $json = $request->input('json', null);
        if (!empty($json)) {
            $params = json_decode($json);
            $params_array = json_decode($json, true);
            $params_array = array_map('trim', $params_array);

            //Validate data
            $validate = \Validator::make($params_array, [
                        'name' => 'required|alpha',
                        'surname' => 'alpha',
                        'email' => 'required|email|unique:users',
                        'password' => 'required'
            ]);

            if ($validate->fails()) {
                // Failed validation
                $data = [
                    'status' => 'error',
                    'code' => 404,
                    'message' => 'Data error on register',
                    'errors' => $validate->errors(),
                    'params' => $params_array
                ];
            } else {
                // Successful validation
                // Password hash
                $pwd = hash('sha256', $params->password);

                // Create user
                $user = new User();
                $user->name = $params_array['name'];
                $user->surname = $params_array['surname'];
                $user->email = $params_array['email'];
                $user->password = $pwd;
                $user->role = 'ROLE_USER';

                // Save user
                $user->save();

                $data = [
                    'status' => 'success',
                    'code' => 200,
                    'message' => 'Success on register',
                    'user' => $user
                ];
            }
        } else {
            $data = [
                'status' => 'error',
                'code' => 404,
                'message' => 'Emtpy data error on register',
            ];
        }

        return response()->json($data, $data['code']);
    }

    public function login(Request $request) {
        $jwtAuth = new \JWTAuth();
        // Get data by POST
        $json = $request->input('json', null);

        if (!empty($json)) {
            $params = json_decode($json);
            $params_array = json_decode($json, true);

            $validate = \Validator::make($params_array, [
                        'email' => 'required|email',
                        'password' => 'required'
            ]);
            if ($validate->fails()) {
                // Failed validation
                $signup = [
                    'status' => 'error',
                    'code' => 404,
                    'message' => 'Data error on login',
                    'errors' => $validate->errors()
                ];
            } else {
                // Successful validation
                $pwd = hash('sha256', $params->password);

                // Return token
                $signup = $jwtAuth->signup($params->email, $pwd);

                if (!empty($params->getToken)) {
                    $signup = $jwtAuth->signup($params->email, $pwd, true);
                }
            }
        } else {
            $signup = [
                'status' => 'error',
                'code' => 404,
                'message' => 'Empty data error on login',
            ];
        }
        return response()->json($signup, 200);
    }

    public function update(Request $request) {
        // Check if user is logged in
        $token = $request->header('Authorization');
        $jwtAuth = new \JWTAuth();
        $checkToken = $jwtAuth->checkToken($token);

        // Get data by POST
        $json = $request->input('json', null);
        $params_array = json_decode($json, true);

        if ($checkToken && !empty($params_array)) {
            // Update user
            // Get loged user
            $user = $jwtAuth->checkToken($token, true);

            // Validate data
            $validate = \Validator::make($params_array, [
                        'name' => 'required|alpha',
                        'surname' => 'alpha',
                        'email' => 'required|email|unique:users,email,' . $user->sub
            ]);
            if ($validate->fails()) {
                $data = [
                    'code' => 404,
                    'status' => 'error',
                    'message' => 'Error updating user',
                    'error' => $validate->errors()
                ];
            } else {
                // Remove non-updatable fields
                unset($params_array['id']);
                unset($params_array['role']);
                unset($params_array['password']);
                unset($params_array['email']);
                unset($params_array['created_at']);
                unset($params_array['remember_token']);

                // Update in DB
                $user_update = User::where('id', $user->sub)->update($params_array);

                // Return array
                $data = [
                    'status' => 'success',
                    'code' => 200,
                    'token' => $user,
                    'changes' => $params_array
                ];
            }
        } else {
            $data = [
                'status' => 'error',
                'code' => 404,
                'message' => 'User is not logged'
            ];
        }

        return response()->json($data, $data['code']);
    }

    public function upload(Request $request) {
        // Get data
        $image = $request->file('file0');
        // Validate image
        $validate = \Validator::make($request->all(), [
                    'file0' => 'required|image|mimes:jpg,jpeg,png,gif'
        ]);
        // Save image
        if (!$image || $validate->fails()) {
            $data = [
                'status' => 'error',
                'code' => 404,
                'message' => 'Failed upload'
            ];
        } else {
            $image_name = time() . $image->getClientOriginalName();
            \Storage::disk('users')->put($image_name, \File::get($image));

            $data = [
                'status' => 'success',
                'code' => 200,
                'message' => 'Succesful upload',
                'image' => $image_name
            ];
        }
        return response()->json($data, $data['code']);
    }

    public function getImage($filename) {
        $isset = \Storage::disk('users')->exists($filename);

        if ($isset) {
            $data = \Storage::disk('users')->get($filename);

            return new Response($data, 200);
        } else {
            $data = [
                'status' => 'error',
                'code' => 404,
                'message' => 'Failed avatar'
            ];
            return response()->json($data, $data['code']);
        }
    }

    public function detail($id) {
        $user = User::find($id);

        if (is_object($user)) {
            $data = [
                'status' => 'success',
                'code' => 200,
                'user' => $user
            ];
        } else {
            $data = [
                'status' => 'error',
                'code' => 404,
                'message' => 'Failed detail'
            ];
        }

        return response()->json($data, $data['code']);
    }

}
