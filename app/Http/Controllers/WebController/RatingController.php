<?php

namespace App\Http\Controllers\WebController;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use GuzzleHttp\Client;

class RatingController extends Controller
{
    public function store(Request $request)
    {
        $client = new Client();

        $this->header['Authorization'] = 'Bearer '.session()->get('token');
        
        $req = $client->request("POST", $this->url."guest/rating", [
            "headers" => $this->header,
            "form_params" => [
                "restaurant_slug" => $request->get('restaurant_slug'),
                "rating" => $request->get('rating')
            ]
        ]);

        $result = json_decode($req->getBody()->getContents(), true);

        $client1 = new Client();
        $log_store = $client1->request("POST", $this->url."logs", [
            "headers" => $this->header,
            "form_params" => [
                "ip_address" => $request->ip(),
                "type" => "Insert",
                "description" => "Rating ".$request->get('rating')." for Restaurant Slug : ".$request->get('restaurant_slug'),
                "origin" => "web"
            ]
        ]);

        return response()->json($result);
    }
}
