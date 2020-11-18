<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\Category;

class TestController extends Controller
{
    public function index() {
        $test = ['Potato', 'Tomato', 'Onion'];
                
        return view('test.index', [
            'title' => 'test',
            'test' => $test
        ]);
    }
    
    public function testOrm(){
//        $posts = Post::all();
//        foreach ($posts as $post){
//            echo $post->title."<br>";
//            echo $post->user->name."<br>";
//            echo $post->category->name."<br>";
//            echo $post->content."<br>";
//        }
        
        $categories = Category::all();
        foreach ($categories as $category){
            echo "<hr>";
            echo "name: ".$category->name."<br>";
            
            foreach ($category->posts as $post){
                echo "title: ".$post->title."<br>";
                echo "user: ".$post->user->name."<br>";
                echo "category: ".$post->category->name."<br>";
                echo "content: ".$post->content."<br>";
                echo "<hr>";
            }
        }
        
        die();
    }
}
