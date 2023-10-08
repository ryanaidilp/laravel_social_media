<?php

namespace App\Http\Controllers\Api;

use App\Models\Post;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Xetaio\Mentions\Parser\MentionParser;

class PostController extends Controller
{
    public function create(Request $request): JsonResponse
    {
        try {
            DB::beginTransaction();
            $file = $request->file('image');
            $fileName = $file->getClientOriginalName() ?? Str::random(10);
            $extension = $file->getClientOriginalExtension();
            $path = "public/uploads";
            $name = "{$fileName}.{$extension}";
            $file->storeAs(
                $path,
                $name,
            );
            $post = Post::create([
                'user_id' => auth()->user()->id,
                'description' => $request->get('description'),
                'image' => $path . '/' . $name,
            ]);
            DB::commit();

            $parser = new MentionParser($post);
            $content = $parser->parse($post->description);

            $post->description = $content;
            $post->save();

            return response()->json([
                'message' => 'Post created successfully!',
                'success' => false,
                'data' => $post->refresh()->load('user'),
                'error' => null,
            ], status: 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(
                data: [
                    'message' => 'There is a failure when try to create post!',
                    'success' => false,
                    'data' => null,
                    'error' => [
                        'code' => $th->getCode(),
                        'message' => $th->getMessage(),
                    ],
                ],
                status: 400,
            );
        }
    }
}
