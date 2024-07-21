<?php

namespace App\Http\Controllers;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class PostController extends Controller
{
    // Get all posts
    public function index()
    {
        $posts = Post::with('user')->get();
        return response()->json($posts, 200);
    }

    // Get one post
    public function show($id)
    {
        $post = Post::with('user')->find($id);
        if ($post) {
            return response()->json($post, 200);
        }
        return response()->json(['message' => 'Post not found'], 404);
    }

    // Create post
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'body' => 'required|string',
            'category_id' => 'nullable|exists:categories,id',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        $post = Post::create([
            'title' => $request->title,
            'body' => $request->body,
            'category_id' => $request->category_id,
            'user_id' => Auth::id(),
        ]);
        Log::info('Post created', ['post' => $post, 'user_id' => Auth::id()]);
        return response()->json(['message' => 'Post created successfully', 'post' => $post], 201);
    }

    // Update post
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'body' => 'required|string',
            'category_id' => 'nullable|exists:categories,id',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        $post = Post::find($id);
        if ($post) {
            $post->update([
                'title' => $request->title,
                'body' => $request->body,
                'category_id' => $request->category_id
            ]);
            Log::info('Post updated', ['post' => $post, 'user_id' => Auth::id()]);  // Log update
            return response()->json(['message' => 'Post updated successfully', 'post' => $post], 200);
        }
        return response()->json(['message' => 'Post not found'], 404);
    }

    // Delete post
    public function destroy($id)
    {
        $post = Post::find($id);
        if ($post) {
            $post->delete();
            Log::info('Post deleted', ['post_id' => $id, 'user_id' => Auth::id()]);
            return response()->json(['message' => 'Post deleted successfully'], 200);
        }
        return response()->json(['message' => 'Post not found'], 404);
    }
}
