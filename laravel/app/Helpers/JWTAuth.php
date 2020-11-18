<?php
namespace App\Helpers;

use Firebase\JWT\JWT;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class JWTAuth{
    public $key;
    public function __construct() {
        $this->key = 'jwt_key-potato-123';
    }
    public function signup($email, $password, $getToken = null){
        // Check user
        $user = User::where([
            'email' => $email,
            'password' => $password
        ])->first();

        $signup = false;
        if(is_object($user)){
            $signup = true;
        }
        // Generate token
        if($signup){
            $token = [
                'sub'           => $user->id,
                'email'         => $user->email,
                'name'          => $user->name,
                'surname'       => $user->surname,
                'role'          => $user->role,
                'description'   => $user->description,
                'image'         => $user->image,
                'iat'           => time(),
                'exp'           => time() + (7*24*60*60)
            ];
        
            $jwt = JWT::encode($token, $this->key, 'HS256');
            $decoded = JWT::decode($jwt, $this->key, ['HS256']);

            if(is_null($getToken)){
                $data = $jwt;
            }else{
                $data = $decoded;
            }

        }else{
            $data = [
                'status'    => 'error',
                'code'    => 404,
                'message'   => 'Autentication error'
            ];
        }
        return $data;
    }
    
    public function checkToken($jwt, $getIdentity = false) {
        $auth = false;
        
        try{
            $jwt = str_replace('"', '', $jwt);
            $decoded = JWT::decode($jwt, $this->key,['HS256']);
        } catch (\UnexpectedValueException $e) {
            $auth = false;
        } catch (\DomainException $e) {
            $auth = false;
        }
        
        if(!empty($decoded) && is_object($decoded) && isset($decoded->sub)){
            $auth = true;
        }else{
            $auth = false;
        }
        
        if($getIdentity){
            $auth = $decoded;
        }
        
        return $auth;
    }
}