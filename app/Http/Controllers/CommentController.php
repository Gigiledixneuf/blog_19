<?php

namespace App\Http\Controllers;

use App\Http\Resources\CommentResource;
use App\Models\Article;
use App\Models\Comment;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function store(Request $request){
        try {
            $validated = $request->validate([
                'content'=>'required|string',
                'article_id'=>'required',




            ]);
            $validated['user_id']=auth()->user()->id;
            $comment = Comment::create($validated);

            return response()->json([
                "message"=>"Comment add succesfully",
                "comment"=>$comment
            ]);



        } catch (\Exception $exception) {

            return response()->json(['error' => 'An error occurred: ' . $exception->getMessage()], 500);
        }


    }
    public function show(int $id)
    {
        // Récupérer les commentaires de l'article avec l'ID spécifié
        $comments = Comment::with('user')->where('article_id', $id)->get();

        // Retourner la collection des commentaires sous forme de CommentResource
        return CommentResource::collection($comments);
    }
}
