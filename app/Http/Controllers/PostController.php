<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PostController extends Controller
{
    //JWT Auth. Middleware
    public function __construct()
    {
        $this->middleware('api.auth',['except' => [
            'index',
            'show',
            'getImagePosts',
            'postsByCategory',
            'postsByUser',
            'posts']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $posts = Post::all();
        return response()->json([
            'status' => 'Success',
            'code' => 200,
            'post' => $posts
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Get the form data
        $loggedUser = $request->get('loggedUser');
        $json =  $request->input('json'); //Default = null
        $inputData = json_decode($json,true);
        $inputData['user_id'] = $loggedUser->sub; //adding user's id

        // Information that should not be modified
        unset($inputData['id']);
        unset($inputData['created_at']);

        // Data validation
        $dataValidation = validator($inputData,[
            'user_id' => 'required|integer|exists:users,id',
            'category_id' => 'required|integer|exists:categories,id',
            'title' => 'required',
            'content' => 'required',
        ]);

        if($dataValidation->fails()){
            $data = array(
                'status' => 'Error',
                'code' => 400,
                'message' => 'Problem with post creation',
                'errors' => $dataValidation->errors()
            );
        }else{
            // Post creation
            $post  = Post::create($inputData);

            $data = array(
                'status' => 'Success',
                'code' => 200,
                'message' => 'Post creation successful',
                'post' => $post
            );
        }

        return response()->json($data, $data['code']);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $post = Post::find($id);
        if(is_object($post)){
            $data = array(
                'status' => 'Success',
                'code' => 200,
                'post' => $post
            );
        }else{
            $data = array(
                'status' => 'Error',
                'code' => 400,
                'message' => 'Post doesn\'t exists'
            );
        }

        return response()->json($data, $data['code']);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //Validation of belonging Get the form data
        $loggedUserID = $request->get('loggedUser')->sub;
        $post = Post::where('id',$id)->where('user_id',$loggedUserID)->first();
        if($post && is_numeric($id)){

            $json =  $request->input('json'); //Default = null
            $inputData = json_decode($json,true);
            $inputData['user_id'] = $loggedUserID; //adding user's id

            // Information that should not be modified
            unset($inputData['id']);
            unset($inputData['created_at']);

            // Data validation
            $dataValidation = validator($inputData,[
                'user_id' => 'required|integer|exists:users,id',
                'category_id' => 'required|integer|exists:categories,id',
                'title' => 'required',
                'content' => 'required'
            ]);

            if($dataValidation->fails()){
                $data = array(
                    'status' => 'Error',
                    'code' => 400,
                    'message' => 'Problem updating post',
                    'errors' => $dataValidation->errors()
                );
            }else{
                // Post creation
                $post = Post::where('id', $id)->update($inputData);

                $data = array(
                    'status' => 'Success',
                    'code' => 200,
                    'message' => 'Post update successful',
                    'post' => $post
                );
            }
        }else{
            $data = array(
                'status' => 'Error',
                'code' => 400,
                'message' => 'Post doesn\'t exists or it\'s not your post',
            );
        }

        return response()->json($data, $data['code']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        $loggedUserID = $request->get('loggedUser')->sub;
        $post = Post::where('id',$id)->where('user_id',$loggedUserID)->first();
        if($post && is_numeric($id)){
            $post->delete();
            $data = array(
                'status' => 'Success',
                'code' => 200,
                'message' => 'Post was deleted',

            );
        }else{
            $data = array(
                'status' => 'Error',
                'code' => 400,
                'message' => 'Post doesn\'t exists or it\'s not your post',
            );
        }

        return response()->json($data, $data['code']);
    }

    public function uploadImagePost(Request $request){
        $image = $request->file('file0');
        //$loggedUser = $request->get('loggedUser');
        $dataValidation = validator($request->all(),[
            'file0' => 'required|image|mimes:jpg,jpeg,png,gif',
        ]);

        if($dataValidation->fails()){
            $data = array(
                'status' => 'Error',
                'code' => 400,
                'error' => $dataValidation->errors(),
                'message' => 'Problem uploading image'
            );
        }else{
            $image_name= time().$image->getClientOriginalName();
            \Storage::disk('posts')->put($image_name,\File::get($image));
            //Post::where('id', $loggedUser->sub)->update(['image' => $image_name]);
            $data = array(
                'status' => 'Success',
                'code' => 200,
                'message' => 'Image uploaded',
                'image' => $image_name
            );

        }

        return response()->json($data, $data['code']);
    }

    public function getImagePosts($filename){
        if(\Storage::disk('posts')->exists($filename)){
            $image = \Storage::disk('posts')->get($filename);
            return new Response($image,200);
        } else {
            return response()->json(array(
                'status' => 'Error',
                'code' => 400,
                'message' => 'Problem getting image'
            ), 400);
        }
    }

    public function postsByCategory($id){
        $posts = Post::where('category_id', $id)->get();
        $category = Category::find($id);
        return response()->json(array(
            'status' => 'Success',
            'post' => $posts,
            'category' => $category->name
        ), 200);
    }

    public function postsByUser($id){
        $posts = Post::where('user_id', $id)->get();
        return response()->json(array(
            'status' => 'Success',
            'post' => $posts
        ), 200);
    }

    public function posts(){
        $posts = Post::get();
        return response()->json(array(
            'status' => 'Success',
            'post' => $posts
        ), 200);
    }
}
