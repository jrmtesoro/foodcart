<?php

namespace App\Http\Controllers\ApiController;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Image;
use Illuminate\Http\File;
use Illuminate\Support\Facades\Storage;
use App\Notifications\BanNotification;
use App\Notifications\CloseReport;
use App\Notifications\InvestigateReport;
use App\Notifications\StoreReport;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $reports = $this->report();

        if ($request->has('filter')) {
            if ($request->get('filter') !== null) {
                $status = $request->get('filter');
                if ($status == "active") {
                    $reports = $reports->whereIn('status', [0, 1]);
                } else if ($status == "closed") {
                    $reports = $reports->where('status', 2);
                }
            }
        }

        return response()->json([
            "success" => true,
            "data" => $reports->get()
        ]);
    }

    public function owner(Request $request)
    {
        $restaurant_id = auth()->user()->restaurant()->value('id');

        $report = $this->report()->getReport($restaurant_id);

        return response()->json([
            "success" => true,
            "data" => $report
        ]);
    }

    public function owner_show($report_code, Request $request)
    {
        $restaurant_id = auth()->user()->restaurant()->value('id');

        $report_check = $this->report()->checkReport($report_code, $restaurant_id);

        if (!$report_check) {
            return response()->json([
                "success" => false,
                "message" => "Report does not exist!"
            ]);
        }

        $report = $this->report()->where('code', $report_code)->get()->first();
        $report['suborder'] = $this->report()->find($report['id'])->suborder()->get()->first();
        $report['order'] = $this->suborder()->find($report['suborder']['id'])->order()->get()->first();
        $report['date'] = $report->created_at->format('F d, Y h:i A');
        $report['order']['date'] = $report['order']['created_at']->format('F d, Y h:i A');

        return response()->json([
            "success" => true,
            "data" => $report 
        ]);
    }

    public function admin_show($report_code, Request $request)
    {
        $report = $this->report()->where('code', $report_code)->get()->first();

        if (!$report) {
            return response()->json([
                "success" => false,
                "message" => "Report does not exist!"
            ]);
        }

        $report['date'] = $report['created_at']->format('F d, Y h:i A');
        $report['suborder'] = $this->report()->find($report['id'])->suborder()->get()->first();
        $report['suborder']['order'] = $this->suborder()->find($report['suborder']['id'])->order()->get()->first();
        $report['suborder']['itemlist'] = $this->suborder()->find($report['suborder']['id'])->itemlist()->get();
        $report['suborder']['order']['date'] = $report['suborder']['order']['created_at']->format('F d, Y h:i A');

        $report['restaurant'] = $this->report()->find($report['id'])->restaurant()->get()->first();
        $report['customer'] = $this->report()->find($report['id'])->customer()->get()->first();
        $report['customer']['user'] = $this->customer()->find($report['customer']['id'])->get()->first();

        return response()->json([
            "success" => true,
            "data" => $report
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(),[
            "report_reason" => "required",
            "sub_order_id" => "required|exists:sub_orders,id",
            "customer_id" => "required|exists:customer,id",
            "report_proof1" => "nullable|image|mimes:jpeg,png,jpg|max:5120",
            "report_proof2" => "nullable|image|mimes:jpeg,png,jpg|max:5120",
            "report_proof3" => "nullable|image|mimes:jpeg,png,jpg|max:5120"
        ]);

        if ($validator->fails()) {
            return response()->json([
                "success" => false,
                "message" => "Invalid Input",
                "errors" => $validator->errors()
            ]);
        }

        $user = $this->customer()->find($request->get('customer_id'))->user()->get()->first();
        $banned = $this->user()->find($user['id'])->ban()->get()->first();

        if ($banned) {
            return response()->json([
                "success" => false,
                "message" => "This user is already banned!"
            ]);
        }

        $code = time().rand(1000, 9999);
        $report_values = [
            "reason" => $request->get('report_reason'),
            "code" => $code
        ];

        $index = 0;
        $name_array = ['proof1', 'proof2', 'proof3'];
        foreach (['report_proof1', 'report_proof2', 'report_proof3'] as $key) {
            if ($request->hasFile($key)) {
                $image = $request->file($key);
                $path = $image->getRealPath().'.jpg';
                $file_name = time().rand(1000, 9999).'.jpg';

                $whole_pic = Image::make($image)->encode('jpg')->save($path);
                Storage::putFileAs('report', new File($path), $file_name);

                $medium = Image::make($image)->resize(300, 200)->encode('jpg')->save($path);
                Storage::putFileAs('report/medium', new File($path), $file_name);

                $thumbnail = Image::make($image)->resize(100, 100)->encode('jpg')->save($path);
                Storage::putFileAs('report/thumbnail', new File($path), $file_name);
                
                $report_values[$name_array[$index]] = $file_name;
                $index++;
            }
        }

        $report = $this->report()->create($report_values);
        
        $customer_id = $request->get('customer_id');
        $sub_order_id = $request->get('sub_order_id');
        $restaurant_id = auth()->user()->restaurant()->value('id');

        $this->customer()->find($customer_id)->report()->save($report);
        $this->suborder()->find($sub_order_id)->report()->save($report);
        $this->restaurant()->find($restaurant_id)->report()->save($report);

        $user_id = auth()->user()->id;

        //$this->user()->find($user_id)->notify(new StoreReport($code));

        if ($request->header()['origin'][0] == "app") {
            $this->logs()->create([
                "user_id" => auth()->user()->id,
                "ip_address" => $request->ip(),
                "type" => "Insert",
                "description" => "Ticket #".$report->code,
                "origin" => $request->header()['origin'][0]
            ]);
        }

        return response()->json([
            "success" => true,
            "data" => [
                "report_code" => $report->code
            ],
            "message" => "Your report has been sent to the administrator."
        ]);
    }

    public function investigate($report_code, Request $request)
    {
        $report = $this->report()->where('code', $report_code)->get()->first();
    
        if (!$report) {
            return response()->json([
                "success" => false,
                "message" => "Update Report Status Failed!"
            ]);
        }

        $status = $report['status'];

        if ($status != 0) {
            if ($status == 2) {
                return response()->json([
                    "success" => false,
                    "message" => "Report is already closed!"
                ]);
            }

            return response()->json([
                "success" => false,
                "message" => "Report is already at investigation stage!"
            ]);
        }

        $this->report()->find($report['id'])->update([
            "status" => 1
        ]);

        $restaurant = $this->report()->find($report['id'])->restaurant()->get()->first();
        $user = $this->restaurant()->find($restaurant['id'])->user()->get()->first();
        $user_id = $user['id'];
    
        //$this->user()->find($user_id)->notify(new InvestigateReport($report_code));

        if ($request->header()['origin'][0] == "app") {
            $this->logs()->create([
                "user_id" => auth()->user()->id,
                "ip_address" => $request->ip(),
                "type" => "Update",
                "description" => "Report Ticket #".$report_code." investigating",
                "origin" => $request->header()['origin'][0]
            ]);
        }

        return response()->json([
            "success" => true,
            "message" => "Report status successfully updated!"
        ]);
    }

    public function close($report_code, Request $request)
    {
        $validator = Validator::make(['report_code' => $report_code, 'report_comment' => $request->get('report_comment')],[
            "report_code" => "required|exists:report,code",
            "report_comment" => "required"
        ]);

        if ($validator->fails()) {
            return response()->json([
                "success" => false,
                "message" => "Invalid Input",
                "errors" => $validator->errors()
            ]);  
        }

        $report = $this->report()->where('code', $report_code)->get()->first();

        $status = $report['status'];

        if ($status != 1) {
            if ($status == 2) {
                return response()->json([
                    "success" => false,
                    "message" => "Report is already closed!"
                ]);
            }
        }

        $desc = "";
        if ($request->has('report_ban')) {
            $customer = $this->report()->find($report['id'])->customer()->get()->first();

            $reports = $this->customer()->find($customer['id'])->report()->get();
            
            foreach ($reports as $rep) {
                $report_id = $rep['id'];
                $this->report()->find($report_id)->update([
                    "status" => 2,
                    "comment" => $request->get('report_comment')
                ]);
            }
            
            $user = $this->customer()->find($customer['id'])->user()->get()->first();
            $ban = $this->user()->find($user['id'])->ban()->create([
                "reason" => $request->get('ban_reason')
            ]);

            //$this->user()->find($user['id'])->notify(new BanNotification());

            if ($request->header()['origin'][0] == "app") {
                $this->logs()->create([
                    "user_id" => auth()->user()->id,
                    "ip_address" => $request->ip(),
                    "type" => "Update",
                    "description" => "Customer ID : ".$customer['id']." banned",
                    "origin" => $request->header()['origin'][0]
                ]);
            }

            $desc = "Customer ID : ".$customer['id']." banned";
            
        } else {
            $this->report()->where('code', $report_code)->update([
                "status" => 2,
                "comment" => $request->get('report_comment')
            ]);
        }

        $restaurant = $this->report()->find($report['id'])->restaurant()->get()->first();
        $user = $this->restaurant()->find($restaurant['id'])->user()->get()->first();
        $user_id = $user['id'];
    
        //$this->user()->find($user_id)->notify(new CloseReport($report_code));

        $this->logs()->create([
            "user_id" => auth()->user()->id,
            "type" => "Update",
            "description" => "Report Ticket #".$report_code." closed"
        ]);

        return response()->json([
            "success" => true,
            "data" => [
                "desc" => $desc
            ],
            "message" => "Successfully closed the report!"
        ]);
    }
}
