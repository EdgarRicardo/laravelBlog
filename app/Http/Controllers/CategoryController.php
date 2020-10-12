<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CategoryController extends Controller
{
     //JWT Auth. Middleware
    public function __construct()
    {
        $this->middleware('api.auth',['except' => ['index','show']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $categories = Category::all();
        return response()->json([
            'status' => 'Success',
            'code' => 200,
            'categories' => $categories
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
        $json =  $request->input('json'); //Default = null
        $inputData = json_decode($json,true);

        //Clean Data
        /*if(!empty($inputData))
            $inputData = array_map('trim', $inputData);*/

        // Data validation
        $dataValidation = validator($inputData,[
            'name' => 'required|unique:categories',
        ]);

        if($dataValidation->fails()){
            $data = array(
                'status' => 'Error',
                'code' => 400,
                'message' => 'Problem with the category creation',
                'errors' => $dataValidation->errors()
            );
        }else{
            // Category creation
            $category  = Category::create($inputData);

            $data = array(
                'status' => 'Success',
                'code' => 200,
                'message' => 'Category creation successful',
                'category' => $category
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
        $category = Category::find($id);
        if(is_object($category)){
            $data = array(
                'status' => 'Success',
                'code' => 200,
                'categoriy' => $category
            );
        }else{
            $data = array(
                'status' => 'Error',
                'code' => 400,
                'message' => 'Category doesn\'t exists'
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
        // Get the form data
        if(Category::find($id) && is_numeric($id)){
            $json =  $request->input('json'); //Default = null
            $inputData = json_decode($json,true);

            // Information that should not be modified
            unset($inputData['id']);
            unset($inputData['created_at']);

            // Data validation
            $dataValidation = validator($inputData,[
                'name' => 'required|unique:categories,name,'.$id,
            ]);

            if($dataValidation->fails()){
                $data = array(
                    'status' => 'Error',
                    'code' => 400,
                    'message' => 'Problem updating category ',
                    'errors' => $dataValidation->errors()
                );
            }else{
                // Category creation
                $category = Category::where('id', $id)->update($inputData);

                $data = array(
                    'status' => 'Success',
                    'code' => 200,
                    'message' => 'Category update successful',
                    'category' => $inputData
                );
            }
        }else{
            $data = array(
                'status' => 'Error',
                'code' => 400,
                'message' => 'Category doesn\'t exists',
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
    public function destroy($id)
    {
        //
    }
}
