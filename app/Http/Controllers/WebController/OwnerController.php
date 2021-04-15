<?php

namespace App\Http\Controllers\WebController;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use GuzzleHttp\Client;
use RealRashid\SweetAlert\Facades\Alert;

class OwnerController extends Controller
{
    public function dashboard()
    {
        return view('owner.pages.dashboard');
    }

    public function profile(Request $r)
    {
        $client = new Client();

        $this->header['Authorization'] = 'Bearer '.session()->get('token');

        $request = $client->request("GET", $this->url."owner/profile", [
            "headers" => $this->header
        ]);

        $result = json_decode($request->getBody()->getContents(), true);
        
        return view('owner.pages.profile')
                ->with('restaurant', $result['data']['restaurant'])
                ->with('user', $result['data']['user']);
    }

    public function profileUpdate(Request $request)
    {
        $multipart = [];
        $textfields = ['flat_rate', '24hours', 'address', 'contact_number', 'image_name', 'eta', 'open_time', 'close_time'];
        foreach ($request->all() as $key => $value) 
        {
            if (in_array($key, $textfields)) {
                $temp_array = [];
                $temp_array['name'] = $key;
                if ($key == "image_name") 
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
        }

        $client = new Client();

        $this->header['Authorization'] = 'Bearer '.session()->get('token');

        $req = $client->request("POST", $this->url."owner/profile", [
            "headers" => $this->header,
            "multipart" => $multipart
        ]);

        $result = json_decode($req->getBody()->getContents(), true);

        if (!$result['success']) {
            Alert::error('Update Profile Failed!', $result['message']);
            return redirect()->back()
                    ->with('restaurant_error', $result['errors']);
        }

        $client1 = new Client();
        $log_store = $client1->request("POST", $this->url."logs", [
            "headers" => $this->header,
            "form_params" => [
                "ip_address" => $request->ip(),
                "type" => "Update",
                "description" => "Restaurant Profile",
                "origin" => "web"
            ]
        ]);
        
        Alert::success('Update Profile Success!', $result['message']);
        return redirect()->route('owner.profile');
    }

    public function info(Request $request)
    {
        if (!session()->get('info')) {
            return redirect()->route('owner.dashboard');
        }
        
        return view('owner.pages.info');
    }

    public function updateInfo(Request $request)
    {
        $multipart = [];
        foreach ($request->all() as $key => $value) {
            $temp_array = [];
            $temp_array['name'] = $key;
            if ($key == "image_name") {
                $temp_array['contents'] = fopen($value->getPathname(), 'r');
                $temp_array['Mime-Type'] = $value->getmimeType();
                $temp_array['filename'] = $value->getClientOriginalName();
            } else {
                $temp_array['contents'] = $value;
            }

            $multipart[] = $temp_array;
        }

        $client = new Client();

        $this->header['Authorization'] = 'Bearer '.session()->get('token');

        $req = $client->request("POST", $this->url."owner/info", [
            "headers" => $this->header,
            "multipart" => $multipart
        ]);

        $result = json_decode($req->getBody()->getContents(), true);

        

        if (!$result['success']) {
            Alert::error('Update Restaurant Information Failed!', $result['message']);
            return redirect()->back()
                    ->withInput($request->all())
                    ->withErrors($result['errors']);
        }

        session()->forget('info');
        session()->put('restaurant_image', $result['data']['image_name']);

        $client1 = new Client();
        $log_store = $client1->request("POST", $this->url."logs", [
            "headers" => $this->header,
            "form_params" => [
                "ip_address" => $request->ip(),
                "type" => "Update",
                "description" => "Restaurant Profile",
                "origin" => "web"
            ]
        ]);
        
        Alert::success('Update Restaurant Information Success!', $result['message']);
        return redirect()->route('owner.dashboard');
    }
}
