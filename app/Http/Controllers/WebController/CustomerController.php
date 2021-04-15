<?php

namespace App\Http\Controllers\WebController;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use GuzzleHttp\Client;
use RealRashid\SweetAlert\Facades\Alert;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        return view('admin.pages.customer.customer_index');
    }

    public function show($customer_id, Request $request)
    {
        $client = new Client();

        $this->header['Authorization'] = 'Bearer '.session()->get('token');

        $req = $client->request("GET", $this->url."admin/customer/$customer_id", [
            "headers" => $this->header
        ]);

        $result = json_decode($req->getBody()->getContents(), true);

        if (!$result['success']) {
            Alert::error('Customer View Failed', $result['message']);
            return redirect()->route('customer.index');
        }
        
        return view('admin.pages.customer.customer_show')
                ->with('customer', $result['data']);
    }

    public function cancel_suborder(Request $request) {
        $client = new Client();

        $this->header['Authorization'] = 'Bearer '.session()->get('token');

        $req = $client->request("POST", $this->url."guest/suborder/cancel", [
            "headers" => $this->header,
            "form_params" => [
                "sub_order_id" => $request->get('sub_order_id')
            ]
        ]);

        $result = json_decode($req->getBody()->getContents(), true);

        if (!$result['success']) {
            Alert::error('Cancel Order Failed', $result['message']);
            return redirect()->back();
        }

        Alert::success('Order Cancelled!', $result['message']);
        return redirect()->back();
    }

    public function cancel_order(Request $request) {
        $client = new Client();

        $this->header['Authorization'] = 'Bearer '.session()->get('token');

        $req = $client->request("POST", $this->url."guest/order/cancel", [
            "headers" => $this->header,
            "form_params" => [
                "order_code" => $request->get('order_code')
            ]
        ]);

        $result = json_decode($req->getBody()->getContents(), true);

        if (!$result['success']) {
            Alert::error('Cancel Order Failed', $result['message']);
            return redirect()->back();
        }

        Alert::success('Order Cancelled!', $result['message']);
        return redirect()->back();
    }

    public function accept_suborder($suborder)
    {
        $client = new Client();

        $this->header['Authorization'] = 'Bearer '.session()->get('token');

        $req = $client->request("GET", $this->url."guest/order/$suborder/complete", [
            "headers" => $this->header
        ]);

        $result = json_decode($req->getBody()->getContents(), true);

        if (!$result['success']) {
            Alert::error('Complete Order Failed', $result['message']);
            return redirect()->back();
        }

        Alert::success('Order Completed!', $result['message']);
        return redirect()->back();
    }

    public function customer_order($customer_id, $order_code, Request $request)
    {
        $client = new Client();

        $this->header['Authorization'] = 'Bearer '.session()->get('token');

        $req = $client->request("GET", $this->url."admin/customer/$customer_id/order/$order_code", [
            "headers" => $this->header
        ]);

        $result = json_decode($req->getBody()->getContents(), true);
        
        if (!$result['success']) {
            Alert::error('Order View Failed', $result['message']);
            return redirect()->route('customer.show', ['customer_id' => $customer_id]);
        }

        return view('admin.pages.customer.customer_order_show')
                ->with('order', $result['data'])
                ->with('customer_id', $customer_id);
    }

    public function datatable(Request $request)
    {
        $client = new Client();

        $this->header['Authorization'] = 'Bearer '.session()->get('token');

        $req = $client->request("GET", $this->url."datatable/customer", [
            "headers" => $this->header,
            "query" => $request->all()
        ]);

        $result = json_decode($req->getBody()->getContents(), true);

        return $result;
    }

    public function datatable_order(Request $request)
    {
        $client = new Client();

        $this->header['Authorization'] = 'Bearer '.session()->get('token');

        $req = $client->request("GET", $this->url."datatable/customer/order", [
            "headers" => $this->header,
            "query" => $request->all()
        ]);

        $result = json_decode($req->getBody()->getContents(), true);

        return $result;
    }

    public function customer_report($customer_id, Request $request)
    {
        $client = new Client();

        $this->header['Authorization'] = 'Bearer '.session()->get('token');

        $req = $client->request("GET", $this->url."datatable/customer/$customer_id/report", [
            "headers" => $this->header,
            "query" => $request->all()
        ]);

        $result = json_decode($req->getBody()->getContents(), true);

        return $result;
    }

    public function edit()
    {
        $client = new Client();

        $this->header['Authorization'] = 'Bearer '.session()->get('token');

        $req = $client->request('GET', $this->url."profile", [
            "headers" => $this->header
        ]);

        $result = json_decode($req->getBody()->getContents(), true);

        return view('guest.pages.profile')
                ->with('user', $result['data']);
    }

    public function update(Request $request)
    {
        $client = new Client();

        $this->header['Authorization'] = 'Bearer '.session()->get('token');

        $names = ['reg_fname', 'reg_lname', 'reg_address', 'reg_contact_number', 'reg_password', 'reg_password_confirm'];
        $form_params = [];

        foreach ($request->all() as $key => $value) {
            if (in_array($key, $names) && $value !== null) {
                $form_params[$key] = $value;
            }
        }
        
        $req = $client->request('POST', $this->url."profile", [
            "headers" => $this->header,
            "form_params" => $form_params
        ]);

        $result = json_decode($req->getBody()->getContents(), true);

        if (!$result['success']) {
            Alert::error('Update Profile Failed', $result['message']);
            return redirect()->back()
                ->withInput($request->all())
                ->withErrors($result['errors']);
        }

        session([
            'fname' => $request->get('reg_fname'),
            'lname' => $request->get('reg_lname')
        ]);

        $client1 = new Client();
        $log_store = $client1->request("POST", $this->url."logs", [
            "headers" => $this->header,
            "form_params" => [
                "ip_address" => $request->ip(),
                "type" => "Update",
                "description" => "Update Profile",
                "origin" => "web"
            ]
        ]);

        Alert::success('Update Profile Success', $result['message']);
        return redirect()->route('customer.edit');
    }
}
