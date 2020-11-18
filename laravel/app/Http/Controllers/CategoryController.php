<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use \App\Models\Category;

class CategoryController extends Controller {

    public function __construct() {
        $this->middleware('api.auth', ['except' => ['index', 'show']]);
    }

    public function index() {
        $categories = Category::all();

        if (is_object($categories)) {
            $data = [
                'code' => 200,
                'status' => 'success',
                'categories' => $categories
            ];
        } else {
            $data = [
                'code' => 404,
                'status' => 'error',
                'message' => 'Error showing categories'
            ];
        }

        return response()->json($data, $data['code']);
    }

    public function show($id) {
        $category = Category::find($id);

        if (is_object($category)) {
            $data = [
                'code' => 200,
                'status' => 'success',
                'category' => $category
            ];
        } else {
            $data = [
                'code' => 404,
                'status' => 'error',
                'message' => 'Error showing category'
            ];
        }

        return response()->json($data, $data['code']);
    }

    public function store(Request $request) {
        // Get data by POST
        $json = $request->input('json', null);
        $params_array = json_decode($json, true);

        if (!empty($params_array)) {
            // Validate data
            $validate = \Validator::make($params_array, [
                        'name' => 'required'
            ]);
            // Save category
            if ($validate->fails()) {
                $data = [
                    'code' => 404,
                    'status' => 'error',
                    'message' => 'Error saving category'
                ];
            } else {
                $category = new Category();
                $category->name = $params_array['name'];
                $category->save();

                $data = [
                    'code' => 200,
                    'status' => 'success',
                    'category' => $category
                ];
            }
        } else {
            $data = [
                'code' => 404,
                'status' => 'error',
                'message' => 'No category to save'
            ];
        }

        // Return
        return response()->json($data, $data['code']);
    }

    public function update($id, Request $request) {
        // Get data by POST
        $json = $request->input('json', null);
        $params_array = json_decode($json, true);

        if (!empty($params_array)) {
            //Validate data
            $validate = \Validator::make($params_array, [
                        'name' => 'required'
            ]);
            if ($validate->fails()) {
                $data = [
                    'code' => 404,
                    'status' => 'error',
                    'message' => 'Error saving category',
                    'error' => $validate->errors()
                ];
            } else {
                // Remove non-updatable fields
                unset($params_array['id']);
                unset($params_array['created_at']);

                // Update category
                $category = Category::where('id', $id)->update($params_array);

                $data = [
                    'code' => 200,
                    'status' => 'success',
                    'category' => $params_array
                ];
            }
        } else {
            $data = [
                'code' => 404,
                'status' => 'error',
                'message' => 'No category to update'
            ];
        }

        return response()->json($data, $data['code']);
    }

}
