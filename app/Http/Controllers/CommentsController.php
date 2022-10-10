<?php

namespace App\Http\Controllers;

use App\Http\Resources\CommentCollection;
use App\Http\Resources\WeatherRequestCollection;
use App\Models\Comments;
use App\Models\WeatherRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($weather_request_id)
    {
        $weatherRequest = WeatherRequest::where(['id' => $weather_request_id, 'user_id' => Auth::id()])
            ->with('comments')
            ->paginate(20);

        return new WeatherRequestCollection($weatherRequest);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $weather_request_id)
    {
        $data = $request->validate([
            'body' => 'required|string'
        ]);
        
        $weatherRequest = WeatherRequest::where(['id' => $weather_request_id, 'user_id' => Auth::id()])->first();

        if ($weatherRequest) {
            $comment = new Comments(['body' => $data['body']]);
            $weatherRequest->comments()->save($comment);
            return response(null, 201);
        } else {
            abort(404);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Comments  $comments
     * @return \Illuminate\Http\Response
     */
    public function show(Comments $comments)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Comments  $comments
     * @return \Illuminate\Http\Response
     */
    public function edit(Comments $comments)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Comments  $comments
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Comments $comments)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Comments  $comments
     * @return \Illuminate\Http\Response
     */
    public function destroy($weather_request, $comment)
    {
        $weatherRequest = WeatherRequest::with('comments')->where([
            'user_id' => Auth::id(),
            'id' => $weather_request,
        ])->whereHas('comments', function($q) use($comment) {
            $q->where('id', $comment);
        })->first();
        
        if ($weatherRequest) {
            $weatherRequest->comments()->delete();
            return response()->json(null, 204);
        } else {
            abort(404);
        }
    }
}
