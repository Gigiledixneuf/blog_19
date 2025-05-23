<?php

namespace App\Http\Controllers;
use App\Http\Resources\ArticleResource;
use Illuminate\Support\Facades\Log;

use Illuminate\Http\Request;
use App\Http\Requests\ArticleRequest;
use App\Models\Article;
use Illuminate\Support\Facades\Storage;

class ArticleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return ArticleResource::collection(Article::with(['user', 'likes'])->get());

    }

    /**
     * Store a newly created resource in storage.
     */



     public function store(ArticleRequest $request)
     {
         try {
             $data = $request->validated();
     
             if ($request->hasFile('picture')) {
                 $image = $request->file('picture');
                 $data['picture'] = $image->store('articles', 'public');
             }
     
             $data['user_id'] = auth()->id();
             $article = Article::create($data);
     
             $article->categories()->attach($request->validated('categories'));
             
            
             return response()->json([
                 'message' => 'Post added successfully',
                 'article' => $article,
             ], 201);
     
         } catch (\Exception $exception) {
             return response()->json([
                 'error' => 'An error occurred: ' . $exception->getMessage()
             ], 500);
         }
     }
     

    /**
     * Display the specified resource.
     */
    public function show( int $id)
    {
        $article = Article::withCount("likes")->findOrFail($id);
        return new ArticleResource($article->load('categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ArticleRequest $request, $id)
    {
        try {
            // Find the article by ID, if it doesn't exist, return a 404 response
            $article = Article::findOrFail($id);

            // Validate and get the input data
            $data = $request->validated();

            // Check if a new image is uploaded, if so, update it
            if ($request->hasFile('picture')) {
                // Delete the old picture if it exists
                if ($article->picture) {
                    Storage::disk('public')->delete($article->picture);
                }

                // Store the new image
                $image = $request->file('picture');
                $data['picture'] = $image->store('articles', 'public');
            }

            // Update the user ID if needed (you can skip this if the user is not changing)
            $data['user_id'] = auth()->user()->id;

            // Update the article with the new data
            $article->update($data);

            // Update the categories (detach old ones and attach new ones)
            $article->categories()->sync($request->validated('categories'));

            dd($article);

            return response()->json([
                'message' => 'Post updated successfully',
                'article' => $article,
            ], 200);

        } catch (\Exception $exception) {
            return response()->json(['error' => 'An error occurred: ' . $exception->getMessage()], 500);
        }
    }








    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            // Find the article by its ID
            $article = Article::findOrFail($id);

            // Check if the authenticated user is the author of the article
            if ($article->user_id !== auth()->user()->id) {
                return response()->json(['error' => 'You are not authorized to delete this article'], 403);
            }

            // Check if an image exists and delete it
            if ($article->picture && Storage::exists('public/' . $article->picture)) {
                Storage::delete('public/' . $article->picture);
            }

            // Delete the article
            $article->delete();
            $article->categories()->detach();

            return response()->json([

            ], 200);

        } catch (\Exception $exception) {
            \Log::error($exception);
            return response()->json(['error' => 'An error occurred: ' . $exception->getMessage()], 500);
        }
    }

}
