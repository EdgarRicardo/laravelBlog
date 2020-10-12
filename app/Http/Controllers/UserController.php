<?php

namespace App\Http\Controllers;

use App\Helpers\JwtAuth;
use App\Http\Requests\UserValidation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

use function GuzzleHttp\json_decode;

class UserController extends Controller
{
    public function index()
    {
        //
    }

    // Show the form for creating a new resource.
    public function create()
    {

    }



    // Function for user creation
    public function store(Request $request)
    {
        // Get the form data
        $json =  $request->input('json'); //Default = null
        $inputData = json_decode($json,true);

        // Password encryption before clean data
        $encryptedPass = password_hash($inputData['password'], PASSWORD_DEFAULT, ['cost' => 10]);

        //Clean Data
        if(!empty($inputData))
            $inputData = array_map('trim', $inputData);

        // Data validation
        $dataValidation = validator($inputData,[
            'name' => 'required|alpha',
            'surname' => 'required|alpha',
            'email' => 'required|email|unique:users', // User already exists? ORM
            'password' => 'required',
        ]);

        if($dataValidation->fails()){
            $data = array(
                'status' => 'Error',
                'code' => 400,
                'message' => 'Problem with the user creation',
                'errors' => $dataValidation->errors()
            );
        }else{

            // User creation
            $user = new User();
            $user->name = $inputData['name'];
            $user->surname = $inputData['surname'];
            $user->email = $inputData['email'];
            $user->password = $encryptedPass;
            $user->save();

            $data = array(
                'status' => 'Success',
                'code' => 200,
                'message' => 'User creation successful',
                'user' => $user
            );
        }

        return response()->json($data, $data['code']);
    }

    public function login(Request $request)
    {
        // Get post information
        $json =  $request->input('json'); //Default = null
        $inputData = json_decode($json,true);

        // Data validation
        $dataValidation = validator($inputData,[
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if($dataValidation->fails()){
            return response()->json(array(
                'status' => 'Error',
                'code' => 400,
                'message' => 'The user did not complete the form',
                'errors' => $dataValidation->errors()
            ),400);
        }else{
            $email=$inputData['email'];
            $password=$inputData['password'];

            $jwtAuth = new JwtAuth();
            $data = $jwtAuth->signup($email,$password);
            return response()->json($data, $data["code"]);
        }
    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        //
    }

    public function update(Request $request)
    {
        $json =  $request->input('json'); //Default = null
        $inputData = json_decode($json,true);
        $loggedUser = $request->get('loggedUser');
        // Information that should not be modified
        unset($inputData['id']);
        unset($inputData['password']);
        unset($inputData['remember_token']);
        unset($inputData['created_at']);

        $dataValidation = validator($inputData,[
            'name' => 'required|alpha',
            'surname' => 'required|alpha',
            'role' => 'required|alpha',
            'email' => 'required|email|unique:users,email,'.$loggedUser->sub
        ]);

        //Update
        if($dataValidation->fails()){
            $data = array(
                'status' => 'Error',
                'code' => 400,
                'message' => $dataValidation->errors()
            );
        }else{
            User::where('id', $loggedUser->sub)->update($inputData);
            $userUpdated = User::find($loggedUser->sub);
            $data = array(
                'status' => 'Success',
                'code' => 200,
                'message' => 'User updated',
                'user' => $userUpdated
            );
        }

        return response()->json($data, $data['code']);
    }

    public function destroy($id)
    {
        //
    }

    public function uploadAvatar(Request $request){
        $image = $request->file('file0');
        $loggedUser = $request->get('loggedUser');
        $dataValidation = validator($request->all(),[
            'file0' => 'required|image|mimes:jpg,jpeg,png,gif',
        ]);

        if($dataValidation->fails()){
            $data = array(
                'status' => 'Error',
                'code' => 400,
                'error' => $dataValidation->errors(),
                'message' => 'Problem uploading avatar'
            );
        }else{
            $image_name= time().$image->getClientOriginalName();
            \Storage::disk('users')->put($image_name,\File::get($image));
            //User::where('id', $loggedUser->sub)->update(['image' => $image_name]);
            $data = array(
                'status' => 'Success',
                'code' => 200,
                'message' => 'Avatar uploaded',
                'image' => $image_name
            );

        }

        return response()->json($data, $data['code']);
    }

    public function getImageUsers($filename){
        if(\Storage::disk('users')->exists($filename)){
            $image = \Storage::disk('users')->get($filename);
            return new Response($image,200);
        } else {
            return response()->json(array(
                'status' => 'Error',
                'code' => 400,
                'message' => 'Problem getting image'
            ), 400);
        }
    }

    //It's not necesary this method
    public function userInfo($id){
        $user = User::find($id);
        if(is_object($user)){
            $data = array(
                'status' => 'Success',
                'code' => 200,
                'user' => $user
            );
        }else{
            $data = array(
                'status' => 'Error',
                'code' => 400,
                'message' => 'User doesn\'t exists'
            );
        }
        return response()->json($data, $data['code']);
    }
}
