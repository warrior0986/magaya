<?php

namespace App\Http\Controllers;

use App\Http\Resources\WeatherRequestCollection;
use App\Http\Resources\WeatherRequestResource;
use App\Models\WeatherRequest;
use App\Repositories\ExternalWeatherRequestRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WeatherRequestController extends Controller
{
    protected $external_request;

    public function __construct(ExternalWeatherRequestRepositoryInterface $external_request)
    {
        $this->external_request = $external_request;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $list = WeatherRequest::where('user_id', Auth::id())->paginate(20);
        return new WeatherRequestCollection($list);
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
    public function store(Request $request)
    {
        $data = $request->validate([
            'city_name' => 'string|required'
        ]);

        $weatherRequest = WeatherRequest::create([
            'user_id' => Auth::id()
        ]);

        $this->external_request->get($weatherRequest, $data['city_name']);
        
        return new WeatherRequestResource($weatherRequest);

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\WeatherRequest  $weatherRequest
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $weatherRequest = WeatherRequest::where(['id' => $id, 'user_id' => Auth::id()])->first();

        if ($weatherRequest) {
            return new WeatherRequestResource($weatherRequest);
        } else {
            abort(404, 'Not Found');
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\WeatherRequest  $weatherRequest
     * @return \Illuminate\Http\Response
     */
    public function edit(WeatherRequest $weatherRequest)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\WeatherRequest  $weatherRequest
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, WeatherRequest $weatherRequest)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\WeatherRequest  $weatherRequest
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $weatherRequest = WeatherRequest::where(['id' => $id, 'user_id' => Auth::id()])->first();

        if ($weatherRequest) {
            $weatherRequest->delete();
            return response()->json(null, 204);
        } else {
            abort(404);
        }
    }
}
