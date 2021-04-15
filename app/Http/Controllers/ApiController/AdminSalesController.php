<?php

namespace App\Http\Controllers\ApiController;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;

class AdminSalesController extends Controller
{
    public function index(Request $request)
    {
        $restaurants = $this->restaurant()->where('status', 1);
        if ($request->has('search')) {
            $search = $request->get('search');
            if ($search !== null) {
                $restaurants = $restaurants->where('name', 'LIKE', '%{$search}%');
            }
        }

        $restaurants = $restaurants->get();
        $restaurant_sales = [];
        $indx = 0;
        foreach ($restaurants as $restaurant) {
            $sub_orders = $this->restaurant()->find($restaurant->id)->suborder()->where('status', 3)->get();
            $total = 0;
            $count = 0;
            $web_order = 0;
            $app_order = 0;
            foreach ($sub_orders as $sub_order) {
                $total += $sub_order->total;
                $count++;

                if ($sub_order->origin == "web") {
                    $web_order++;
                } else if ($sub_order->origin == "app") {
                    $app_order++;
                }
            }

            $restaurant_sales[$indx]['id'] = $restaurant->id;
            $restaurant_sales[$indx]['name'] = $restaurant->name;
            $restaurant_sales[$indx]['total_sales'] = $total;
            $restaurant_sales[$indx]['total_transaction'] = $count;
            $restaurant_sales[$indx]['web_order'] = $web_order;
            $restaurant_sales[$indx]['app_order'] = $app_order;
            $indx++;
        }

        return response()->json([
            "success" => true,
            "data" => $restaurant_sales
        ]);
    }

    public function index1(Request $request)
    {
        $shown_list = \DB::table('item_list')->select(['item_list.name as item_name', 'restaurant.name', \DB::raw('SUM(item_list.quantity*item_list.price) as sales, SUM(item_list.quantity) as orders')])
            ->join('restaurant_item_list', 'restaurant_item_list.item_list_id', '=', 'item_list.id')
            ->join('restaurant', 'restaurant.id', '=', 'restaurant_item_list.restaurant_id')
            ->join('sub_order_item_list', 'sub_order_item_list.item_list_id', '=', 'item_list.id')
            ->join('sub_orders', 'sub_orders.id', '=', 'sub_order_item_list.sub_order_id')
            ->where('sub_orders.status', 3)
            ->groupBy('item_list.identifier');

        $menu_item_list = \DB::table('menu_item_list')->select('menu_id')
                    ->get()->pluck('menu_id');

        $hidden_items1 = \DB::table('menu')->select('menu.id')
                    ->whereNotIn('menu.id', $menu_item_list)
                    ->get()->pluck('id');

        $hidden_list = \DB::table('menu')->select(['menu.name as item_name', 'restaurant.name', \DB::raw('0 as orders, 0 as sales')])
            ->join('restaurant_menu', 'restaurant_menu.menu_id', '=', 'menu.id')
            ->join('restaurant', 'restaurant.id', '=', 'restaurant_menu.restaurant_id')
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

    public function show($restaurant_id, Request $request)
    {
        $sub_orders = $this->restaurant()->find($restaurant_id)->suborder()->get();

        $total = 0;
        $count = 0;
        $web_order = 0;
        $app_order = 0;
        foreach ($sub_orders as $sub_order) {
            $total += 0;
            $count++;

            if ($sub_order->origin == "web") {
                $web_order++;
            } else if ($sub_order->origin == "app") {
                $app_order++;
            }
        }

        $restaurant = $this->restaurant()->find($restaurant_id)->get()->first();

        return response()->json([
            "success" => true,
            "data" => [
                "total_sales" => $total,
                "total_orders" => $count,
                "web_order" => $web_order,
                "app_order" => $app_order,
                "restaurant" => $restaurant
            ]
        ]);
    }

    public function pdf(Request $request)
    {
        $query = \DB::table('restaurant')
                ->where('restaurant.status', 1)
                ->groupBy('restaurant.id');

        $radio_filter = $request->get('radio_filter');
        if ($radio_filter == "specific") {
            $specific_date = Carbon::createFromFormat('m-d-Y', $request->get('specific_date'))->format('Y-m-d');
            $query->leftJoin('restaurant_sub_order', function ($query) {
                        $query->on('restaurant_sub_order.restaurant_id', '=', 'restaurant.id');
                    })
                    ->leftJoin('sub_orders', function ($query) use ($specific_date) {
                        $query->on('sub_orders.id', '=', 'restaurant_sub_order.sub_order_id')
                                ->whereDate('sub_orders.updated_at', $specific_date)
                                ->where('sub_orders.status', 3);
                    })
                    ->select(['restaurant.id', 'restaurant.name', 
                            \DB::raw('SUM(sub_orders.total) as sales,
                            COUNT(sub_orders.id) as orders,
                            SUM(case when sub_orders.origin = "web" then 1 else 0 end) as web_order,
                            SUM(case when sub_orders.origin = "app" then 1 else 0 end) as app_order')]);
        } else {
            $start_range = Carbon::createFromFormat('m-d-Y', $request->get('start_range'))->startOfDay()->format('Y-m-d H:i:s');
            $end_range = Carbon::createFromFormat('m-d-Y', $request->get('end_range'))->endOfDay()->format('Y-m-d H:i:s');

            $query->leftJoin('restaurant_sub_order', function ($query) {
                        $query->on('restaurant_sub_order.restaurant_id', '=', 'restaurant.id');
                    })
                    ->leftJoin('sub_orders', function ($query) use ($start_range, $end_range) {
                        $query->on('sub_orders.id', '=', 'restaurant_sub_order.sub_order_id')->where('sub_orders.status', 3)
                        ->whereBetween('sub_orders.updated_at', [$start_range, $end_range]);
                    })
                    ->select(['restaurant.id', 'restaurant.name', 
                            \DB::raw('SUM(sub_orders.total) as sales,
                            COUNT(sub_orders.id) as orders,
                            SUM(case when sub_orders.origin = "web" then 1 else 0 end) as web_order,
                            SUM(case when sub_orders.origin = "app" then 1 else 0 end) as app_order')]);;
        }

        if ($request->has('search')) {
            $column = $request->get('column');
            $search = '%'.$request->get('search').'%';
            if ($column == "all") {
                $query->whereRaw("(restaurant.id LIKE ? OR restaurant.name LIKE ?)",
                [
                    $search, $search
                ]);
            } else {
                $query->where($column, 'LIKE', "%{$search}%");
            }
        }

        $query = $query->get();

        $data = [];
        $total_web = 0;
        $total_app = 0;
        $total_sales = 0;
        $total_orders = 0;
        $indx = 0;
        foreach ($query as $restaurant) {
            if ($restaurant->sales == null) {
                $query[$indx]->sales = "₱ 0.00";
            } else {
                $total_sales += $restaurant->sales;
                $query[$indx]->sales = "₱ ".$restaurant->sales.".00";
            }

            $total_orders += $restaurant->orders;
            $total_web += $restaurant->web_order;
            $total_app += $restaurant->app_order;

            $indx++;
        }

        $data['total_web'] = intVal($total_web);
        $data['total_app'] = intVal($total_app);
        $data['total_sales'] = "₱ ".$total_sales.".00";
        $data['total_orders'] = intVal($total_orders);
        $data['table'] = $query;

        $tmp = "";
        if ($radio_filter == "specific") {
            $specific_date = Carbon::createFromFormat('m-d-Y', $request->get('specific_date'))->format('F d, Y');
            $tmp = $specific_date;
        } else {
            $start_range = Carbon::createFromFormat('m-d-Y', $request->get('start_range'))->format('F d, Y');
            $end_range = Carbon::createFromFormat('m-d-Y', $request->get('end_range'))->format('F d, Y');
            $tmp = $start_range." - ".$end_range;
        }

        $data['header']['dates'] = $tmp;

        $pdf = \PDFF::loadView('admin.pages.pdf.sales', compact('data'));
        $pdf->setOption('enable-javascript', true);
        $pdf->setOption('javascript-delay', 5000);
        $pdf->setOption('enable-smart-shrinking', true);
        $pdf->setOption('no-stop-slow-scripts', true);
        $pdf->setOption('footer-center', 'Page [page]');

        $path = "/app/pdf/admin";
        $now = Carbon::now()->format('m-d-Y_h-i-sA');
        $filename = "[".$now."]-Restaurant_Sales_Report.pdf";
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
                "full_path" => "pdf/admin/$filename"
            ]
        ]); 
    }

    public function pdf1(Request $request)
    {
        $radio_filter = $request->get('radio_filter');
        if ($radio_filter == "specific") {
            $specific_date = Carbon::createFromFormat('m-d-Y', $request->get('specific_date'))->format('Y-m-d');
            $shown_items = \DB::table('item_list')->select('item_list.id')
                    ->join('sub_order_item_list', 'sub_order_item_list.item_list_id', '=', 'item_list.id')
                    ->join('sub_orders', 'sub_orders.id', '=', 'sub_order_item_list.sub_order_id')
                    ->whereDate('sub_orders.updated_at', $specific_date)
                    ->where('sub_orders.status', 3)
                    ->get()->pluck('id');

                    $shown_items1 = \DB::table('item_list')->select('item_list.identifier')
                    ->join('sub_order_item_list', 'sub_order_item_list.item_list_id', '=', 'item_list.id')
                    ->join('sub_orders', 'sub_orders.id', '=', 'sub_order_item_list.sub_order_id')
                    ->whereDate('sub_orders.updated_at', $specific_date)
                    ->where('sub_orders.status', 3)
                    ->get()->pluck('identifier'); 
        } else {
            $start_range = Carbon::createFromFormat('m-d-Y', $request->get('start_range'))->startOfDay()->format('Y-m-d H:i:s');
            $end_range = Carbon::createFromFormat('m-d-Y', $request->get('end_range'))->endOfDay()->format('Y-m-d H:i:s');
            
            $shown_items = \DB::table('item_list')->select('item_list.id')
                    ->join('sub_order_item_list', 'sub_order_item_list.item_list_id', '=', 'item_list.id')
                    ->join('sub_orders', 'sub_orders.id', '=', 'sub_order_item_list.sub_order_id')
                    ->where('sub_orders.status', 3)
                    ->whereBetween('sub_orders.updated_at', [$start_range, $end_range])
                    ->where('sub_orders.status', 3)
                    ->get()->pluck('id');

            $shown_items1 = \DB::table('item_list')->select('item_list.identifier')
                    ->join('sub_order_item_list', 'sub_order_item_list.item_list_id', '=', 'item_list.id')
                    ->join('sub_orders', 'sub_orders.id', '=', 'sub_order_item_list.sub_order_id')
                    ->where('sub_orders.status', 3)
                    ->whereBetween('sub_orders.updated_at', [$start_range, $end_range])
                    ->get()->pluck('identifier');  
        }
       

        $menu_item_list = \DB::table('menu_item_list')->select('menu_id')
                    ->get()->pluck('menu_id');
        
        $hidden_items1 = \DB::table('menu')->select('menu.id')
                    ->whereNotIn('menu.id', $menu_item_list)
                    ->get()->pluck('id');

        $hidden_items_query = \DB::table('item_list')->select(['item_list.name as item_name', 'restaurant.name as restaurant_name', \DB::raw('0 as sales, 0 as orders, 0 as percentage')])
                    ->join('restaurant_item_list', 'restaurant_item_list.item_list_id', '=', 'item_list.id')
                    ->join('restaurant', 'restaurant.id', '=', 'restaurant_item_list.restaurant_id')
                    ->whereNotIn('item_list.identifier', $shown_items1)
                    ->groupBy('item_list.identifier');

        $hidden_items_query1 = \DB::table('menu')->select(['menu.name as item_name', 'restaurant.name', \DB::raw('0 as sales, 0 as orders, 0 as percentage')])
                    ->join('restaurant_menu', 'restaurant_menu.menu_id', '=', 'menu.id')
                    ->join('restaurant', 'restaurant.id', '=', 'restaurant_menu.restaurant_id')
                    ->whereNull('menu.deleted_at')
                    ->whereIn('menu.id', $hidden_items1);

        if ($request->has('search')) {
            $column = $request->get('column');
            $search = '%'.$request->get('search').'%';
            if ($column == "all") {
                $hidden_items_query->whereRaw("(item_list.name LIKE ? OR restaurant.name LIKE ?)",
                [
                    $search, $search
                ]);

                $hidden_items_query1->whereRaw("(menu.name LIKE ? OR restaurant.name LIKE ?)",
                [
                    $search, $search
                ]);
            } else {
                if ($column == 'menu.name') {
                    $hidden_items_query->where('item_list.name', 'LIKE', "%{$search}%");
                    $hidden_items_query1->where('menu.name', 'LIKE', "%{$search}%");
                } else {
                    $hidden_items_query->where($column, 'LIKE', "%{$search}%");
                    $hidden_items_query1->where($column, 'LIKE', "%{$search}%");
                }
            }
        }
                    
        $temp = \DB::table('item_list')->select([\DB::raw('SUM(item_list.quantity) as orders')])
                ->join('restaurant_item_list', 'restaurant_item_list.item_list_id', '=', 'item_list.id')
                ->join('restaurant', 'restaurant.id', '=', 'restaurant_item_list.restaurant_id')
                ->whereIn('item_list.id', $shown_items)
                ->groupBy('item_list.identifier')
                ->get();
                
        $total_orders = 0;
        foreach ($temp as $item) {
            $total_orders += $item->orders;
        }

        $shown_items_query = \DB::table('item_list')->select(['item_list.name as item_name', 'restaurant.name as restaurant_name', \DB::raw('SUM(item_list.quantity*item_list.price) as sales, SUM(item_list.quantity) as orders, (SUM(item_list.quantity)/'.$total_orders.')*100 as percentage')])
                    ->join('restaurant_item_list', 'restaurant_item_list.item_list_id', '=', 'item_list.id')
                    ->join('restaurant', 'restaurant.id', '=', 'restaurant_item_list.restaurant_id')
                    ->whereIn('item_list.id', $shown_items)
                    ->groupBy('item_list.identifier');
        
        if ($request->has('search')) {
            $column = $request->get('column');
            $search = '%'.$request->get('search').'%';
            if ($column == "all") {
                $shown_items_query->whereRaw("(item_list.name LIKE ? OR restaurant.name LIKE ?)",
                [
                    $search, $search
                ]);
            } else {
                if ($column == 'menu.name') {
                    $shown_items_query->where('item_list.name', 'LIKE', "%{$search}%");
                } else {
                    $shown_items_query->where($column, 'LIKE', "%{$search}%");
                }
            }
        }

        $shown_items_query->unionAll($hidden_items_query)
                    ->unionAll($hidden_items_query1);

        $menu = $shown_items_query->get();
        $data = [];

        $total_sales = 0;
        $total_orders = 0;
        $pie_chart = [['Item Name', 'Orders']];
        foreach ($menu as $row) {
            $total_sales += $row->sales;
            $total_orders += $row->orders;
            $pie_chart[] = [$row->item_name, intVal($row->orders)];

            $row->percentage = round($row->percentage)."%";
            $row->sales = "₱ ".$row->sales.".00";
        }
        
        $data['table'] = $menu;
        $data['total_sales'] = "₱ ".$total_sales.".00";
        $data['total_orders'] = $total_orders;
        $data['pie_chart'] = $pie_chart;

        $tmp = "";
        if ($radio_filter == "specific") {
            $specific_date = Carbon::createFromFormat('m-d-Y', $request->get('specific_date'))->format('F d, Y');
            $tmp = $specific_date;
        } else {
            $start_range = Carbon::createFromFormat('m-d-Y', $request->get('start_range'))->format('F d, Y');
            $end_range = Carbon::createFromFormat('m-d-Y', $request->get('end_range'))->format('F d, Y');
            $tmp = $start_range." - ".$end_range;
        }

        $data['header']['dates'] = $tmp;

        $pdf = \PDFF::loadView('admin.pages.pdf.sales1', compact('data'));
        $pdf->setOption('enable-javascript', true);
        $pdf->setOption('javascript-delay', 5000);
        $pdf->setOption('enable-smart-shrinking', true);
        $pdf->setOption('no-stop-slow-scripts', true);
        $pdf->setOption('footer-center', 'Page [page]');

        $path = "/app/pdf/admin";
        $now = Carbon::now()->format('m-d-Y_h-i-sA');
        $filename = "[".$now."]-Restaurant_Sales_Report.pdf";
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
                "full_path" => "pdf/admin/$filename"
            ]
        ]); 
    }
}
