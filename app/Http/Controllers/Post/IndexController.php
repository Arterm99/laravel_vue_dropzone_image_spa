<?php

namespace App\Http\Controllers\Post;

use App\Http\Controllers\Controller;
use App\Http\Resources\Post\PostResource;
use App\Models\Post;

class IndexController extends Controller
{
    public function __invoke()
    {
        // Latest - сортировка от более раннего к старому
        $post = Post::Latest()->first();
        return new PostResource($post);
    }
}
