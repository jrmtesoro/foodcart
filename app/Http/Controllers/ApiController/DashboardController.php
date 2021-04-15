<?php

namespace App\Http\Controllers\ApiController;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use DB;

class DashboardController extends Controller
{
    public function admin_chart($filter)
    {
        $now = Carbon::now();
        $result = array(
            "labels" => [],
            "sales" => [],
            "orders" => [],
            "count" => []
        );

        if ($filter == "today") {
            $date_today = $now->format('Y-m-d');
            $hour = $now->format('H');
            
            $orders = \DB::table('orders')->selectRaw('HOUR(orders.created_at) as hour, COUNT(id) as orders, SUM(total) as sales')
                    ->where('status', 3)
                    ->whereDate('orders.created_at', $date_today);
            $query = "(";
            for ($hr = 0; $hr <= $hour; $hr++) {
                $query .= "HOUR(orders.created_at) = $hr ";
                if ($hr != $hour) {
                    $query .= "OR ";
                }
                
            }
            $orders = $orders->whereRaw("$query)")
                ->orderBy(\DB::raw('HOUR(orders.created_at)'), 'ASC')
                ->groupBy(\DB::raw('HOUR(orders.created_at)'))
                ->get();

            for ($hr = 0; $hr <= $hour; $hr++) {
                $o = 0;
                $s = 0;
                foreach ($orders as $order) {
                    if ($order) {
                        if ($order->hour == $hr) {
                            $o = $order->orders;
                            $s = $order->sales;
                            break;
                        }
                    }
                }

                $result['sales'][] = intVal($s);
                $result['orders'][] = intVal($o);
                $result['labels'][] = Carbon::createFromFormat('H:i', $hr.":00")->format('h:i A');
            }

            $result['count']['users'] = $this->user()->whereDate('created_at', $date_today)->get()->count();
            $result['count']['reports'] = $this->report()->whereDate('created_at', $date_today)->get()->count();
            $result['count']['orders'] = array_sum($result['orders']);
            $result['count']['sales'] = array_sum($result['sales']);

        } else if ($filter == "week") {
            $orders = \DB::table('orders')->selectRaw('DATE(orders.created_at) as date, COUNT(id) as orders, SUM(total) as sales')
                    ->where('status', 3);
            $query = "(";
            $temp_now = $now;
            for ($day = 1; $day <= 7; $day++) {
                $format_date = $temp_now->format('Y-m-d');
                $query .= "DATE(orders.created_at) = '$format_date' ";

                if ($day != 7) {
                    $query .= "OR ";
                }
                $temp_now->subDay();
            }

            $orders = $orders->whereRaw("$query)")
                    ->orderBy(\DB::raw('DATE(orders.created_at)'), 'ASC')
                    ->groupBy(\DB::raw('DATE(orders.created_at)'))
                    ->get();

            $temp_now = Carbon::today()->subDays(6);        
            for ($day = 1; $day <= 7; $day++) {
                $format_date = $temp_now->format('Y-m-d');
                $o = 0;
                $s = 0;
                foreach ($orders as $order) {
                    if ($order) {
                        if ($order->date == $format_date) {
                            $o = $order->orders;
                            $s = $order->sales;
                            break;
                        }
                    }
                }

  
                $result['sales'][] = intVal($s);
                $result['orders'][] = intVal($o);
                $result['labels'][] = $temp_now->format('M d');
                $temp_now->addDay();
            }

            $start = Carbon::today()->startOfDay()->format('Y-m-d H:i:s');
            $end = Carbon::today()->subDays(6)->endOfDay()->format('Y-m-d H:i:s');
            $result['count']['users'] = $this->user()->whereBetween('created_at', [$end, $start])->get()->count();
            $result['count']['reports'] = $this->report()->whereBetween('created_at', [$end, $start])->get()->count();
            $result['count']['orders'] = array_sum($result['orders']);
            $result['count']['sales'] = array_sum($result['sales']);

        } else if ($filter == "month") {
            $orders = \DB::table('orders')->selectRaw('DATE(orders.created_at) as date, COUNT(id) as orders, SUM(total) as sales')
                    ->where('status', 3);
            $query = "(";
            $temp_now = $now;
            for ($day = 1; $day <= intVal(Carbon::today()->format('d')); $day++) {
                $format_date = $temp_now->format('Y-m-d');
                $query .= "DATE(orders.created_at) = '$format_date' ";

                if ($day != intVal(Carbon::today()->format('d'))) {
                    $query .= "OR ";
                }
                $temp_now->subDay();
            }

            $orders = $orders->whereRaw("$query)")
                    ->orderBy(\DB::raw('DATE(orders.created_at)'), 'DESC')
                    ->groupBy(\DB::raw('DATE(orders.created_at)'))
                    ->get();
                    
            $temp_now = Carbon::today()->subDays(intVal(Carbon::today()->format('d'))-1);        
            for ($day = 1; $day <= intVal(Carbon::today()->format('d')); $day++) {
                $format_date = $temp_now->format('Y-m-d');
                $o = 0;
                $s = 0;
                foreach ($orders as $order) {
                    if ($order) {
                        if ($order->date == $format_date) {
                            $o = $order->orders;
                            $s = $order->sales;
                            break;
                        }
                    }
                }

                $result['sales'][] = intVal($s);
                $result['orders'][] = intVal($o);
                $result['labels'][] = $temp_now->format('M d');
                $temp_now->addDay();
            }

            $start = Carbon::today()->startOfDay()->format('Y-m-d H:i:s');
            $end = Carbon::today()->startOfMonth()->endOfDay()->format('Y-m-d H:i:s');
            $result['count']['users'] = $this->user()->whereBetween('created_at', [$end, $start])->get()->count();
            $result['count']['reports'] = $this->report()->whereBetween('created_at', [$end, $start])->get()->count();
            $result['count']['orders'] = array_sum($result['orders']);
            $result['count']['sales'] = array_sum($result['sales']);

        } else if ($filter == "year") {
            $orders = \DB::table('orders')->selectRaw('MONTH(orders.created_at) as date, COUNT(id) as orders, SUM(total) as sales')
                    ->where('status', 3);
            $query = "(";
            $temp_now = $now;
            for ($month = 1; $month <= intVal(Carbon::today()->format('m')); $month++) {
                $format_date = $temp_now->format('m');
                $query .= "MONTH(orders.created_at) = '$format_date' ";

                if ($month != intVal(Carbon::today()->format('m'))) {
                    $query .= "OR ";
                }
                $temp_now->subMonth();
            }

            $orders = $orders->whereRaw("$query)")
                    ->orderBy(\DB::raw('MONTH(orders.created_at)'), 'DESC')
                    ->groupBy(\DB::raw('MONTH(orders.created_at)'))
                    ->get();

            for ($month = 1; $month <= intVal(Carbon::today()->format('m')); $month++) {
                $format_date = Carbon::createFromFormat('m', $month)->format('m');
                $o = 0;
                $s = 0;
                foreach ($orders as $order) {
                    if ($order) {
                        if ($order->date == $format_date) {
                            $o = $order->orders;
                            $s = $order->sales;
                            break;
                        }
                    }
                }

                $result['sales'][] = intVal($s);
                $result['orders'][] = intVal($o);
                $result['labels'][] = Carbon::createFromFormat('m', $month)->format('M');
                $temp_now->addMonth();
            }

            $start = Carbon::today()->startOfDay()->format('Y-m-d H:i:s');
            $end = Carbon::today()->startofYear()->endOfDay()->format('Y-m-d H:i:s');
            $result['count']['users'] = $this->user()->whereBetween('created_at', [$end, $start])->get()->count();
            $result['count']['reports'] = $this->report()->whereBetween('created_at', [$end, $start])->get()->count();
            $result['count']['orders'] = array_sum($result['orders']);
            $result['count']['sales'] = array_sum($result['sales']);
        }

        return response()->json($result);
    }

    public function owner_chart($filter)
    {
        $restaurant_id = auth()->user()->restaurant()->value('id');
        $now = Carbon::now();
        $result = array(
            "labels" => [],
            "sales" => [],
            "orders" => [],
            "count" => []
        );

        if ($filter == "today") {
            $date_today = $now->format('Y-m-d');
            $hour = $now->format('H');
            
            $orders = \DB::table('sub_orders')->selectRaw('HOUR(sub_orders.updated_at) as hour, COUNT(sub_orders.id) as orders, SUM(sub_orders.total) as sales')
                    ->join('restaurant_sub_order', 'restaurant_sub_order.sub_order_id', '=', 'sub_orders.id')
                    ->join('restaurant', 'restaurant.id', '=', 'restaurant_sub_order.restaurant_id')
                    ->where('restaurant.id', $restaurant_id)
                    ->where('sub_orders.status', 3)
                    ->whereDate('sub_orders.updated_at', $date_today);
            $query = "(";
            for ($hr = 0; $hr <= $hour; $hr++) {
                $query .= "HOUR(sub_orders.updated_at) = $hr ";
                if ($hr != $hour) {
                    $query .= "OR ";
                }
                
            }
            $orders = $orders->whereRaw("$query)")
                ->orderBy(\DB::raw('HOUR(sub_orders.updated_at)'), 'ASC')
                ->groupBy(\DB::raw('HOUR(sub_orders.updated_at)'))
                ->get();

            for ($hr = 0; $hr <= $hour; $hr++) {
                $o = 0;
                $s = 0;
                foreach ($orders as $order) {
                    if ($order) {
                        if ($order->hour == $hr) {
                            $o = $order->orders;
                            $s = $order->sales;
                            break;
                        }
                    }
                }

                $result['sales'][] = intVal($s);
                $result['orders'][] = intVal($o);
                $result['labels'][] = Carbon::createFromFormat('H:i', $hr.":00")->format('h:i A');
            }
        } else if ($filter == "week") {            
            $orders = \DB::table('sub_orders')->selectRaw('DATE(sub_orders.updated_at) as date, COUNT(sub_orders.id) as orders, SUM(sub_orders.total) as sales')
                    ->join('restaurant_sub_order', 'restaurant_sub_order.sub_order_id', '=', 'sub_orders.id')
                    ->join('restaurant', 'restaurant.id', '=', 'restaurant_sub_order.restaurant_id')
                    ->where('restaurant.id', $restaurant_id)
                    ->where('sub_orders.status', 3);

            $query = "(";
            $temp_now = $now;
            for ($day = 1; $day <= 7; $day++) {
                $format_date = $temp_now->format('Y-m-d');
                $query .= "DATE(sub_orders.updated_at) = '$format_date' ";

                if ($day != 7) {
                    $query .= "OR ";
                }
                $temp_now->subDay();
            }

            $orders = $orders->whereRaw("$query)")
                    ->orderBy(\DB::raw('DATE(sub_orders.updated_at)'), 'ASC')
                    ->groupBy(\DB::raw('DATE(sub_orders.updated_at)'))
                    ->get();

            $temp_now = Carbon::today()->subDays(6);        
            for ($day = 1; $day <= 7; $day++) {
                $format_date = $temp_now->format('Y-m-d');
                $o = 0;
                $s = 0;
                foreach ($orders as $order) {
                    if ($order) {
                        if ($order->date == $format_date) {
                            $o = $order->orders;
                            $s = $order->sales;
                            break;
                        }
                    }
                }

  
                $result['sales'][] = intVal($s);
                $result['orders'][] = intVal($o);
                $result['labels'][] = $temp_now->format('M d');
                $temp_now->addDay();
            }
        } else if ($filter == "month") {
            $orders = \DB::table('sub_orders')->selectRaw('DATE(sub_orders.updated_at) as date, COUNT(sub_orders.id) as orders, SUM(sub_orders.total) as sales')
                    ->join('restaurant_sub_order', 'restaurant_sub_order.sub_order_id', '=', 'sub_orders.id')
                    ->join('restaurant', 'restaurant.id', '=', 'restaurant_sub_order.restaurant_id')
                    ->where('restaurant.id', $restaurant_id)
                    ->where('sub_orders.status', 3);

            $query = "(";
            $temp_now = $now;
            for ($day = 1; $day <= intVal(Carbon::today()->format('d')); $day++) {
                $format_date = $temp_now->format('Y-m-d');
                $query .= "DATE(sub_orders.updated_at) = '$format_date' ";

                if ($day != intVal(Carbon::today()->format('d'))) {
                    $query .= "OR ";
                }
                $temp_now->subDay();
            }

            $orders = $orders->whereRaw("$query)")
                    ->orderBy(\DB::raw('DATE(sub_orders.updated_at)'), 'DESC')
                    ->groupBy(\DB::raw('DATE(sub_orders.updated_at)'))
                    ->get();
                    
            $temp_now = Carbon::today()->subDays(intVal(Carbon::today()->format('d'))-1);        
            for ($day = 1; $day <= intVal(Carbon::today()->format('d')); $day++) {
                $format_date = $temp_now->format('Y-m-d');
                $o = 0;
                $s = 0;
                foreach ($orders as $order) {
                    if ($order) {
                        if ($order->date == $format_date) {
                            $o = $order->orders;
                            $s = $order->sales;
                            break;
                        }
                    }
                }

                $result['sales'][] = intVal($s);
                $result['orders'][] = intVal($o);
                $result['labels'][] = $temp_now->format('M d');
                $temp_now->addDay();
            }
        } else if ($filter == "year") {
            $orders = \DB::table('sub_orders')->selectRaw('MONTH(sub_orders.updated_at) as date, COUNT(sub_orders.id) as orders, SUM(sub_orders.total) as sales')
                    ->join('restaurant_sub_order', 'restaurant_sub_order.sub_order_id', '=', 'sub_orders.id')
                    ->join('restaurant', 'restaurant.id', '=', 'restaurant_sub_order.restaurant_id')
                    ->where('restaurant.id', $restaurant_id)
                    ->where('sub_orders.status', 3);

            $query = "(";
            $temp_now = $now;
            for ($month = 1; $month <= intVal(Carbon::today()->format('m')); $month++) {
                $format_date = $temp_now->format('m');
                $query .= "MONTH(sub_orders.updated_at) = '$format_date' ";

                if ($month != intVal(Carbon::today()->format('m'))) {
                    $query .= "OR ";
                }
                $temp_now->subMonth();
            }

            $orders = $orders->whereRaw("$query)")
                    ->orderBy(\DB::raw('MONTH(sub_orders.updated_at)'), 'DESC')
                    ->groupBy(\DB::raw('MONTH(sub_orders.updated_at)'))
                    ->get();

            for ($month = 1; $month <= intVal(Carbon::today()->format('m')); $month++) {
                $format_date = Carbon::createFromFormat('m', $month)->format('m');
                $o = 0;
                $s = 0;
                foreach ($orders as $order) {
                    if ($order) {
                        if ($order->date == $format_date) {
                            $o = $order->orders;
                            $s = $order->sales;
                            break;
                        }
                    }
                }

                $result['sales'][] = intVal($s);
                $result['orders'][] = intVal($o);
                $result['labels'][] = Carbon::createFromFormat('m', $month)->format('M');
                $temp_now->addMonth();
            }
        }


        $temp = \DB::table('sub_orders')->select('sub_orders.id')
                ->join('restaurant_sub_order', 'restaurant_sub_order.sub_order_id', '=', 'sub_orders.id')
                ->join('restaurant', 'restaurant.id', '=', 'restaurant_sub_order.restaurant_id')
                ->where('restaurant.id', $restaurant_id)
                ->whereNotIn('sub_orders.status', [3,4,5])
                ->get();

        $result['count']['o_orders'] = $temp->count();
        $result['count']['orders'] = array_sum($result['orders']);
        $result['count']['sales'] = array_sum($result['sales']);

        return response()->json($result);
    }
}
