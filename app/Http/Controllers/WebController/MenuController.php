<?php

namespace App\Http\Controllers\WebController;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use GuzzleHttp\Client;
use RealRashid\SweetAlert\Facades\Alert;
use Carbon\Carbon;

class MenuController extends Controller
{
    public function index(Request $r)
    {
        $client = new Client();

        $this->header['Authorization'] = 'Bearer '.session()->get('token');

        $request = $client->request("GET", $this->url."owner/category", [
            "headers" => $this->header
        ]);

        $result = json_decode($request->getBody()->getContents(), true);

        return view('owner.pages.menu.menu_index')
            ->with('category_list', $result['data']);

    }

    public function datatable(Request $request)
    {
        $client = new Client();

        $this->header['Authorization'] = 'Bearer '.session()->get('token');

        $request = $client->request("GET", $this->url."datatable/menu", [
            "headers" => $this->header,
            "query" => $request->all()
        ]);

        $result = json_decode($request->getBody()->getContents(), true);

        return $result;
    }

    public function create()
    {
        $client = new Client();

        $this->header['Authorization'] = 'Bearer '.session()->get('token');
        $request = $client->request("GET", $this->url."owner/menu/create", [
            "headers" => $this->header,
        ]);

        $result = json_decode($request->getBody()->getContents(), true);

        $tag_list = [];
        foreach ($result['data']['tag_list'] as $tag) {
            $tag_list[]['name'] = $tag;
        }

        $result['data']['tag_list'] = $tag_list;

        return view('owner.pages.menu.menu_create')
                ->with('data', $result['data']);
    }

    public function store(Request $request)
    {
        foreach ($request->all() as $key => $value) 
        {
            $temp_array = [];
            $temp_array['name'] = $key;
            if ($key == "menu_image") 
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

        $this->header['Authorization'] = 'Bearer '.session()->get('token');
        $req = $client->request("POST", $this->url."owner/menu", [
            "headers" => $this->header,
            "multipart" => $multipart
        ]);

        $result = json_decode($req->getBody()->getContents(), true);

        if (empty($result)) {
            Alert::error('Add Item Failed', 'Server Error!');
            return redirect()->route('menu.index');;
        }
        if (!$result['success']) {
            Alert::error('Add Item Failed', $result['message']);

            if ($result['message'] == 'Invalid Input') {
                return redirect()->back()
                    ->withErrors($result['errors'])
                    ->withInput($request->all());
            }
            
            return redirect()->back()
                ->withInput($request->all());
        }

        $client1 = new Client();
        $log_store = $client1->request("POST", $this->url."logs", [
            "headers" => $this->header,
            "form_params" => [
                "ip_address" => $request->ip(),
                "type" => "Insert",
                "description" => "Menu Name : ".$request->get('menu_name'),
                "origin" => "web"
            ]
        ]);

        Alert::Success('Add Item Success', $result['message']);
        return redirect('owner/menu');
    }

    public function show($menu, Request $r)
    {
        $client = new Client();

        $this->header['Authorization'] = 'Bearer '.session()->get('token');

        $request = $client->request("GET", $this->url."owner/menu/$menu", [
            "headers" => $this->header,
        ]);

        $result = json_decode($request->getBody()->getContents(), true);

        if (empty($result)) {
            Alert::error('View Item Failed', 'Server Error!');
            return redirect()->route('menu.index');
        }
        else if (!$result['success']) {
            Alert::error('View Item Failed', $result['message']);
            return redirect()->route('menu.index');
        }

        return view('owner.pages.menu.menu_show')->with('menu_details', $result['data']);
    }

    public function edit($menu)
    {
        $client = new Client();

        $this->header['Authorization'] = 'Bearer '.session()->get('token');

        $request = $client->request("GET", $this->url."owner/menu/$menu/edit", [
            "headers" => $this->header,
        ]);

        $result = json_decode($request->getBody()->getContents(), true);

        if (empty($result)) {
            Alert::error('Edit Item Failed', 'Server Error!');
            return redirect()->route('menu.index');
        } else if (!$result['success']) {
            Alert::error('Edit Item Failed', $result['message']);
            return redirect()->route('menu.index');
        }

        $tag_list = [];
        foreach ($result['data']['tag_list'] as $tag) {
            $tag_list[]['name'] = $tag;
        }

        if (!empty($result['data']['menu_details']['tag'])) {
            $tag_list1 = [];
            foreach ($result['data']['menu_details']['tag'] as $tag) {
                $tag_list1[] = $tag['name'];
            }

            $result['data']['menu_details']['tag_list1'] = $tag_list1;
        }

        return view('owner.pages.menu.menu_edit')
                ->with('menu_details', $result['data']['menu_details'])
                ->with('tag_list', $tag_list)
                ->with('category_list', $result['data']['category_list']);
    }

    public function update($menu, Request $request)
    {
        $multipart = [];
        foreach ($request->all() as $key => $value) 
        {
            $temp_array = [];
            $temp_array['name'] = $key;
            if ($key == "menu_image") 
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

        $this->header['Authorization'] = 'Bearer '.session()->get('token');

        $req = $client->request("POST", $this->url."owner/menu/".$menu, [
            "headers" => $this->header,
            "multipart" => $multipart
        ]);

        $result = json_decode($req->getBody()->getContents(), true);

        if (empty($result)) {
            Alert::error('Update Item Failed', 'Server Error!');
            return redirect()->route('menu.index');
        } else if (!$result['success']) {
            Alert::error('Update Item Failed', $result['message']);

            if ($result['message'] == "Menu doesn't exist!") {
                return redirect()->route('menu.index');
            } else if ($result['message'] == "Invalid Input") {
                return redirect()->back()
                    ->withErrors($result['errors'])
                    ->withInput($request->all());
            } 
            return redirect()->back()
                    ->withInput($request->all());
        }

        $client1 = new Client();
        $log_store = $client1->request("POST", $this->url."logs", [
            "headers" => $this->header,
            "form_params" => [
                "ip_address" => $request->ip(),
                "type" => "Updated",
                "description" => "Menu Name : ".$request->get('menu_name'),
                "origin" => "web"
            ]
        ]);

        Alert::Success('Update Item Success', $result['message']);
        return redirect()->route('menu.index');
    }

    public function destroy($menu, Request $request)
    {
        $client = new Client();

        $this->header['Authorization'] = 'Bearer '.session()->get('token');

        $req = $client->request('delete', $this->url."owner/menu/".$menu, [
            "headers" => $this->header
        ]);

        $result = json_decode($req->getBody()->getContents(), true);

        if ($request->ajax()) {
            if (empty($result)) {
                return response()->json([
                    "success" => false,
                    "message" => "Server Error!"
                ]);
            } else if (!$result['success']) {
                return response()->json([
                    "success" => false,
                    "message" => $result['message']
                ]);
            }

            $client1 = new Client();
            $log_store = $client1->request("POST", $this->url."logs", [
                "headers" => $this->header,
                "form_params" => [
                    "ip_address" => $request->ip(),
                    "type" => "Delete",
                    "description" => "Menu ID : ".$menu,
                    "origin" => "web"
                ]
            ]);

            return response()->json([
                "success" => true,
                "message" => $result['message']
            ]);
        }

        if (empty($result)) {
            Alert::error('Hide Item Failed', 'Server Error!');
            return redirect()->route('menu.index');
        } else if (!$result['success']) {
            Alert::error('Hide Item Failed', $result['message']);
            return redirect()->route('menu.index');
        }

        $client1 = new Client();
        $log_store = $client1->request("POST", $this->url."logs", [
            "headers" => $this->header,
            "form_params" => [
                "ip_address" => $request->ip(),
                "type" => "Delete",
                "description" => "Menu ID : ".$menu,
                "origin" => "web"
            ]
        ]);

        Alert::Success('Hide Item Success', $result['message']);
        return redirect()->route('menu.index');
    }

    public function restore($menu, Request $request)
    {
        $client = new Client();

        $this->header['Authorization'] = 'Bearer '.session()->get('token');

        $req = $client->request('post', $this->url."owner/menu/$menu/restore", [
            "headers" => $this->header
        ]);

        $result = json_decode($req->getBody()->getContents(), true);

        if ($request->ajax()) {
            if (empty($result)) {
                return response()->json([
                    "success" => false,
                    "message" => "Server Error!"
                ]);
            } else if (!$result['success']) {
                return response()->json([
                    "success" => false,
                    "message" => $result['message']
                ]);
            }

            $client1 = new Client();
            $log_store = $client1->request("POST", $this->url."logs", [
                "headers" => $this->header,
                "form_params" => [
                    "ip_address" => $request->ip(),
                    "type" => "Update",
                    "description" => "Menu ID : ".$menu." restored",
                    "origin" => "web"
                ]
            ]);
            
            return response()->json([
                "success" => true,
                "message" => $result['message']
            ]);
        }

        if (empty($result)) {
            Alert::error('Show Item Failed', 'Server Error!');
            return redirect()->route('menu.index');
        } else if (!$result['success']) {
            Alert::error('Show Item Failed', $result['message']);
            return redirect()->route('menu.index');
        }

        $client1 = new Client();
        $log_store = $client1->request("POST", $this->url."logs", [
            "headers" => $this->header,
            "form_params" => [
                "ip_address" => $request->ip(),
                "type" => "Update",
                "description" => "Menu ID : ".$menu." restored",
                "origin" => "web"
            ]
        ]);

        Alert::Success('Show Item Success', $result['message']);
        return redirect()->route('menu.index');
    }
}
