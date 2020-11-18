<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Post;
use App\Models\User;

class PostController extends Controller {

    public function __construct() {
        $this->middleware('api.auth', ['except' => ['index', 'show', 'getImage', 'getPostsByCategory', 'getPostsByUser']]);
    }
    
    private function getIdentity($request) {
        // Get logged user
        $jwtAuth = new \JWTAuth();
        $token = $request->header('Authorization', null);
        $user = $jwtAuth->checkToken($token, true);
        $user = User::find($user->sub);

        return $user;
    }

    public function index() {
        $posts = Post::all();

        if (is_object($posts)) {
            $data = [
                'code' => 200,
                'status' => 'success',
                'posts' => $posts
            ];
        } else {
            $data = [
                'code' => 404,
                'status' => 'error',
                'message' => 'Error showing posts'
            ];
        }

        return response()->json($data, $data['code']);
    }

    public function show($id) {
        $post = Post::find($id)
                    ->load('category')
                    ->load('user');

        if (is_object($post)) {
            $data = [
                'code' => 200,
                'status' => 'success',
                'post' => $post
            ];
        } else {
            $data = [
                'code' => 404,
                'status' => 'error',
                'message' => 'Error showing post'
            ];
        }

        return response()->json($data, $data['code']);
    }

    public function store(Request $request) {
        // Get data by POST
        $json = $request->input('json', null);
        $params = json_decode($json);

        $params_array = json_decode($json, true);

        if (!empty($params_array)) {
            // Get logged user
            $user = $this->getIdentity($request);
/*             var_dump($user);
            die; */

            // Validate data
            $validate = \Validator::make($params_array, [
                        'title' => 'required',
                        'content' => 'required',
                        'category_id' => 'required'
            ]);
            // Save post
            if ($validate->fails()) {
                $data = [
                    'code' => 404,
                    'status' => 'error',
                    'message' => 'Error saving post',
                    'error' => $validate->errors()
                ];
            } else {
                $post = new Post();
                $post->user_id = $user->id;
                $post->title = $params->title;
                $post->content = $params->content;
                $post->category_id = $params->category_id;
                $post->image = $params->image;
                $post->save();

                $data = [
                    'code' => 200,
                    'status' => 'success',
                    'post' => $post
                ];
            }
        } else {
            $data = [
                'code' => 404,
                'status' => 'error',
                'message' => 'No post to save'
            ];
        }

        // Return
        return response()->json($data, $data['code']);
    }

    public function update($id, Request $request) {
        // Get logged user
        $user = $this->getIdentity($request);

        // Get data by POST
        $json = $request->input('json', null);
        $params_array = json_decode($json, true);

        if (!empty($params_array)) {
            //Validate data
            $validate = \Validator::make($params_array, [
                        'title' => 'required',
                        'content' => 'required',
                        'category_id' => 'required'
            ]);
            if ($validate->fails()) {
                $data = [
                    'code' => 404,
                    'status' => 'error',
                    'message' => 'Error updating post',
                    'error' => $validate->errors()
                ];
            } else {
                // Remove non-updatable fields
                unset($params_array['id']);
                unset($params_array['user_id']);
                unset($params_array['created_at']);

                // if isset post
                $post = Post::find($id);

                if (is_object($post)) {

                    // Update post
                    $where = [
                        'id' => $id
                    ];

                    if ($user->role != 'ROLE_ADMIN') {
                        $where['user_id'] = strval($user->id);
                    }else{
                        $where['user_id'] = strval($post->user_id);
                    }

                    $post = Post::where('id', $where['id'])
                            ->where('user_id', $where['user_id'])
                            ->first();
                    
                    if(is_object($post)){
                        $post->update($params_array);
                        $data = [
                            'code' => 200,
                            'status' => 'success',
                            'post' => $post,
                            'changes' => $params_array
                        ];
                    }else{
                        $data = [
                        'code' => 404,
                        'status' => 'error',
                        'message' => 'Post doesn\'t exists for you'
                    ];
                    }
                } else {
                    $data = [
                        'code' => 404,
                        'status' => 'error',
                        'message' => 'Post doesn\'t exists, can\'t update'
                    ];
                }
            }
        } else {
            $data = [
                'code' => 404,
                'status' => 'error',
                'message' => 'Post doesn\'t exists, can\'t update2'
            ];
        }

        return response()->json($data, $data['code']);
    }

    public function destroy($id, Request $request) {
        // Get logged user
        $user = $this->getIdentity($request);

        // Get post
        // If admin requests
        if($user->role == "ROLE_ADMIN"){
            $post = Post::where('id', $id)
            ->first();
        // Else    
        } else {
            $post = Post::where('id', $id)
            ->where('user_id', $user->sub)
            ->first();
        }

        if (is_object($post)) {
            // Delete post
            $post->delete();

            // Return
            $data = [
                'code' => 200,
                'status' => 'success',
                'deleted' => $post
            ];
        } else {
            $data = [
                'code' => 404,
                'status' => 'error',
                'message' => 'Post doesn\'t exists, can\'t delete'
            ];
        }

        return response()->json($data, $data['code']);
    }

    public function upload(Request $request) {
        // Get data
        $image = $request->file('file0');
        
        // Validate data
        $validate = \Validator::make($request->all(), [
            'file0' => 'required|image|mimes:jpg,jpeg,png,gif'
        ]);
        
        // Save image
        if(!$image || $validate->fails()){
            $data = [
                'code' => 404,
                'status' => 'error',
                'error' => 'Failed image upload'
            ];
        }else{
            $image_name = time().$image->getClientOriginalName();
            
            \Storage::disk('images')->put($image_name, \File::get($image));
            
            $data = [
                'code' => 200,
                'status' => 'success',
                'message' => 'Image uploaded',
                'image' => $image_name
            ];
        }
        
        return response()->json($data, $data['code']);
    }
    
    public function getImage($filename){
        // Check if file exists
        $isset = \Storage::disk('images')->exists($filename);
        
        if($isset){
            // Get image
            $file = \Storage::disk('images')->get($filename);
            
            // Return image
            return new Response($file, 200);
        }else{
            $data = [
                'code' => 404,
                'status' => 'error',
                'message' => 'Image doesn\'t exists'
            ];
            return response()->json($data, $data['code']);
        }
    }
    
    public function getPostsByCategory($id) {
        $posts = Post::where('category_id', $id)->get();
        
        return response()->json([
            'status' => 'success',
            'posts' => $posts
        ], 200);
    }
    
    public function getPostsByUser($id) {
        $posts = Post::where('user_id', $id)->get();
        
        return response()->json([
            'status' => 'success',
            'posts' => $posts
        ], 200);
    }
}
