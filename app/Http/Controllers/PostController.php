<?php

namespace App\Http\Controllers;

use App\Http\Resources\PostResource;
use App\Models\Post;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;

class PostController extends Controller
{
    /**
     * List all posts
     *
     * Return a list of posts.
     *
     * @response AnonymousResourceCollection<LengthAwarePaginator<PostResource>>
     *
     * @unauthenticated
     */
    public function index(Request $request)
    {
        $perPage = $request->integer('per_page', 10);
        $page = $request->integer('page', 1);

        $posts = Post::paginate($perPage, ['*'], 'page', $page);

        return PostResource::collection($posts);
    }

    /**
     * Create a post
     *
     * Create a new post.
     */
    public function store(Request $request)
    {
        $fields = $request->validate([
            'title' => 'required|max:255',
            'content' => 'required',
        ]);

        $post = $request->user()
            ->posts()
            ->create([
                'title' => $fields['title'],
                'slug' => $this->generateUniqueSlug($fields['title']),
                'content' => $fields['content'],
            ]);

        /**
         * The created post.
         *
         * @status 201
         */
        return PostResource::make($post);
    }

    /**
     * Retrieve a post
     *
     * Retrieve a post by ID.
     *
     * @param  int  $id  The post ID
     *
     * @unauthenticated
     */
    public function show($id)
    {
        try {
            $post = Post::findOrFail($id);

            return PostResource::make($post);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Post not found'], 404);
        }
    }

    /**
     * Update a post
     *
     * Update the specified post.
     *
     * @param  int  $id  The post ID
     */
    public function update(Request $request, $id)
    {
        try {
            $post = Post::findOrFail($id);

            Gate::authorize('modify', $post);

            $fields = $request->validate([
                'title' => 'required|max:255',
                'content' => 'required',
            ]);

            $post->update([
                'title' => $fields['title'],
                'slug' => $this->generateUniqueSlug($fields['title']),
                'content' => $fields['content'],
            ]);

            return PostResource::make($post);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Post not found'], 404);
        } catch (AuthorizationException $e) {
            return response()->json(['error' => 'You do not have permission to modify this post.'], 403);
        }
    }

    /**
     * Delete a post
     *
     * Delete the specified post.
     *
     * @param  int  $id  The post ID
     */
    public function destroy($id)
    {
        try {
            $post = Post::findOrFail($id);

            Gate::authorize('modify', $post);

            $post->delete();

            return response()->noContent();
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Post not found'], 404);
        } catch (AuthorizationException $e) {
            return response()->json(['error' => 'You do not have permission to delete this post.'], 403);
        }
    }

    private function generateUniqueSlug($title)
    {
        $slug = Str::slug($title);
        $count = Post::where('slug', 'like', "{$slug}%")->count();

        return $count ? "{$slug}-{$count}" : $slug;
    }
}
