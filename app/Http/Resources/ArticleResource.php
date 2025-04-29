<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ArticleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        Carbon::setLocale('fr'); // Définir la langue en français

        return [
            'id' => $this->id,
            'title' => $this->title,
            "picture" => $this->picture,
            "user" => new UserResource($this->user),
            "content" => $this->content,
            'nbr_comment' => $this->comments->count(),
            'comments' => CommentResource::collection($this->comments),
            'likes' => $this->likes_count,
            'category' => CategoryResource::collection($this->categories),
            'date_creation' => Carbon::parse($this->created_at)->diffForHumans(),
            'last_modif' => $this->updated_at,
        ];
    }
}
