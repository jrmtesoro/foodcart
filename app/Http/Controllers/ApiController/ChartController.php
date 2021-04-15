<?php

namespace App\Http\Controllers\ApiController;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use Carbon\Carbon;

class ChartController extends Controller
{
    public function totalOrders(Request $request)
    {
        $restaurant_id = auth()->user()->restaurant()->value('id');

        $year = Carbon::now()->format('Y');
        if ($request->has('year')) {
            $year = $request->get('year');
        }
        $sub_orders = $this->restaurant()->totalOrders($restaurant_id, $year);

        $month_array = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        $result = [];
        $index = 0;
        $month_now = Carbon::now()->format('M');
        foreach ($month_array as $month) {
            $result['month'][$index] = $month;
            $result['data'][$index] = 0;
            foreach ($sub_orders as $m => $v) {
                if ($m == $month) {
                    foreach ($v as $order) {
                        $result['data'][$index]++;
                    }
                }
            }
            $index++;

            if ($month == $month_now) {
                break;
            }
        }

        return response()->json([
            "success" => true,
            "data" => $result
        ]);
    }

    public function totalSales(Request $request)
    {
        $restaurant_id = auth()->user()->restaurant()->value('id');

        $year = Carbon::now()->format('Y');
        if ($request->has('year')) {
            $year = $request->get('year');
        }
        $sub_orders = $this->restaurant()->totalSales($restaurant_id, $year);

        $month_array = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        $result = [];
        $index = 0;
        $month_now = Carbon::now()->format('M');     
        
        foreach ($month_array as $month) {
            $result['month'][$index] = $month;
            $result['data'][$index] = 0;
            foreach ($sub_orders as $m => $v) {
                if ($m == $month) {
                    foreach ($v as $order) {
                        $result['data'][$index] += $order->total;
                    }
                }
            }
            $index++;

            if ($month == $month_now) {
                break;
            }
        }

        return response()->json([
            "success" => true,
            "data" => $result
        ]);
    }
}
