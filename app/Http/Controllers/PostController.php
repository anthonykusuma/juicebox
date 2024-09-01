<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PostController extends Controller
{
    /**
     * Display a paginated list of all posts.
     */
    public function index(Request $request)
    {
        $posts = Post::paginate(10);

        return response($posts, 200);
    }

    /**
     * Store a new post.
     */
    public function store(Request $request)
    {
        $fields = $request->validate([
            'title' => 'required|max:255',
            'content' => 'required',
        ]);

        $post = $request->user()->posts()->create([
            'title' => $fields['title'],
            'slug' => $this->generateUniqueSlug($fields['title']),
            'content' => $fields['content'],
        ]);

        return response($post, 201);
    }

    /**
     * Display the specified post.
     *
     * @param  int  $id  the postID
     */
    public function show($id)
    {
        try {
            $post = Post::findOrFail($id);

            return response($post, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Post not found'], 404);
        }
    }

    /**
     * Update the specified post.
     *
     * @param  int  $id  the postID
     */
    public function update(Request $request, $id)
    {
        try {
            $post = Post::findOrFail($id);

            $fields = $request->validate([
                'title' => 'required|max:255',
                'content' => 'required',
            ]);

            $post->update([
                'title' => $fields['title'],
                'slug' => $this->generateUniqueSlug($fields['title']),
                'content' => $fields['content'],
            ]);

            return response($post, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Post not found'], 404);
        }
    }

    /**
     * Remove the specified post.
     *
     * @param  int  $id  the postID
     */
    public function destroy($id)
    {
        try {
            $post = Post::findOrFail($id);

            $post->delete();

            return response()->json(['message' => 'Post deleted'], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Post not found'], 404);
        }
    }

    private function generateUniqueSlug($title)
    {
        $slug = Str::slug($title);
        $count = Post::where('slug', 'like', "{$slug}%")->count();

        return $count ? "{$slug}-{$count}" : $slug;
    }
}
