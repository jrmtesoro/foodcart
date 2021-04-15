<?php

namespace App\Http\Controllers\WebController;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use GuzzleHttp\Client;
use RealRashid\SweetAlert\Facades\Alert;

class AdminMenuController extends Controller
{
    public function index(Request $request)
    {
        $client = new Client();

        $this->header['Authorization'] = 'Bearer '.session()->get('token');

        $req = $client->request("get", $this->url."admin/tag", [
            "headers" => $this->header
        ]);

        $result = json_decode($req->getBody()->getContents(), true);
        
        return view('admin.pages.menu.menu_index')
                ->with('tag_list', $result['data']);
    }

    public function show($menu_id, Request $request)
    {
        $client = new Client();

        $this->header['Authorization'] = 'Bearer '.session()->get('token');

        $req = $client->request("get", $this->url."admin/menu/$menu_id", [
            "headers" => $this->header
        ]);

        $result = json_decode($req->getBody()->getContents(), true);

        if (!$result['success']) {
            Alert::error('View Menu Failed', $result['message']);
            return redirect()->route('admin.menu.index');
        }

        return view('admin.pages.menu.menu_show')
                ->with('menu_details', $result['data']);
    }

    public function datatable(Request $request)
    {
        $client = new Client();

        $this->header['Authorization'] = 'Bearer '.session()->get('token');

        $req = $client->request("get", $this->url."datatable/admin/menu", [
            "headers" => $this->header,
            "query" => $request->all()
        ]);

        $result = json_decode($req->getBody()->getContents(), true);

        return $result;
    }
}
