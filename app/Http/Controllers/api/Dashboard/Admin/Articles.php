<?php

    namespace App\Http\Controllers\api\Dashboard\Admin;

    use App\Article;
    use App\Http\Controllers\Controller;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Validator;
    use Tymon\JWTAuth\JWTAuth;
    use Illuminate\Support\Facades\DB;
    use File;

    class Articles extends Controller
    { 

        public function Add(Request $request)
        {
            $validator = Validator::make($request->all(), [ 'title' => 'required','body' => 'required','video' => 'required' ]);

            if($validator->fails()){ return response()->json([ "errs" => $validator->errors() ], 202); }

            $post = new Article;
            $post->title = $request->get('title');
            $post->body = $request->get('body');
            $post->video = $request->get('video');
            $post->save();

            return response()->json(null, 200);
        }

        public function get()
        {
            $get = Article::all();
            return ResultNoSB($get, 200);
        } 

        public function get_by_id($id)
        {
            $get = Article::where('id', $id)->first();
            if (!$get) { return response()->json(null, 203); }
            return Result($get, 200);
        }

        public function DeleteArticle(Request $request)
        {
            $id = $request->only('id')['id'];
            $posts = Article::find($id)->delete();
            return response()->json(null, 200);
        }

        public function UpdateArticle(Request $request)
        {
            $validator = Validator::make($request->all(), [
                'id' => 'required|exists:articles,id',
                'title' => 'required',
                'body' => 'required',
                'video' => 'required',
            ]);

            if($validator->fails()){ return response()->json([ "errs" => $validator->errors() ], 202); }

            $posts = Article::where('id', $request->get('id'))->update($request->all());
            return response()->json(null, 200);
        }

    }
