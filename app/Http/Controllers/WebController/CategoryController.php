<?php

namespace App\Http\Controllers\WebController;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use GuzzleHttp\Client;
use RealRashid\SweetAlert\Facades\Alert;
use Carbon\Carbon;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        return view('owner.pages.category.category_index');
    }

    public function datatable(Request $request)
    {
        $client = new Client();

        $this->header['Authorization'] = 'Bearer '.session()->get('token');

        $req = $client->request("GET", $this->url."datatable/category", [
            "headers" => $this->header,
            "query" => $request->all()
        ]);

        $result = json_decode($req->getBody()->getContents(), true);

        return $result;
    }

    public function store()
    {
        
    }

    public function update($category, Request $request)
    { 
        $client = new Client();

        $form_params['category_name'] = $request->get('category_name');

        $this->header['Authorization'] = 'Bearer '.session()->get('token');

        $req = $client->request('POST', $this->url."owner/category/".$category, [
            "headers" => $this->header,
            "form_params" => $form_params
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
                    "message" => $result['message'],
                    "errors" => $result['errors']
                ]);
            }

            $client1 = new Client();
            $log_store = $client1->request("POST", $this->url."logs", [
                "headers" => $this->header,
                "form_params" => [
                    "ip_address" => $request->ip(),
                    "type" => "Update",
                    "description" => "Category name : ".$request->get('category_name'),
                    "origin" => "web"
                ]
            ]);

            return response()->json([
                "success" => true,
                "message" => $result['message']
            ]);
        }
    }

    public function destroy($category, Request $request)
    {
        $client = new Client();

        $this->header['Authorization'] = 'Bearer '.session()->get('token');

        $req = $client->request('delete', $this->url."owner/category/".$category, [
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
                $response = [
                    "success" => false,
                    "message" => $result['message']
                ];
                if ($result['message'] == "This category is being used.") {
                    $response['footer'] = 'Click <a class="px-2" href="'.route('menu.index').'?cat='.$category.'&show=both">HERE</a>to see what items are using this category.';
                }

                return response()->json($response);
            }

            $client1 = new Client();
            $log_store = $client1->request("POST", $this->url."logs", [
                "headers" => $this->header,
                "form_params" => [
                    "ip_address" => $request->ip(),
                    "type" => "Delete",
                    "description" => "Category ID : ".$category,
                    "origin" => "web"
                ]
            ]);

            return response()->json([
                "success" => true,
                "message" => $result['message']
            ]);
        }
    }

    public function restore($category, Request $request)
    {
        $client = new Client();

        $this->header['Authorization'] = 'Bearer '.session()->get('token');

        $req = $client->request('post', $this->url."owner/category/$category/restore", [
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
                    "description" => "Category ID : ".$category." restored",
                    "origin" => "web"
                ]
            ]);
            
            return response()->json([
                "success" => true,
                "message" => $result['message']
            ]);
        }
    }
}
