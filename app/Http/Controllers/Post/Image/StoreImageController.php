<?php

namespace App\Http\Controllers\Post\Image;

use App\Http\Controllers\Controller;
use App\Http\Requests\Post\Image\StoreRequest;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class StoreImageController extends Controller
{
    public function __invoke(StoreRequest $request)
    {
        $data = $request->validated();
        $image = $data['image'];
        $name = md5(Carbon::now() . '_' . $image->getClientOriginalName()) . '.' . $image->getClientOriginalExtension();

        // putFileAs - лучший аналог put
        $filePath = Storage::disk('public')->putFileAs('/images', $image, $name);

        return response()->json(['url' => url('/storage/' . $filePath)]);
    }
}
