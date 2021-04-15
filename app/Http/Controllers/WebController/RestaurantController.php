<?php

namespace App\Http\Controllers\WebController;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use GuzzleHttp\Client;
use RealRashid\SweetAlert\Facades\Alert;

class RestaurantController extends Controller
{
    public function index(Request $request)
    {
        return view('admin.pages.restaurant.restaurant_index');
    }

    public function show($restaurant_id, Request $request)
    {
        $client = new Client();

        $this->header['Authorization'] = 'Bearer '.session()->get('token');

        $req = $client->request("get", $this->url."admin/restaurant/$restaurant_id", [
            "headers" => $this->header
        ]);

        $result = json_decode($req->getBody()->getContents(), true);

        if (!$result['success']) {
            Alert::error('View Restaurant Failed', $result['message']);
            return redirect()->route('admin.restaurant.index');
        }
        
        return view('admin.pages.restaurant.restaurant_show')
                ->with('category_list', $result['data']['category_list'])
                ->with('total_orders', $result['data']['total_orders'])
                ->with('total_sales', $result['data']['total_sales'])
                ->with('restaurant', $result['data']['restaurant'])
                ->with('user_id', $result['data']['user_id'])
                ->with('banned', $result['data']['banned']);
    }

    public function restaurant_menu($restaurant_id, $menu_id, Request $request)
    {
        $client = new Client();

        $this->header['Authorization'] = 'Bearer '.session()->get('token');

        $req = $client->request("get", $this->url."admin/restaurant/$restaurant_id/menu/$menu_id", [
            "headers" => $this->header
        ]);

        $result = json_decode($req->getBody()->getContents(), true);

        if (!$result['success']) {
            if ($result['message'] == "Restaurant does not exist!") {
                Alert::error('View Restaurant Failed', $result['message']);
                return redirect()->route('admin.restaurant.index');
            } else {
                Alert::error('View Item Failed', $result['message']);
                return redirect()->route('admin.restaurant.index');
            }
        }

        return view('admin.pages.restaurant.restaurant_menu_show')
                ->with('menu_details', $result['data']);
    }

    public function menu_datatable(Request $request)
    {
        $client = new Client();

        $this->header['Authorization'] = 'Bearer '.session()->get('token');

        $req = $client->request("get", $this->url."datatable/restaurant/menu", [
            "headers" => $this->header,
            "query" => $request->all()
        ]);

        $result = json_decode($req->getBody()->getContents(), true);

        return $result;
    }

    public function datatable(Request $request)
    {
        $client = new Client();

        $this->header['Authorization'] = 'Bearer '.session()->get('token');

        $req = $client->request("get", $this->url."datatable/restaurant", [
            "headers" => $this->header,
            "query" => $request->all()
        ]);

        $result = json_decode($req->getBody()->getContents(), true);

        return $result;
    }

    public function search(Request $request)
    {
        $client = new Client();

        $req = $client->request("POST", $this->url."restaurant", [
            "form_params" => $request->all()
        ]);

        $result = json_decode($req->getBody()->getContents(), true);

        return view('guest.pages.restaurant')
                ->with('input', $request->all())
                ->with('tags', $result['tags'])
                ->with('restaurants', $result['data']);
    }

    public function create()
    {
        return view('guest.pages.partnership');
    }

    public function store(Request $request)
    {
        foreach ($request->all() as $key => $value) 
        {
            $temp_array = [];
            $temp_array['name'] = $key;
            if (in_array($key, ['reg_permit_1', 'reg_permit_2', 'reg_permit_3'])) 
            {
                $temp_array['contents'] = fopen($value->getPathname(), 'r');
                $temp_array['Mime-Type'] = $value->getmimeType();
                $temp_array['filename'] = $value->getClientOriginalName();
            }
            else
            {
                $temp_array['contents'] = $value;
            }

            $multipart[] = $temp_array;
        }

        $client = new Client();

        $req = $client->request("POST", $this->url."partner", [
            "headers" => $this->header,
            "multipart" => $multipart
        ]);

        $result = json_decode($req->getBody()->getContents(), true);
        
        if (!$result['success']) {
            Alert::error('Sending Application Error', $result['message']);
            return redirect()->route('partner')
                    ->withInput($request->all())
                    ->withErrors($result['errors']);
        }

        Alert::success('Application Sent', $result['message']);
        return redirect()->route('home');
    }

    public function restaurant_order($restaurant_id, Request $request)
    {
        $client = new Client();

        $this->header['Authorization'] = 'Bearer '.session()->get('token');

        $req = $client->request("get", $this->url."datatable/restaurant/$restaurant_id/order", [
            "headers" => $this->header,
            "query" => $request->all()
        ]);

        $result = json_decode($req->getBody()->getContents(), true);

        return $result;
    }

    public function restaurant_report($restaurant_id, Request $request)
    {
        $client = new Client();

        $this->header['Authorization'] = 'Bearer '.session()->get('token');

        $req = $client->request("get", $this->url."datatable/restaurant/$restaurant_id/report", [
            "headers" => $this->header,
            "query" => $request->all()
        ]);

        $result = json_decode($req->getBody()->getContents(), true);

        return $result;
    }

    public function restaurant_order_view($restaurant_id, $sub_order_id, Request $request)
    {
        $client = new Client();

        $this->header['Authorization'] = 'Bearer '.session()->get('token');

        $req = $client->request("GET", $this->url."admin/restaurant/$restaurant_id/order/$sub_order_id", [
            "headers" => $this->header
        ]);

        $result = json_decode($req->getBody()->getContents(), true);

        if (!$result['success']) {
            Alert::error('View Order Failed', $result['message']);
            return redirect()->route('admin.restaurant.show', ["restaurant_id" => $restaurant_id]);
        }

        return view('admin.pages.restaurant.restaurant_order')
                ->with('order', $result['data']);
    }
}
