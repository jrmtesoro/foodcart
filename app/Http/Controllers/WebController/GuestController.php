<?php

namespace App\Http\Controllers\WebController;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use GuzzleHttp\Client;

class GuestController extends Controller
{
    public function home(Request $request)
    {
        $client = new Client();

        if (session()->has('token')) {
            $this->header['Authorization'] = 'Bearer '.session()->get('token');
            $request = $client->request("GET", $this->url."home1", [
                "headers" => $this->header,
                "http_errors" => false
            ]);
        } else {
            $request = $client->request("GET", $this->url."home", [
                "headers" => $this->header
            ]);
        }

        $result = json_decode($request->getBody()->getContents(), true);

        return view('guest.pages.home')->with('restaurants', $result['data']);
    }

    public function checkout()
    {
        $client = new Client();

        $this->header['Authorization'] = 'Bearer '.session()->get('token');

        $request = $client->request("GET", $this->url."checkout", [
            "headers" => $this->header
        ]);

        $result = json_decode($request->getBody()->getContents(), true);
        
        if (!$result['success']) {
            return redirect()->route('home');
        }

        $indx = 0;
        foreach ($result['data'] as $restaurant) {
            $contact = $restaurant['contact_number'];
            $contact_number = "";
            if (strlen($contact) == 7) {
                $contact_number = substr($contact, 0, 3)."-".substr($contact, 2, 2)."-".substr($contact, 4, 2);
            } else {
                $contact_number = "+63-".substr($contact, 0, 4)."-".substr($contact, 3, 4)."-".substr($contact, 7, 3);
            }
            $result['data'][$indx]['contact_number'] = $contact_number;

            $indx++;
        }

        return view('guest.pages.checkout')
            ->with('data', $result['data'])
            ->with('grand_total', $result['grand_total'])
            ->with('total_cooking_time', $result['total_cooking_time'])
            ->with('total_flat_rate', $result['total_flat_rate'])
            ->with('validation_rules', $result['validation_rules'])
            ->with('validation_message', $result['validation_message']);
    }

    public function guest_restaurant($slug)
    {
        $client = new Client();

        $request = $client->request("GET", $this->url."restaurant/$slug", [
            "headers" => $this->header
        ]);

        $result = json_decode($request->getBody()->getContents(), true);

        if (!$result['success']) {
            return abort(404);
        }

        if(session()->has('token')) {
            $this->header['Authorization'] = 'Bearer '.session()->get('token');

            //favorite
            $client1 = new Client();
            $request1 = $client1->request("GET", $this->url."guest/favorite", [
                "headers" => $this->header
            ]);
            $result1 = json_decode($request1->getBody()->getContents(), true);

            //rating
            $client2 = new Client();
            $request2 = $client2->request("GET", $this->url."guest/rating", [
                "headers" => $this->header
            ]);
            $result2 = json_decode($request2->getBody()->getContents(), true);

            $favorites = $result1['data'];

            $ratings = $result2['data'];

            $is_favorite = false;
            foreach ($favorites as $favorite) {
                if ($result['data']['slug'] == $favorite['slug']) {
                    $is_favorite = true;
                }
            }

            $rate = $result['data']['rating'];
            foreach ($ratings as $rating) {
                if ($result['data']['slug'] == $rating['slug']) {
                    $rate = $rating['rate'];
                }
            }

            $result['data']['favorite'] = $is_favorite;
            $result['data']['rating'] = $rate;
        }

        $contact = $result['data']['contact_number'];
        $contact_number = "";
        if (strlen($contact) == 7) {
            $contact_number = substr($contact, 0, 3)."-".substr($contact, 2, 2)."-".substr($contact, 4, 2);
        } else {
            $contact_number = "+63-".substr($contact, 0, 4)."-".substr($contact, 3, 4)."-".substr($contact, 7, 3);
        }
        $result['data']['contact_number'] = $contact_number;

        return view('guest.pages.restaurant.restaurant_index')
                ->with('details', $result['data']);
    }
}
