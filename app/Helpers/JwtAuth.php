<?php

namespace App\Helpers;

use App\Models\User;
use Firebase\JWT\JWT;
use Illuminate\Support\Facades\DB;
use PhpParser\Node\Expr\FuncCall;

class JwtAuth{

    public $key;

    public function __construct()
    {
        $this->key= 'secretToken@jeje';
    }

    public function signup($email, $password, $getToken = null){
        //User exists?
        $user = User::where([
            'email' => $email,
        ])->first();

        //Correct credentials?
        if(is_object($user) && password_verify($password, $user->password)){
            //Token generation for the logged user
            $token = array(
                'sub' => $user->id,
                'email' => $user->email,
                'name' => $user->name,
                'surname' => $user->surname,
                'image' => $user->image,
                'role' => $user->role,
                'iat' => time(), //Creation date token
                'exp' => time() + (60 * 60 * 24 * 7) // sec * min * hours * days - 1 week expiration
            );

            $jwtToken = JWT::encode($token, $this->key, 'HS256'); //HS256 cipher algo.
            $decodedToken = JWT::decode($jwtToken, $this->key,['HS256']);
            //Return the decoded data or the token, based on a parameter
            /*if(is_null($getToken)){
                return $jwtToken;
            }else{
                return $decodedToken;
            }*/
            return array(
                'status' => 'Success',
                'code' => 200,
                'message' => 'Correct Login',
                'token' => $jwtToken,
                'userInfo' => $decodedToken
            );
        }else{
             return array(
                'status' => 'Error',
                'code' => 400,
                'message' => 'Incorrect Login'
            );
        }

        return true;
    }

    public function checkToken($jwtToken, $getIdentity = false){

        try {
            // Clean quotation marks
            $jwtToken = str_replace('"','',$jwtToken);

            $decodedToken = JWT::decode($jwtToken, $this->key,['HS256']);
        } catch (\UnexpectedValueException $e) {
            return false;
        } catch (\DomainException $e){
            return false;
        }

        if(!empty($decodedToken) && is_object($decodedToken) && isset($decodedToken->sub)){
            if($getIdentity) return $decodedToken;
            return true;
        }else
            return false;
    }
}

?>
