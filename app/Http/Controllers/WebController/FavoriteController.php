<?php

namespace App\Http\Controllers\WebController;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use GuzzleHttp\Client;
use RealRashid\SweetAlert\Facades\Alert;

class FavoriteController extends Controller
{
    public function index()
    {
        $client = new Client();

        $this->header['Authorization'] = 'Bearer '.session()->get('token');

        $req = $client->request('get', $this->url."guest/favorite", [
            "headers" => $this->header,
        ]);

        $result = json_decode($req->getBody()->getContents(), true);

        return view('guest.pages.favorite')
                ->with('favorites', $result['data']);
    }

    public function store(Request $request)
    {
        $client = new Client();

        $this->header['Authorization'] = 'Bearer '.session()->get('token');

        $req = $client->request('POST', $this->url."guest/favorite", [
            "headers" => $this->header,
            "form_params" => [
                "restaurant_slug" => $request->get('restaurant_slug')
            ]
        ]);

        $result = json_decode($req->getBody()->getContents(), true);

        $client1 = new Client();
        $log_store = $client1->request("POST", $this->url."logs", [
            "headers" => $this->header,
            "form_params" => [
                "ip_address" => $request->ip(),
                "type" => "Insert",
                "description" => "Added Restaurant Slug : ".$request->get('restaurant_slug')." to favorite",
                "origin" => "web"
            ]
        ]);


        return response()->json($result);
    }

    public function destroy($restaurant_slug, Request $request)
    {
        $client = new Client();

        $this->header['Authorization'] = 'Bearer '.session()->get('token');

        $req = $client->request('delete', $this->url."guest/favorite/$restaurant_slug", [
            "headers" => $this->header
        ]);

        $result = json_decode($req->getBody()->getContents(), true);

        if ($request->ajax()) {
            return response()->json($result);
        }

        if (!$result['success']) {
            Alert::error('Remove Favorite Failed', $result['message']);
            return redirect()->route('favorite.index');
        }

        $client1 = new Client();
        $log_store = $client1->request("POST", $this->url."logs", [
            "headers" => $this->header,
            "form_params" => [
                "ip_address" => $request->ip(),
                "type" => "Delete",
                "description" => "Deleted Restaurant Slug : ".$restaurant_slug." to favorite",
                "origin" => "web"
            ]
        ]);

        Alert::success('Remove Favorite Success', $result['message']);
        return redirect()->route('favorite.index');
    }
}
