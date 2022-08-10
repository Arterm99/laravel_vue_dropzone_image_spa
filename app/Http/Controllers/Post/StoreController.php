<?php

namespace App\Http\Controllers\Post;

use App\Http\Controllers\Controller;
use App\Http\Requests\Post\StoreRequest;
use App\Models\Image;
use App\Models\Post;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class StoreController extends Controller
{
    public function __invoke(StoreRequest $request)
    {
        $data =  $request->validated();
        $images =  $data['images'];

        // Удаляем изображения
        unset($data['images']);

        // Добавляем в бд
        $post = Post::firstOrCreate($data);
        foreach ($images as $image) {
            $name = md5(Carbon::now() . '_' . $image->getClientOriginalName()) . '.' . $image->getClientOriginalExtension();
            // putFileAs - лучший аналог put
            $filePath = Storage::disk('public')->putFileAs('/images', $image, $name);

            // Имя к превью
            $previewName = 'prev_' . $name;

            // Каждое изображение ссылается на post_id
            // Добавляем путь изображений и превью в БД
            Image::create([
                'path' => $filePath,
                'url' => url('/storage/' . $filePath),
                'preview_url' => url('/storage/images/' . $previewName),
                'post_id' => $post->id
            ]);

            // Подключаем расширение Intervention и сохраняем его
            \Intervention\Image\Facades\Image::make($image)->fit(100, 100)->save(storage_path('app/public/images/' . $previewName));

        }

 // (!) Для фото прописываем "Симлинк", что бы фотографии копировались из storage в public / php artisan storage:link

        return response()->json(['message' => 'success']);
    }
}
