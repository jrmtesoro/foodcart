<?php

namespace App\Http\Controllers\ApiController;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Storage;

class OwnerSalesController extends Controller
{
    public function index(Request $request)
    {
        $restaurant_id = auth()->user()->restaurant()->value('id');

        $shown_list = \DB::table('item_list')->select(['item_list.name as item_name', \DB::raw('SUM(item_list.quantity*item_list.price) as sales, SUM(item_list.quantity) as orders')])
                    ->join('restaurant_item_list', 'restaurant_item_list.item_list_id', '=', 'item_list.id')
                    ->join('restaurant', 'restaurant.id', '=', 'restaurant_item_list.restaurant_id')
                    ->where('restaurant.id', $restaurant_id)
                    ->groupBy('item_list.identifier');

        $menu_item_list = \DB::table('menu')->select('menu.id')
                    ->join('menu_item_list', 'menu_item_list.menu_id', '=', 'menu.id')
                    ->join('restaurant_menu', 'restaurant_menu.menu_id', '=', 'menu.id')
                    ->join('restaurant', 'restaurant.id', '=', 'restaurant_menu.restaurant_id')
                    ->where('restaurant.id', $restaurant_id)
                    ->get()->pluck('id');

        $hidden_items1 = \DB::table('menu')->select('menu.id')
                    ->join('restaurant_menu', 'restaurant_menu.menu_id', '=', 'menu.id')
                    ->join('restaurant', 'restaurant.id', '=', 'restaurant_menu.restaurant_id')
                    ->where('restaurant.id', $restaurant_id)
                    ->whereNotIn('menu.id', $menu_item_list)
                    ->get()->pluck('id');

        $hidden_list = \DB::table('menu')->select(['menu.name as item_name', \DB::raw('0 as orders, 0 as sales')])
                    ->join('restaurant_menu', 'restaurant_menu.menu_id', '=', 'menu.id')
                    ->join('restaurant', 'restaurant.id', '=', 'restaurant_menu.restaurant_id')
                    ->where('restaurant.id', $restaurant_id)
                    ->whereNull('menu.deleted_at')
                    ->whereIn('menu.id', $hidden_items1);

        if ($request->has('search')) {
            $search = $request->get('search');
            if ($search !== null) {
                $shown_list = $shown_list->where('item_list.name', 'LIKE', "%{$search}%");
                $hidden_list = $hidden_list->where('menu.name', 'LIKE', "{%$search%}");
            }
        }

        $shown_list->unionAll($hidden_list);

        return response()->json([
            "success" => true,
            "data" => $shown_list->get()
        ]);
    }

    public function index1(Request $request)
    {
        $restaurant_id = auth()->user()->restaurant()->value('id');
        
        $now = Carbon::now();
        $start_date = Carbon::now()->subDays(7)->startOfDay()->format('Y-m-d H:i:s');;
        $end_date = $now->endOfDay()->format('Y-m-d H:i:s');;

        $sub_orders = \DB::table('sub_orders')->selectRaw('DATE(sub_orders.updated_at) as date, SUM(sub_orders.total) as sales, COUNT(sub_orders.id) as orders')
                ->join('restaurant_sub_order', 'restaurant_sub_order.sub_order_id', '=', 'sub_orders.id')
                ->join('restaurant', 'restaurant.id', '=', 'restaurant_sub_order.restaurant_id')
                ->where('restaurant.id', $restaurant_id)
                ->whereBetween('sub_orders.updated_at', [$start_date, $end_date])
                ->groupBy(\DB::raw('DATE(sub_orders.updated_at)'));

        $rows = [];
        $sd = Carbon::now();
        for ($day = 1; $day <= 7; $day++) {
            $temp = $sd->format('Y-m-d');
            if (!$sub_orders->get()->contains('date', $temp)) {
                $rows[] = \DB::table('sub_orders')->selectRaw('"'.$temp.'" as date, 0 as sales, 0 as orders')->limit(1);
            }
            $sd->subDay();
        }

        foreach ($rows as $row) {
            $sub_orders->unionAll($row);
        }

        $sub_orders = $sub_orders->orderBy('date', 'asc')->get();

        return response()->json([
            "success" => true,
            "data" => $sub_orders
        ]);
    }

    public function pdf(Request $request)
    {
        $restaurant_id = auth()->user()->restaurant()->value('id');

        $radio_filter = $request->get('radio_filter');
        if ($radio_filter == "specific") {
            $specific_date = Carbon::createFromFormat('m-d-Y', $request->get('specific_date'))->format('Y-m-d');
            $shown_items = \DB::table('item_list')->select('item_list.id')
                    ->join('sub_order_item_list', 'sub_order_item_list.item_list_id', '=', 'item_list.id')
                    ->join('sub_orders', 'sub_orders.id', '=', 'sub_order_item_list.sub_order_id')
                    ->join('restaurant_item_list', 'restaurant_item_list.item_list_id', '=', 'item_list.id')
                    ->join('restaurant', 'restaurant.id', '=', 'restaurant_item_list.restaurant_id')
                    ->where('restaurant.id', $restaurant_id)
                    ->whereDate('sub_orders.updated_at', $specific_date)
                    ->where('sub_orders.status', 3)
                    ->get()->pluck('id');

            $shown_items1 = \DB::table('item_list')->select('item_list.identifier')
                    ->join('sub_order_item_list', 'sub_order_item_list.item_list_id', '=', 'item_list.id')
                    ->join('sub_orders', 'sub_orders.id', '=', 'sub_order_item_list.sub_order_id')
                    ->join('restaurant_item_list', 'restaurant_item_list.item_list_id', '=', 'item_list.id')
                    ->join('restaurant', 'restaurant.id', '=', 'restaurant_item_list.restaurant_id')
                    ->where('restaurant.id', $restaurant_id)
                    ->whereDate('sub_orders.updated_at', $specific_date)
                    ->where('sub_orders.status', 3)
                    ->get()->pluck('identifier'); 
        } else {
            $start_range = Carbon::createFromFormat('m-d-Y', $request->get('start_range'))->startOfDay()->format('Y-m-d H:i:s');;
            $end_range = Carbon::createFromFormat('m-d-Y', $request->get('end_range'))->endOfDay()->format('Y-m-d H:i:s');;
            
            $shown_items = \DB::table('item_list')->select('item_list.id')
                    ->join('sub_order_item_list', 'sub_order_item_list.item_list_id', '=', 'item_list.id')
                    ->join('sub_orders', 'sub_orders.id', '=', 'sub_order_item_list.sub_order_id')
                    ->join('restaurant_item_list', 'restaurant_item_list.item_list_id', '=', 'item_list.id')
                    ->join('restaurant', 'restaurant.id', '=', 'restaurant_item_list.restaurant_id')
                    ->where('restaurant.id', $restaurant_id)
                    ->where('sub_orders.status', 3)
                    ->whereBetween('sub_orders.updated_at', [$start_range, $end_range])
                    ->get()->pluck('id');

            $shown_items1 = \DB::table('item_list')->select('item_list.identifier')
                    ->join('sub_order_item_list', 'sub_order_item_list.item_list_id', '=', 'item_list.id')
                    ->join('sub_orders', 'sub_orders.id', '=', 'sub_order_item_list.sub_order_id')
                    ->join('restaurant_item_list', 'restaurant_item_list.item_list_id', '=', 'item_list.id')
                    ->join('restaurant', 'restaurant.id', '=', 'restaurant_item_list.restaurant_id')
                    ->where('restaurant.id', $restaurant_id)
                    ->where('sub_orders.status', 3)
                    ->whereBetween('sub_orders.updated_at', [$start_range, $end_range])
                    ->get()->pluck('identifier');  
        }
       

        $menu_item_list = \DB::table('menu_item_list')->select('menu_id')
                    ->get()->pluck('menu_id');
        
        $hidden_items1 = \DB::table('menu')->select('menu.id')
                    ->join('restaurant_menu', 'restaurant_menu.menu_id', '=', 'menu.id')
                    ->join('restaurant', 'restaurant.id', '=', 'restaurant_menu.restaurant_id')
                    ->where('restaurant.id', $restaurant_id)
                    ->whereNotIn('menu.id', $menu_item_list)
                    ->get()->pluck('id');

        $hidden_items_query = \DB::table('item_list')->select(['item_list.name as item_name', \DB::raw('0 as sales, 0 as orders, 0 as percentage')])
                    ->join('restaurant_item_list', 'restaurant_item_list.item_list_id', '=', 'item_list.id')
                    ->join('restaurant', 'restaurant.id', '=', 'restaurant_item_list.restaurant_id')
                    ->where('restaurant.id', $restaurant_id)
                    ->whereNotIn('item_list.identifier', $shown_items1)
                    ->groupBy('item_list.identifier');

        $hidden_items_query1 = \DB::table('menu')->select(['menu.name as item_name', \DB::raw('0 as sales, 0 as orders, 0 as percentage')])
                    ->join('restaurant_menu', 'restaurant_menu.menu_id', '=', 'menu.id')
                    ->join('restaurant', 'restaurant.id', '=', 'restaurant_menu.restaurant_id')
                    ->where('restaurant.id', $restaurant_id)
                    ->whereIn('menu.id', $hidden_items1);

        $temp = \DB::table('item_list')->select([\DB::raw('SUM(item_list.quantity) as orders')])
                    ->join('restaurant_item_list', 'restaurant_item_list.item_list_id', '=', 'item_list.id')
                    ->join('restaurant', 'restaurant.id', '=', 'restaurant_item_list.restaurant_id')
                    ->where('restaurant.id', $restaurant_id)
                    ->whereIn('item_list.id', $shown_items)
                    ->groupBy('item_list.identifier')
                    ->get();            
        
        $total_ord = 0;
        foreach ($temp as $row) {
            $total_ord += $row->orders;
        }

        $shown_items_query = \DB::table('item_list')->select(['item_list.name as item_name', \DB::raw('SUM(item_list.quantity*item_list.price) as sales, SUM(item_list.quantity) as orders, (SUM(item_list.quantity)/'.$total_ord.')*100 as percentage')])
                    ->join('restaurant_item_list', 'restaurant_item_list.item_list_id', '=', 'item_list.id')
                    ->join('restaurant', 'restaurant.id', '=', 'restaurant_item_list.restaurant_id')
                    ->where('restaurant.id', $restaurant_id)
                    ->whereIn('item_list.id', $shown_items)
                    ->groupBy('item_list.identifier');
        
        
        $shown_items_query->unionAll($hidden_items_query)
                    ->unionAll($hidden_items_query1);
        

        $shown_items_query = $shown_items_query->get();

        $indx = 0;
        $total_sales = 0;
        $total_orders = 0;
        $data = [];
        $pie_chart = [['Item Name', 'Orders']];
        foreach ($shown_items_query as $row) {
            $item_name = $row->item_name;
            $sales = $row->sales;
            $orders = $row->orders;
            $percentage = $row->percentage;
            
            $total_sales += $sales;
            $total_orders += $orders;

            $shown_items_query[$indx]->sales = "₱ ".$sales.".00";
            $shown_items_query[$indx]->percentage = round($percentage)."%";


            $pie_chart[] = [$item_name, intVal($orders)];
            $indx++;
        }
        
        $restaurant = $this->restaurant()->where("id", $restaurant_id)->get()->first();
        $data['header']['restaurant_name'] = $restaurant['name'];
        $data['total_sales'] = "₱ ".$total_sales.".00";;
        $data['total_orders'] = $total_orders;
        $data['pie_chart'] = $pie_chart;
        
        $temp = "";
        if ($request->get('radio_filter') == "specific") {
            $specific_date = Carbon::createFromFormat('m-d-Y', $request->get('specific_date'))->format('F d, Y');
            $temp = $specific_date;
        } else {
            $start_range = Carbon::createFromFormat('m-d-Y', $request->get('start_range'))->format('F d, Y');
            $end_range = Carbon::createFromFormat('m-d-Y', $request->get('end_range'))->format('F d, Y');

            $temp = $start_range." - ".$end_range;
        }
        $data['header']['dates'] = $temp;
        $data['table'] = $shown_items_query;
        
        $pdf = \PDFF::loadView('owner.pages.pdf.menu_report', compact('data'));
        $pdf->setOption('enable-javascript', true);
        $pdf->setOption('javascript-delay', 5000);
        $pdf->setOption('enable-smart-shrinking', true);
        $pdf->setOption('no-stop-slow-scripts', true);
        $pdf->setOption('footer-center', 'Page [page]');

        $path = "/app/pdf/restaurant/$restaurant_id";
        $now = Carbon::now()->format('m-d-Y_h-i-sA');
        $filename = "[".$now."]-Menu_Sales_Report.pdf";
        $full_path = storage_path().$path."/".$filename;
        $pdf->save($full_path);

        if ($request->header()['origin'][0] == "app") {
            $this->logs()->create([
                "user_id" => auth()->user()->id,
                "ip_address" => $request->ip(),
                "type" => "Print",
                "description" => "Menu Sales Report - $filename",
                "origin" => $request->header()['origin'][0]
            ]); 
        }

        return response()->json([
            "success" => true,
            "data" => [
                "full_path" => "pdf/restaurant/$restaurant_id/$filename"
            ]
        ]); 
    }

    public function pdf1(Request $request)
    {
        $restaurant_id = auth()->user()->restaurant()->value('id');

        $interval = $request->get('interval');
        $hidden_rows = [];

        $before_format = "";
        $after_format = "";
        $legend = "Dates";
        if ($interval == "daily") {
            $daily_input = Carbon::createFromFormat('m-d-Y', $request->get('daily_input'))->format('Y-m-d');
            $before_format = "H";
            $after_format = "h:i A";
            $legend = "Time";

            $query = \DB::table('sub_orders')->selectRaw('HOUR(sub_orders.updated_at) as date, SUM(sub_orders.total) as sales, COUNT(sub_orders.id) as orders')
                ->join('restaurant_sub_order', 'restaurant_sub_order.sub_order_id', '=', 'sub_orders.id')
                ->join('restaurant', 'restaurant.id', '=', 'restaurant_sub_order.restaurant_id')
                ->where('restaurant.id', $restaurant_id)
                ->where('sub_orders.status', 3)
                ->whereDate('sub_orders.updated_at', $daily_input)
                ->groupBy(\DB::raw('HOUR(sub_orders.updated_at)'));
        
            for ($hour = 0; $hour <= 23; $hour++) {
                if (!$query->get()->contains('date', $hour)) {
                    $hidden_rows[] = \DB::table('sub_orders')->selectRaw($hour.' as date, 0 as sales, 0 as orders')->limit(1);
                }
            }

        } else if ($interval == "week-picker-wrapper") {
            $weekly_input = $request->get('weekly_input');
            $weekly_array = explode(' - ', $weekly_input);
            $start_date = Carbon::createFromFormat('m/d/Y', $weekly_array[0])->startOfDay()->format('Y-m-d H:i:s');;
            $end_date = Carbon::createFromFormat('m/d/Y', $weekly_array[1])->endOfDay()->format('Y-m-d H:i:s');;
            
            $before_format = "Y-m-d";
            $after_format = "m-d-Y";
            $query = \DB::table('sub_orders')->selectRaw('DATE(sub_orders.updated_at) as date, SUM(sub_orders.total) as sales, COUNT(sub_orders.id) as orders')
                ->join('restaurant_sub_order', 'restaurant_sub_order.sub_order_id', '=', 'sub_orders.id')
                ->join('restaurant', 'restaurant.id', '=', 'restaurant_sub_order.restaurant_id')
                ->where('restaurant.id', $restaurant_id)
                ->where('sub_orders.status', 3)
                ->whereBetween('sub_orders.updated_at', [$start_date, $end_date])
                ->groupBy(\DB::raw('DATE(sub_orders.updated_at)'));

            $sd = Carbon::createFromFormat('m/d/Y', $weekly_array[0]);
            for ($day = 1; $day <= 7; $day++) {
                $temp = $sd->format('Y-m-d');
                if (!$query->get()->contains('date', $temp)) {
                    $hidden_rows[] = \DB::table('sub_orders')->selectRaw('"'.$temp.'" as date, 0 as sales, 0 as orders')->limit(1);
                }
                $sd->addDay();
            }
        } else if ($interval == "monthly") {
            $monthly_input = Carbon::createFromFormat('m-Y', $request->get('monthly_input'));
            $month_input = $monthly_input->format('m');
            $year_input = $monthly_input->format('Y');
            
            $query = \DB::table('sub_orders')->selectRaw('DAY(sub_orders.updated_at) as date, SUM(sub_orders.total) as sales, COUNT(sub_orders.id) as orders')
                ->join('restaurant_sub_order', 'restaurant_sub_order.sub_order_id', '=', 'sub_orders.id')
                ->join('restaurant', 'restaurant.id', '=', 'restaurant_sub_order.restaurant_id')
                ->where('restaurant.id', $restaurant_id)
                ->where('sub_orders.status', 3)
                ->whereRaw('MONTH(sub_orders.updated_at) = "'.$month_input.'"')
                ->whereRaw('YEAR(sub_orders.updated_at) = "'.$year_input.'"')
                ->groupBy(\DB::raw('DAY(sub_orders.updated_at)'));

            for ($day = 1; $day <= intVal($monthly_input->endOfMonth()->format('d')); $day++) {
                if (!$query->get()->contains('date', $day)) {
                    $hidden_rows[] = \DB::table('sub_orders')->selectRaw($day.' as date, 0 as sales, 0 as orders')->limit(1);
                }
            }

        } else {
            $yearly_input = $request->get('yearly_input');       

            $before_format = "m";
            $after_format = "m-d-Y";

            $query = \DB::table('sub_orders')->selectRaw('MONTH(sub_orders.updated_at) as date, SUM(sub_orders.total) as sales, COUNT(sub_orders.id) as orders')
                    ->join('restaurant_sub_order', 'restaurant_sub_order.sub_order_id', '=', 'sub_orders.id')
                    ->join('restaurant', 'restaurant.id', '=', 'restaurant_sub_order.restaurant_id')
                    ->whereRaw('YEAR(sub_orders.updated_at) = "'.$yearly_input.'"')
                    ->where('restaurant.id', $restaurant_id)
                    ->where('sub_orders.status', 3)
                    ->groupBy(\DB::raw('MONTH(sub_orders.updated_at)'));
            
            $hidden_rows = [];
            for ($month = 1; $month <= 12; $month++) {
                if (!$query->get()->contains('date', $month)) {
                    $hidden_rows[] = \DB::table('sub_orders')->selectRaw($month.' as date, 0 as sales, 0 as orders')->limit(1);
                }
            }
        }

        foreach ($hidden_rows as $row) {
            $query->union($row);
        }

        $query->orderBy('date', 'ASC');

        $table = $query->get();

        $data = [];

        $line_chart = [[$legend, 'Sales']];
        $column_chart = [[$legend, 'Orders']];
        $table_values = [];
        $total_sales = 0;
        $total_orders = 0;
        foreach ($table as $row) {
            if ($interval == "monthly") {
                $monthly_input = Carbon::createFromFormat('m-Y', $request->get('monthly_input'));
                $month_input = $monthly_input->format('M');
                $year_input = $monthly_input->format('Y');

                $temp_date = $month_input." ".$row->date.", ".$year_input;
                $date = Carbon::createFromFormat('M d, Y', $temp_date)->format('D m-d-Y');
            } else if ($interval == "yearly") {
                $date = \Carbon\Carbon::createFromFormat('m-d', $row->date."-01")->format('D m-d-Y');
            } else if ($interval == "week-picker-wrapper" || $interval == "weekly") {
                $date = \Carbon\Carbon::createFromFormat('Y-m-d', $row->date)->format('D m-d-Y');
                $interval = "weekly";
            } else {
                $date = \Carbon\Carbon::createFromFormat($before_format, $row->date)->format($after_format);
            }

            $line_chart[] = [$date, intVal($row->sales)];
            $column_chart[] = [$date, intVal($row->orders)];
            $table_values[] = array(
                "date" => $date,
                "sales" => "₱ ".$row->sales.".00",
                "orders" => $row->orders
            );
            $total_sales += intVal($row->sales);
            $total_orders += intVal($row->orders);
        }

        $restaurant = $this->restaurant()->where("id", $restaurant_id)->get()->first();

        $data['table'] = $table_values;
        $data['total_sales'] =  "₱ ".$total_sales.".00";
        $data['total_orders'] = $total_orders;
        $data['line_chart'] = $line_chart;
        $data['column_chart'] = $column_chart;
        $data['header']['restaurant_name'] = $restaurant['name'];
        $data['header']['interval'] = ucfirst($interval);

        //April 28, 2019 - May 4, 2019
        $temp = "";
        if ($interval == "weekly") {
            $weekly_input = $request->get('weekly_input');
            $weekly_array = explode(' - ', $weekly_input);
            $start_date = Carbon::createFromFormat('m/d/Y', $weekly_array[0])->format('F d, Y');
            $end_date = Carbon::createFromFormat('m/d/Y', $weekly_array[1])->format('F d, Y');

            $temp = $start_date." - ".$end_date;
        } else if ($interval == "monthly") {
            $monthly_input = Carbon::createFromFormat('m-Y', $request->get('monthly_input'))->format('F, Y');
            $temp = $monthly_input;
        } else if ($interval == "yearly") {
            $yearly_input = $request->get('yearly_input');
            $temp = $yearly_input;
        } else {
            $daily_input = Carbon::createFromFormat('m-d-Y', $request->get('daily_input'))->format('F d, Y');
            $temp = $daily_input;
        }
        $data['header']['dates'] = $temp;

        $pdf = \PDFF::loadView('owner.pages.pdf.report', compact('data'));
        $pdf->setOption('enable-javascript', true);
        $pdf->setOption('javascript-delay', 5000);
        $pdf->setOption('enable-smart-shrinking', true);
        $pdf->setOption('no-stop-slow-scripts', true);
        $pdf->setOption('footer-center', 'Page [page]');

        $path = "/app/pdf/restaurant/$restaurant_id";
        $now = Carbon::now()->format('m-d-Y_h-i-sA');
        $filename = "[".$now."]-".ucfirst($interval)."_Report.pdf";
        $full_path = storage_path().$path."/".$filename;
        $pdf->save($full_path);

        if ($request->header()['origin'][0] == "app") {
            $this->logs()->create([
                "user_id" => auth()->user()->id,
                "ip_address" => $request->ip(),
                "type" => "Print",
                "description" => "Restaurant Sales Report - $filename",
                "origin" => $request->header()['origin'][0]
            ]); 
        }

        return response()->json([
            "success" => true,
            "data" => [
                "full_path" => "pdf/restaurant/$restaurant_id/$filename"
            ]
        ]);
    }
}
