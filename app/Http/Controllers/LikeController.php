<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;

class LikeController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request,int $id)
    {
        $liker = Article::query()->findOrFail($id);

        $liker->likes()->toggle([$request->user()->id]);
    }
}
