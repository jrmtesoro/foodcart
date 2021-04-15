<?php

namespace App\Http\Controllers\ApiController;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;

use Datatables;
use DB;

class DataTableController extends Controller
{
    public function getMenu(Request $request)
    {
        $restaurant_id = auth()->user()->restaurant()->value('id');

        $get = ['menu.name', 'menu.price', 'category.name as category_name', 'menu.created_at', 'menu.id', 'menu.deleted_at'];

        $restaurant_menu = DB::table('menu')
                ->join('restaurant_menu', 'restaurant_menu.menu_id', '=', 'menu.id')
                ->join('restaurant', 'restaurant.id', '=', 'restaurant_menu.restaurant_id')
                ->join('menu_category', 'menu_category.menu_id', '=', 'menu.id')
                ->join('category', 'category.id', '=', 'menu_category.category_id')
                ->where('restaurant.id', $restaurant_id)
                ->select($get);

        return DataTables::of($restaurant_menu)
                ->filter(function ($query) use ($request) {
                    if ($request->has('search')) {
                        $column = $request->get('column');
                        $search = '%'.$request->get('search').'%';
                        if ($column == "all") {
                            $query->whereRaw("(menu.name LIKE ? OR menu.price LIKE ? OR menu.created_at LIKE ? OR menu.deleted_at LIKE ?)",
                            [
                                $search, $search, $search, $search
                            ]);
                        } else {
                            $query->where($column, 'LIKE', "%{$search}%");
                        }
                    }

                    if ($request->has('category')) {
                        $category_id = $request->get('category');
                        if ($category_id != "all") {
                            $query->where('category.id', $category_id);
                        }
                        // $date = 'menu.created_at';
                        // if ($request->get('show_trash') == 'trash') {
                        //     $date = 'menu.deleted_at';
                        // }

                        // $start_date = Carbon::createFromFormat('m-d-Y', $request->get('start_date'))->format('Y-m-d');
                        // $end_date = Carbon::createFromFormat('m-d-Y', $request->get('end_date'))->format('Y-m-d');
                        // $query->whereRaw("(".$date." BETWEEN '".$start_date."' AND '".$end_date."' OR ".$date." LIKE '".$end_date."%')");
                    }

                    $trash = $request->get('show_trash');
                    if ($trash == "without") {
                        $query->whereNull('menu.deleted_at');
                    } else if ($trash == "trash") {
                        $query->whereNotNull('menu.deleted_at');
                    }
                })
                ->addColumn('action', function ($restaurant_menu) {
                    $menu_id = $restaurant_menu->id;
                    if ($restaurant_menu->deleted_at !== null) {
                        $restore_btn = "<button class='btn btn-outline-success btn-sm' data-toggle='modal' data-menu='$menu_id' data-target='#restore_confirmation'><span class='fas fa-plus-circle py-1'></span></button>";
                        return $restore_btn;
                    } else {
                        $view_btn = "<a class='btn btn-outline-primary btn-sm' href='menu/$menu_id'><span class='fas fa-eye py-1'></span></a>";
                        $edit_btn = "<a class='btn btn-outline-warning btn-sm' href='menu/$menu_id/edit'><span class='fas fa-pencil-alt py-1'></span></a>";
                        $del_btn = "<button class='btn btn-outline-danger btn-sm' data-toggle='modal' data-menu='$menu_id' data-target='#delete_confirmation'><span class='fas fa-minus-circle py-1'></span></button>";
                        return $view_btn.$edit_btn.$del_btn;
                    }
                })
                ->editColumn('created_at', function ($restaurant_menu) {
                    $created_at = Carbon::parse($restaurant_menu->created_at)->format('m-d-Y g:i A');
                    return $created_at;
                })
                ->editColumn('deleted_at', function ($restaurant_menu) {
                    if ($restaurant_menu->deleted_at !== null) {
                        $deleted_at = Carbon::parse($restaurant_menu->deleted_at)->format('m-d-Y g:i A');
                        return $deleted_at;
                    }
                    return "-";
                })
                ->make('true');
    }

    public function getCategory(Request $request)
    {
        $restaurant_id = auth()->user()->restaurant()->value('id');

        $restaurant_category = DB::table('restaurant')
                ->selectRaw('category.id, category.name, category.created_at, category.deleted_at, count(menu.id) as used_by')
                ->join('restaurant_category', 'restaurant_category.restaurant_id', '=', 'restaurant.id')
                ->join('category', 'category.id', '=', 'restaurant_category.category_id')
                ->leftJoin('menu_category', function ($query) {
                    $query->on('menu_category.category_id', '=', 'category.id');
                })
                ->leftJoin('menu', function ($query) {
                    $query->on('menu.id', '=', 'menu_category.menu_id');
                })
                ->where('restaurant.id', $restaurant_id)
                ->groupBy('category.name');

        return DataTables::of($restaurant_category)
                ->filter(function ($query) use ($request) {

                    if ($request->has('search')) {
                        $query->where('category.name', 'LIKE', "%{$request->get('search')}%");
                    }

                    // $date = 'category.created_at';
                    // if ($request->get('show_trash') == 'trash') {
                    //     $date = 'category.deleted_at';
                    // }

                    // $start_date = Carbon::createFromFormat('m-d-Y', $request->get('start_date'))->format('Y-m-d');
                    // $end_date = Carbon::createFromFormat('m-d-Y', $request->get('end_date'))->format('Y-m-d');
                    // $query->whereRaw("(".$date." BETWEEN '".$start_date."' AND '".$end_date."' OR ".$date." LIKE '".$end_date."%')");
                    

                    $trash = $request->get('show_trash');
                    if ($trash == "without") {
                        $query->whereNull('category.deleted_at');
                    } else if ($trash == 'trash') {
                        $query->whereNotNull('category.deleted_at');
                    }
                })
                ->addColumn('action', function ($restaurant_category) {
                    $category_id = $restaurant_category->id;
                    $category_name = $restaurant_category->name;
                    if ($restaurant_category->deleted_at !== null) {
                        $restore_btn = "<a class='btn btn-outline-default btn-sm' data-toggle='modal' data-menu='$category_id' data-target='#restore_confirmation'><span class='fas fa-trash-restore-alt py-1'></span></a>";
                        return $restore_btn;
                    } else {
                        $edit_btn = "<button class='btn btn-outline-warning btn-sm' data-toggle='modal' data-menu='$category_id' data-name='$category_name' data-target='#edit_category'><span class='fas fa-pencil-alt py-1'></span></button>";
                        $del_btn = "<button class='btn btn-outline-danger btn-sm' data-toggle='modal' data-menu='$category_id' data-target='#delete_confirmation'><span class='fas fa-trash-alt py-1'></span></button>";
                        return $edit_btn.$del_btn;
                    }
                })
                ->editColumn('created_at', function ($restaurant_category) {
                    $created_at = Carbon::parse($restaurant_category->created_at)->format('m-d-Y g:i A');
                    return $created_at;
                })
                ->editColumn('deleted_at', function ($restaurant_category) {
                    if ($restaurant_category->deleted_at !== null) {
                        $deleted_at = Carbon::parse($restaurant_category->deleted_at)->format('m-d-Y g:i A');
                        return $deleted_at;
                    }
                    return "-";
                })
                ->make('true');
    }

    public function getTag(Request $request)
    {
        $tags = DB::table('tag')
                ->selectRaw('tag.id, tag.name, tag.slug, tag.status, tag.created_at, count(menu.id) as used_by')
                ->leftJoin('menu_tag', function ($query) {
                    $query->on('menu_tag.tag_id', '=', 'tag.id');
                })
                ->leftJoin('menu', function ($query) {
                    $query->on('menu.id', '=', 'menu_tag.menu_id');
                })
                ->groupBy('tag.name');
        
        return DataTables::of($tags)
                ->filter(function ($query) use ($request) {
                    if ($request->has('search')) {
                        $column = $request->get('column');
                        $search = '%'.$request->get('search').'%';
                        if ($column == "all") {
                            $query->whereRaw("(tag.name LIKE ? OR tag.slug LIKE ? OR tag.created_at LIKE ?)", 
                            [
                                $search, $search, $search
                            ]);
                        } else {
                            $query->where($column, 'LIKE', "%{$search}%");
                        }
                    }
                    
                    $status = $request->get('show_trash');
                    if ($status != "all") {
                        $query->where('tag.status', $status);
                    }
                })
                ->addColumn('action', function ($tags) {
                    $status = $tags->status;
                    $route = str_replace('api/', '', route('admin.menu.index'))."?tag=$tags->name";
                    $view_btn = "<button class='btn btn-outline-primary btn-sm' data-toggle='modal' data-target='#show_tag' data-view='$route' data-name='$tags->name' data-slug='$tags->slug' data-status='$tags->status' data-use='$tags->used_by'><span class='fas fa-eye py-1'></span></button>";
                    $accept_btn = "<button class='btn btn-outline-success btn-sm' data-toggle='modal' data-target='#accept_confirmation' data-id='$tags->id'><span class='fas fa-check py-1'></span></button>";
                    $decline_btn = "<button class='btn btn-outline-danger btn-sm' data-toggle='modal' data-target='#reject_confirmation' data-id='$tags->id'><span class='fas fa-times' style='padding: 0.3rem 0.2rem'></span></button>";
                    if ($status == 0) {
                        return $view_btn.$accept_btn.$decline_btn;
                    } else {
                        return $view_btn;
                    }
                })
                ->editColumn('status', function ($tags) {
                    $status = $tags->status;
                    $title = "rejected";
                    $color = "danger";
                    if ($status == 0) {
                        $title = "pending";
                        $color = "warning";
                    } else if ($status == 1) {
                        $title = "accepted";
                        $color = "success";
                    }

                    return "<span class='badge badge-dot'><i class='bg-$color'></i> $title</span>";
                })
                ->make('true');
    }

    public function getPartnership(Request $request)
    {
        $restaurant = DB::table('restaurant')
                ->selectRaw('restaurant.id, restaurant.name, restaurant.contact_number, restaurant.status, restaurant.created_at, user.email')
                ->join('user_restaurant', 'user_restaurant.restaurant_id', '=', 'restaurant.id')
                ->join('user', 'user.id', '=', 'user_restaurant.user_id');

        return DataTables::of($restaurant)
                ->filter(function ($query) use ($request) {
                    $status = $request->get('show_trash');
                    if ($status != "all") {
                        $query->where('restaurant.status', $status);
                    }

                    if ($request->has('search')) {
                        $column = $request->get('column');
                        $search = '%'.$request->get('search').'%';
                        if ($column == "all") {
                            $query->whereRaw("(restaurant.name LIKE ?
                            OR user.email LIKE ? 
                            OR restaurant.contact_number LIKE ? 
                            OR restaurant.created_at LIKE ?)", 
                            [
                                $search,$search,$search,$search
                            ]);
                        } else {
                            $query->where($column, 'LIKE', "{%$search%}");
                        }
                    }
                })
                ->addColumn('action', function ($restaurant) {
                    $status = $restaurant->status;
                    $view_btn = "<a class='btn btn-outline-primary btn-sm' href='partnership/$restaurant->id'><span class='fas fa-eye py-1'></span></a>";
                    $review_btn = "<button class='btn btn-outline-primary btn-sm' data-toggle='modal' data-target='#review_confirmation' data-id='$restaurant->id'><span class='fas fa-search py-1'></span></button>";
                    if ($status == 0) {
                        return $view_btn.$review_btn;
                    } else {
                        return $view_btn;
                    }
                })
                ->editColumn('status', function ($restaurant) {
                    $status = $restaurant->status;
                    $title = "rejected";
                    $color = "danger";
                    if ($status == 0) {
                        $title = "pending";
                        $color = "warning";
                    } else if ($status == 1) {
                        $title = "accepted";
                        $color = "success";
                    } else if ($status == 3) {
                        $title = "reviewing";
                        $color = "primary";
                    }

                    return "<span class='badge badge-dot'><i class='bg-$color'></i> $title</span>";
                })
                ->make('true');
    }

    public function getOrder(Request $request)
    {
        $restaurant_id = auth()->user()->restaurant()->value('id');

        $sub_order = \DB::table('orders')
                ->selectRaw('sub_orders.id, orders.code, sub_orders.status, orders.created_at')
                ->join('order_sub_order', 'order_sub_order.order_id', '=', 'orders.id')
                ->join('sub_orders', 'sub_orders.id', '=', 'order_sub_order.sub_order_id')
                ->join('restaurant_sub_order', 'restaurant_sub_order.sub_order_id', '=', 'sub_orders.id')
                ->join('restaurant', 'restaurant.id', '=', 'restaurant_sub_order.restaurant_id')
                ->where('restaurant.id', $restaurant_id)
                ->groupBy('sub_orders.id')
                ->orderBy('orders.created_at', 'DESC');

        return DataTables::of($sub_order)
                ->filter(function ($query) use ($request) {
                    if ($request->has('search')) {
                        $search = "%".$request->get('search')."%";

                        $query->whereRaw('orders.code LIKE ? OR orders.created_at LIKE ?',[
                            $search, $search
                        ]);
                    }

                    $status = $request->get('show_trash');
                    
                    if ($status == "process") {
                        $query->whereIn('sub_orders.status', [0, 1, 2]);
                    } else if ($status == "completed") {
                        $query->where('sub_orders.status', 3);
                    } else if ($status == "rejected") {
                        $query->where('sub_orders.status', 4);
                    } else if ($status == "cancelled") {
                        $query->where('sub_orders.status', 5);
                    }

                    if ($request->has('sales')) {
                        if ($request->get('sales') !== null) {
                            $sub_order_ids = explode(",",$request->get('sales'));
                            $query->whereIn('sub_orders.id', $sub_order_ids);
                        }
                    }

                    // if ($request->has('search')) {
                    //     $search = '%'.$request->get('search').'%';
                    //     if ($column == "all") {
                    //         $query->whereRaw("(restaurant.name LIKE ?
                    //         OR user.email LIKE ? 
                    //         OR restaurant.contact_number LIKE ? 
                    //         OR restaurant.created_at LIKE ?)", 
                    //         [
                    //             $search,$search,$search,$search
                    //         ]);
                    //     } else {
                    //         $query->where($column, 'LIKE', "{%$search%}");
                    //     }
                    // }
                })
                ->addColumn('action', function ($sub_order) {
                    $view_btn = "<a class='btn btn-outline-primary btn-sm' href='order/$sub_order->id'><span class='fas fa-eye py-1'></span></a>";
                    return $view_btn;
                })
                ->editColumn('status', function ($sub_order) {
                    $status = $sub_order->status;
                    $title = "pending";
                    $color = "warning";
                    if ($status == 1) {
                        $title = "processing";
                        $color = "primary";
                    } else if ($status == 2) {
                        $title = "delivering";
                        $color = "info";
                    } else if ($status == 3) {
                        $title = "completed";
                        $color = "success";
                    } else if ($status == 4) {
                        $title = "rejected";
                        $color = "danger";
                    } else if ($status == 5) {
                        $title = "cancelled";
                        $color = "danger";
                    }
                    
                    return "<span class='badge badge-dot'><i class='bg-$color'></i> $title</span>";
                })
                ->make('true');
    }

    public function getReport(Request $request)
    {
        $report = \DB::table('report')
                ->selectRaw('report.code, customer.fname, customer.lname, restaurant.name, report.status, report.created_at')
                ->join('restaurant_report', 'restaurant_report.report_id', '=', 'report.id')
                ->join('restaurant', 'restaurant.id', '=', 'restaurant_report.restaurant_id')
                ->join('customer_report', 'customer_report.report_id', '=', 'report.id')
                ->join('customer', 'customer.id', '=', 'customer_report.customer_id');
        
        return DataTables::of($report)
                ->filter(function ($query) use ($request) {
                    if ($request->has('search')) {
                        $column = $request->get('column');

                        if ($column != "all") {
                            $query->where($column, "LIKE", "%{$request->get('search')}%");
                        } else {
                            $search = "%".$request->get('search')."%";
                            $query->whereRaw('(report.code LIKE ? OR customer.fname LIKE ? OR customer.lname LIKE ?
                                                OR restaurant.name LIKE ? OR report.created_at LIKE ?)', [
                                                    $search, $search, $search, $search, $search
                                                ]);
                        }
                    }

                    $status = $request->get('show_trash');

                    if ($status != 'all') {
                        $query->where('report.status', $status);
                    }
                })
                ->addColumn('action', function ($report) {
                    $view_btn = "<a class='btn btn-outline-primary btn-sm' href='report/$report->code'><span class='fas fa-eye py-1'></span></a>";
                    return $view_btn;
                })
                ->editColumn('status', function ($report) {
                    $status = $report->status;
                    $title = "Open";
                    $color = "success";
                    if ($status == 1) {
                        $title = "Under Investigation";
                        $color = "info";
                    } else if ($status == 2) {
                        $title = "Closed";
                        $color = "danger";
                    }
                    
                    return "<span class='badge badge-dot'><i class='bg-$color'></i> $title</span>";
                })
                ->make('true');
    }

    public function getRestaurant(Request $request)
    {
        $restaurant = \DB::table('restaurant')->selectRaw('restaurant.id, restaurant.name, restaurant.slug, restaurant.contact_number')
                    ->where('restaurant.status', 1);
        
        return DataTables::of($restaurant)
                ->filter(function ($query) use ($request) {
                    if ($request->has('column')) {
                        $column = $request->get('column');
                        
                        if ($column != "all") {
                            $search = $request->get('search');
                            $query->where($column, "LIKE", "%{$search}%");
                        } else {
                            $search = "%".$request->get('search')."%";
                            $query->whereRaw("(restaurant.id LIKE ? OR restaurant.name LIKE ? OR restaurant.slug LIKE ? OR restaurant.contact_number LIKE ?)", 
                            [
                                $search, $search, $search, $search
                            ]);
                        }
                    }
                })
                ->addColumn('action', function ($restaurant) {
                    $restaurant_id = $restaurant->id;
                    $user = $this->restaurant()->find($restaurant_id)->user()->get()->first();
                    
                    // ban
                    // $ban_btn = "<a class='btn btn-outline-primary btn-sm' href='restaurant/$restaurant->id'><span class='fas fa-eye py-1'></span></a>";
                    $view_btn = "<a class='btn btn-outline-primary btn-sm' href='restaurant/$restaurant->id'><span class='fas fa-eye py-1'></span></a>";
                    return $view_btn;
                })
                ->make('true');
    }

    public function getRestaurantMenu(Request $request)
    {
        $restaurant_id = $request->get('restaurant_id');
        $get = ['menu.name', 'menu.price', 'category.name as category_name', 'menu.created_at', 'menu.id', 'menu.deleted_at'];

        $restaurant_menu = DB::table('menu')
                ->join('restaurant_menu', 'restaurant_menu.menu_id', '=', 'menu.id')
                ->join('restaurant', 'restaurant.id', '=', 'restaurant_menu.restaurant_id')
                ->join('menu_category', 'menu_category.menu_id', '=', 'menu.id')
                ->join('category', 'category.id', '=', 'menu_category.category_id')
                ->where('restaurant.id', $restaurant_id)
                ->select($get);

        return DataTables::of($restaurant_menu)
                ->filter(function ($query) use ($request) {
                    if ($request->has('search')) {
                        $column = $request->get('column');
                        $search = '%'.$request->get('search').'%';
                        if ($column == "all") {
                            $query->whereRaw("(menu.name LIKE ? OR menu.price LIKE ? OR menu.created_at LIKE ? OR menu.deleted_at LIKE ?)",
                            [
                                $search, $search, $search, $search
                            ]);
                        } else {
                            $query->where($column, 'LIKE', "%{$search}%");
                        }
                    }

                    if ($request->has('category')) {
                        $category_id = $request->get('category');
                        if ($category_id != "all") {
                            $query->where('category.id', $category_id);
                        }
                        // $date = 'menu.created_at';
                        // if ($request->get('show_trash') == 'trash') {
                        //     $date = 'menu.deleted_at';
                        // }

                        // $start_date = Carbon::createFromFormat('m-d-Y', $request->get('start_date'))->format('Y-m-d');
                        // $end_date = Carbon::createFromFormat('m-d-Y', $request->get('end_date'))->format('Y-m-d');
                        // $query->whereRaw("(".$date." BETWEEN '".$start_date."' AND '".$end_date."' OR ".$date." LIKE '".$end_date."%')");
                    }

                    $trash = $request->get('show_trash');
                    if ($trash == "without") {
                        $query->whereNull('menu.deleted_at');
                    } else if ($trash == "trash") {
                        $query->whereNotNull('menu.deleted_at');
                    }
                })
                ->addColumn('action', function ($restaurant_menu) use ($restaurant_id) {
                    $menu_id = $restaurant_menu->id;
                    $deleted_at = $restaurant_menu->deleted_at;
                    $route = str_replace('api/', '', route('restaurant.menu.show', ['restaurant_id' => $restaurant_id, 'menu_id' => $menu_id]));
                    $view_btn = "<a class='btn btn-outline-primary btn-sm' href='$route'><span class='fas fa-eye py-1'></span></a>";
                    if ($deleted_at !== null) {
                        $view_btn = "<button type='button' class='btn btn-outline-danger btn-sm' disabled><span class='fas fa-eye py-1'></span></button>";
                    }
                    return $view_btn;
                })
                ->editColumn('created_at', function ($restaurant_menu) {
                    $created_at = Carbon::parse($restaurant_menu->created_at)->format('m-d-Y g:i A');
                    return $created_at;
                })
                ->editColumn('deleted_at', function ($restaurant_menu) {
                    if ($restaurant_menu->deleted_at !== null) {
                        $deleted_at = Carbon::parse($restaurant_menu->deleted_at)->format('m-d-Y g:i A');
                        return $deleted_at;
                    }
                    return "-";
                })
                ->make('true');
    }

    public function getCustomer(Request $request)
    {
        $customer = DB::table('customer')->select(['customer.id', 'customer.fname', 'customer.lname', 'customer.contact_number', 'customer.address', 'customer.created_at'])
                    ->join('user_customer', 'user_customer.customer_id', '=', 'customer.id')
                    ->orderBy('customer.fname', 'ASC');

        return DataTables::of($customer)
                ->filter(function ($query) use ($request) {
                    if ($request->has('search')) {
                        $column = $request->get('column');
                        $search = '%'.$request->get('search').'%';
                        if ($column == "all") {
                            $query->whereRaw("(customer.fname LIKE ? OR customer.lname LIKE ? OR customer.contact_number LIKE ? OR customer.address LIKE ? OR customer.created_at LIKE ?)",
                            [
                                $search, $search, $search, $search, $search
                            ]);
                        } else {
                            $query->where($column, 'LIKE', "%{$search}%");
                        }
                    }
                })

                ->addColumn('action', function ($customer) {
                    $customer_id = $customer->id;
                    $route = str_replace('api/', '', route('customer.show', ['customer_id' => $customer_id]));
                    $view_btn = "<a class='btn btn-outline-primary btn-sm' href='$route'><span class='fas fa-eye py-1'></span></a>";
                    return $view_btn;
                })
                ->make('true');
    }

    public function getCustomerOrder(Request $request)
    {
        $customer_id = $request->get('customer_id');
        $order = DB::table('customer')->select(['orders.code', 'orders.created_at', 'orders.status'])
                ->join('customer_order', 'customer_order.customer_id', '=', 'customer.id')
                ->join('orders', 'orders.id', '=', 'customer_order.order_id')
                ->where('customer.id', $customer_id);
        
        return DataTables::of($order)
                ->filter(function ($query) use ($request) {
                    if ($request->has('search')) {
                        $search = "%".$request->get('search')."%";
                        $query->whereRaw('orders.code LIKE ? OR orders.created_at LIKE ?', [
                            $search, $search
                        ]);
                    }
                })
                ->addColumn('action', function ($order) use ($customer_id) {
                    $route = str_replace('/api', '',(route('customer.order.show', ['customer_id' => $customer_id, 'order_code' => $order->code])));
                    $view_btn = "<a class='btn btn-outline-primary btn-sm' href='$route'><span class='fas fa-eye py-1'></span></a>";
                    return $view_btn;
                })
                ->make('true');
    }

    public function getAdminMenu(Request $request)
    {
        $menu = DB::table('menu')->select(['restaurant.name as restaurant_name', 'menu.name as menu_name', \DB::raw('group_concat(tag.name) as tags'), 'menu.created_at as menu_created_at', 'menu.deleted_at', 'menu.id as menu_id'])
                ->leftJoin('menu_tag', function ($query) {
                    $query->on('menu_tag.menu_id', '=', 'menu.id');
                })
                ->leftJoin('tag', function ($query) {
                    $query->on('tag.id', '=', 'menu_tag.tag_id');
                })
                ->join('restaurant_menu', 'restaurant_menu.menu_id', '=', 'menu.id')
                ->join('restaurant', 'restaurant.id', '=', 'restaurant_menu.restaurant_id')
                ->groupBy('menu_tag.menu_id');

        return DataTables::of($menu)
                ->addColumn('action', function ($menu) {
                    $deleted_at = $menu->deleted_at;
                    $route  = str_replace('api/', '', route('admin.menu.show', ['menu_id' => $menu->menu_id]));
                    $view_btn = "<a class='btn btn-outline-primary btn-sm' href='$route'><span class='fas fa-eye py-1'></span></a>";

                    if ($deleted_at !== null) {
                        $view_btn = "<button type='button' class='btn btn-outline-danger btn-sm' disabled><span class='fas fa-eye py-1'></span></button>";
                    }
                   
                    return $view_btn;
                })
                ->filter(function ($query) use ($request) {
                    if ($request->has('search')) {
                        $column = $request->get('column');
                        $search = '%'.$request->get('search').'%';
                        if ($column == "all") {
                            $query->whereRaw("(restaurant.name LIKE ? OR menu.name LIKE ? OR menu.created_at LIKE ?)",
                            [
                                $search, $search, $search
                            ]);
                        } else {
                            $query->where($column, 'LIKE', "%{$search}%");
                        }
                    }
                
                    if ($request->get('tag') != 'all') {
                        $query->where('tag.name', 'LIKE', "%{$request->get('tag')}%");
                    }
                    
                    $trash = $request->get('show_trash');
                    if ($trash == "without") {
                        $query->whereNull('menu.deleted_at');
                    } else if ($trash == "trash") {
                        $query->whereNotNull('menu.deleted_at');
                    }
                    
                })
                ->editColumn('tags', function ($menu) {
                    if ($menu->tags === null) {
                        // $tags = $this->menu()->find($menu->menu_id)->tag()->get();
                        // $temp = [];
                        // foreach ($tags as $tag) {
                        //     $temp[] = $tag['name'];
                        // }

                        // return implode(',', $temp);
                        return "-";
                    }
                    return $menu->tags;
                })
                ->editColumn('created_at', function ($menu) {
                    $created_at = Carbon::parse($menu->menu_created_at)->format('m-d-Y g:i A');
                    return $created_at;
                })
                ->editColumn('deleted_at', function ($menu) {
                    if ($menu->deleted_at !== null) {
                        $deleted_at = Carbon::parse($menu->deleted_at)->format('m-d-Y g:i A');
                        return $deleted_at;
                    }
                    return "-";
                })
                ->make('true');
    }

    public function getAdminSales(Request $request)
    {
        $sales = \DB::table('restaurant')
                ->where('restaurant.status', 1)
                ->groupBy('restaurant.id');

        return DataTables::of($sales)
                ->filter(function ($query) use ($request) {
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
                                    $query->on('sub_orders.id', '=', 'restaurant_sub_order.sub_order_id')
                                        ->where('sub_orders.status', 3)
                                        ->whereBetween('sub_orders.updated_at', [$start_range, $end_range]);
                                })
                                ->select(['restaurant.id', 'restaurant.name', \DB::raw('SUM(sub_orders.total) as sales,
                                COUNT(sub_orders.id) as orders,
                                SUM(case when sub_orders.origin = "web" then 1 else 0 end) as web_order,
                                SUM(case when sub_orders.origin = "app" then 1 else 0 end) as app_order')]);
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
                })
                ->addColumn('action', function ($sales) use ($request) {
                    $sub_orders = DB::table('sub_orders')->select(['sub_orders.id'])
                                ->join('restaurant_sub_order', 'restaurant_sub_order.sub_order_id', '=', 'sub_orders.id')
                                ->join('restaurant', 'restaurant.id', '=', 'restaurant_sub_order.restaurant_id')
                                ->where('restaurant.id', $sales->id)
                                ->where('sub_orders.status', 3);
                    
                    $radio_filter = $request->get('radio_filter');
                    if ($radio_filter == "specific") {
                        $specific_date = Carbon::createFromFormat('m-d-Y', $request->get('specific_date'))->format('Y-m-d');
                        $sub_orders->whereDate('sub_orders.updated_at', $specific_date);
                    } else {
                        $start_range = Carbon::createFromFormat('m-d-Y', $request->get('start_range'))->startOfDay()->format('Y-m-d H:i:s');
                        $end_range = Carbon::createFromFormat('m-d-Y', $request->get('end_range'))->endOfDay()->format('Y-m-d H:i:s');
                        $sub_orders->whereBetween('sub_orders.updated_at', [$start_range, $end_range]);
                    }

                    $sub_orders = $sub_orders->get()->pluck('id');
                    
                    $orders = DB::table('orders')->select(['orders.id'])
                                ->join('order_sub_order', 'order_sub_order.order_id', '=', 'orders.id')
                                ->join('sub_orders', 'sub_orders.id', '=', 'order_sub_order.sub_order_id')
                                ->whereIn('sub_orders.id', $sub_orders)
                                ->where('sub_orders.status', 3)
                                ->get()->pluck('id')->toArray();

                    $order_array = array_unique($orders);

                    if (count($order_array) == 0) {
                        return "<button type='button' class='btn btn-outline-danger btn-sm' disabled><span class='fas fa-eye py-1'></span></button>";
                    } else {
                        $orders_id = implode(",", $order_array);
                        $route = route('admin.order.index.web', ["status" => 3, "sales" => $orders_id]);
                        $view_btn = "<a class='btn btn-outline-primary btn-sm' href='$route'><span class='fas fa-eye py-1'></span></a>";
                        return $view_btn;
                    }
                })
                ->editColumn('sales', function ($sales) {
                    if ($sales->sales === null) {
                        return "₱ 0.00";
                    }
                    return "₱ ".$sales->sales.".00";
                })
                ->editColumn('orders', function ($sales) {
                    if ($sales->sales === null) {
                        return "0";
                    }
                    return $sales->orders;
                })
                ->make('true');
    }

    public function getAdminSales1(Request $request)
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

        return DataTables::of($shown_items_query)
                ->editColumn('percentage', function ($sales) {
                    return round($sales->percentage)."%";
                })
                ->editColumn('sales', function ($sales) {
                    return "₱ ".$sales->sales.".00";
                })
                ->make('true');
    }

    public function getBan(Request $request)
    {
        $ban = \DB::table('ban')->select(['ban.id as ban_id', 'user.id', 'user.email', 'ban.reason', 'ban.created_at'])
                ->join('user_ban', 'user_ban.ban_id', '=', 'ban.id')
                ->join('user', 'user.id', '=', 'user_ban.user_id');
                
        return DataTables::of($ban)
                ->filter(function ($query) use ($request) {
                    if ($request->has('search')) {
                        $column = $request->get('column');
                        $search = '%'.$request->get('search').'%';
                        if ($column == "all") {
                            $query->whereRaw("(user.id LIKE ? OR user.email LIKE ?)",
                            [
                                $search, $search
                            ]);
                        } else {
                            $query->where($column, 'LIKE', "%{$search}%");
                        }
                    }
                })
                ->addColumn('action', function ($ban) {
                    $ban_id = $ban->ban_id;
                    $user_id = $ban->id;
                    $route = route('ban.destroy', $user_id);
                    $lift_btn = "<button data-toggle='modal' data-target='#lift_ban_confirmation' class='btn btn-outline-warning btn-sm' data-route='$route'><span class='fas fa-lock-open py-1'></span></button>";
                    $route1 = str_replace('/api', '',(route('ban.show', ['ban_id' => $ban_id])));
                    $restaurant = $this->user()->find($user_id)->restaurant()->get()->first();
                    if ($restaurant) {
                        $route = route('admin.restaurant.show', ["restaurant_id" => $restaurant['id']]);
                        $view_btn = "<button data-toggle='modal' data-target='#view_details'
                        data-reason='$ban->reason'
                        data-email_address = '$ban->email'
                        data-view='$route'
                        class='btn btn-outline-primary btn-sm'><span class='fas fa-eye py-1'></span></button>";
                    } else {
                        $view_btn = "<a class='btn btn-outline-primary btn-sm' href='$route1'><span class='fas fa-eye py-1'></span></a>";
                    }
                    return $lift_btn.$view_btn;
                })
                ->make('true');
    }

    public function getLogs(Request $request)
    {
        $user_id = auth()->user()->id;
        
        $logs = DB::table('logs')->select(['logs.ip_address', 'logs.type', 'user.email', 'logs.description', 'logs.origin', 'logs.created_at'])
                ->join('user', 'user.id', '=', 'logs.user_id')
                ->where('logs.user_id', $user_id);

        return DataTables::of($logs)
                ->filter(function ($query) use ($request) {
                    if ($request->has('search')) {
                        $column = $request->get('column');
                        $search = '%'.$request->get('search').'%';
                        if ($column == "all") {
                            $query->whereRaw("(logs.ip_address LIKE ? OR logs.type LIKE ? OR logs.description LIKE ? OR logs.created_at LIKE ?)",
                            [
                                $search, $search, $search, $search
                            ]);
                        } else {
                            $query->where($column, 'LIKE', "%{$search}%");
                        }
                    }

                    $origin = $request->get('origin');
                    if ($origin != "all") {
                        $query->where('logs.origin', $origin);
                    }

                    $type = $request->get('show_trash');
                    if ($type != "all") {
                        $query->where('logs.type', $type);
                    }
                })
                ->editColumn('origin', function ($logs) {
                    return strtoupper($logs->origin);
                })
                ->make('true');
    }

    public function getOwnerSales(Request $request)
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
            $start_range = Carbon::createFromFormat('m-d-Y', $request->get('start_range'))->startOfDay()->format('Y-m-d H:i:s');
            $end_range = Carbon::createFromFormat('m-d-Y', $request->get('end_range'))->endOfDay()->format('Y-m-d H:i:s');
            
            $shown_items = \DB::table('item_list')->select('item_list.id')
                    ->join('sub_order_item_list', 'sub_order_item_list.item_list_id', '=', 'item_list.id')
                    ->join('sub_orders', 'sub_orders.id', '=', 'sub_order_item_list.sub_order_id')
                    ->join('restaurant_item_list', 'restaurant_item_list.item_list_id', '=', 'item_list.id')
                    ->join('restaurant', 'restaurant.id', '=', 'restaurant_item_list.restaurant_id')
                    ->where('restaurant.id', $restaurant_id)
                    ->where('sub_orders.status', 3)
                    ->whereBetween("sub_orders.updated_at", [$start_range, $end_range])
                    ->get()->pluck('id');

            $shown_items1 = \DB::table('item_list')->select('item_list.identifier')
                    ->join('sub_order_item_list', 'sub_order_item_list.item_list_id', '=', 'item_list.id')
                    ->join('sub_orders', 'sub_orders.id', '=', 'sub_order_item_list.sub_order_id')
                    ->join('restaurant_item_list', 'restaurant_item_list.item_list_id', '=', 'item_list.id')
                    ->join('restaurant', 'restaurant.id', '=', 'restaurant_item_list.restaurant_id')
                    ->where('restaurant.id', $restaurant_id)
                    ->where('sub_orders.status', 3)
                    ->whereBetween("sub_orders.updated_at", [$start_range, $end_range])
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
        
        return DataTables::of($shown_items_query)
                    ->editColumn('percentage', function ($sales) {
                        return round($sales->percentage)."%";
                    })
                    ->editColumn('sales', function ($sales) {
                        return "₱ ".$sales->sales.".00";
                    })
                    ->make('true');
    }

    public function getOwnerRestaurantSales(Request $request)
    {
        $restaurant_id = auth()->user()->restaurant()->value('id');

        $interval = $request->get('interval');
        $hidden_rows = [];

        if ($interval == "daily") {
            $daily_input = Carbon::createFromFormat('m-d-Y', $request->get('daily_input'))->format('Y-m-d');

            $query = \DB::table('sub_orders')->selectRaw('HOUR(sub_orders.updated_at) as date, SUM(sub_orders.total) as sales, COUNT(sub_orders.id) as orders')
                ->join('restaurant_sub_order', 'restaurant_sub_order.sub_order_id', '=', 'sub_orders.id')
                ->join('restaurant', 'restaurant.id', '=', 'restaurant_sub_order.restaurant_id')
                ->where('restaurant.id', $restaurant_id)
                ->whereDate('sub_orders.updated_at', $daily_input)
                ->where('sub_orders.status', 3)
                ->groupBy(\DB::raw('HOUR(sub_orders.updated_at)'));
        
            for ($hour = 0; $hour <= 23; $hour++) {
                if (!$query->get()->contains('date', $hour)) {
                    $hidden_rows[] = \DB::table('sub_orders')->selectRaw($hour.' as date, 0 as sales, 0 as orders')->limit(1);
                }
            }

        } else if ($interval == "week-picker-wrapper") {
            $weekly_input = $request->get('weekly_input');
            $weekly_array = explode(' - ', $weekly_input);
            $start_date = Carbon::createFromFormat('m/d/Y', $weekly_array[0])->startOfDay()->format('Y-m-d H:i:s');
            $end_date = Carbon::createFromFormat('m/d/Y', $weekly_array[1])->endOfDay()->format('Y-m-d H:i:s');

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
                ->whereRaw('MONTH(sub_orders.updated_at) = "'.$month_input.'"')
                ->whereRaw('YEAR(sub_orders.updated_at) = "'.$year_input.'"')
                ->where('sub_orders.status', 3)
                ->groupBy(\DB::raw('DAY(sub_orders.updated_at)'));

            for ($day = 1; $day <= intVal($monthly_input->endOfMonth()->format('d')); $day++) {
                if (!$query->get()->contains('date', $day)) {
                    $hidden_rows[] = \DB::table('sub_orders')->selectRaw($day.' as date, 0 as sales, 0 as orders')->limit(1);
                }
            }

        } else {
            $yearly_input = $request->get('yearly_input');
            
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

        return DataTables::of($query)
                ->addColumn('action', function ($query) use ($request, $interval, $restaurant_id) {
                    if ($query->orders != 0) {
                        $sub_order = \DB::table('sub_orders')->select('sub_orders.id')
                                ->join('restaurant_sub_order', 'restaurant_sub_order.sub_order_id', '=', 'sub_orders.id')
                                ->join('restaurant', 'restaurant.id', '=', 'restaurant_sub_order.restaurant_id')
                                ->where('restaurant.id', $restaurant_id)
                                ->where('sub_orders.status', 3);

                        if ($interval == "daily") {
                            $hour = $query->date;
                            $daily_input = Carbon::createFromFormat('m-d-Y', $request->get('daily_input'))->format('Y-m-d');
                            $sub_order->whereRaw('(HOUR(sub_orders.updated_at) = "'.$hour.'" AND DATE(sub_orders.updated_at) = "'.$daily_input.'")');
                        } else if ($interval == "week-picker-wrapper") {
                            $weekly_input = $request->get('weekly_input');
                            $weekly_array = explode(' - ', $weekly_input);
                            $start_date = Carbon::createFromFormat('m/d/Y', $weekly_array[0])->startOfDay()->format('Y-m-d H:i:s');
                            $end_date = Carbon::createFromFormat('m/d/Y', $weekly_array[1])->endOfDay()->format('Y-m-d H:i:s');
                            $sub_order->whereBetween('sub_orders.updated_at', [$start_date, $end_date])
                                    ->whereDate('sub_orders.updated_at', $query->date);
                        } else if ($interval == "monthly") {
                            $monthly_input = Carbon::createFromFormat('m-Y', $request->get('monthly_input'));
                            $month_input = $monthly_input->format('m');
                            $year_input = $monthly_input->format('Y');

                            $sub_order->whereRaw('DAY(sub_orders.updated_at) = "'.$query->date.'"')
                                ->whereRaw('MONTH(sub_orders.updated_at) = "'.$month_input.'"')
                                ->whereRaw('YEAR(sub_orders.updated_at) = "'.$year_input.'"');
                        } else {
                            $yearly_input = $request->get('yearly_input');
                            $sub_order->whereRaw('YEAR(sub_orders.updated_at) = "'.$yearly_input.'"')
                                ->whereRaw('MONTH(sub_orders.updated_at) = "'.$query->date.'"');
                        }

                        $sub_order_ids = [];
                        foreach ($sub_order->get() as $row) {
                            $sub_order_ids[] = $row->id;
                        }
                        
                        $sub_order_str = implode(',', $sub_order_ids);

                        $route = route('owner.order.index', ['status' => 'completed', 'sales' => $sub_order_str]);
                        $view_btn = "<a class='btn btn-outline-success btn-sm' href='$route'><span class='fas fa-eye py-1'></span></a>";
                        return $view_btn;
                    }

                    return "<button type='button' class='btn btn-outline-danger btn-sm' disabled><span class='fas fa-eye py-1'></span></button>";
                })
                ->editColumn('date', function ($query) use ($interval, $request) {
                    if ($interval == "daily") {
                        return Carbon::createFromFormat('H', $query->date)->format('h:i A');
                    } else if ($interval == "monthly") {
                        $monthly_input = Carbon::createFromFormat('m-Y', $request->get('monthly_input'));
                        $month_input = $monthly_input->format('m');
                        $year_input = $monthly_input->format('Y');

                        $date = Carbon::createFromFormat('m-d-Y', $month_input."-".$query->date."-".$year_input)->format('D, m-d-Y');
                        return $date;
                    } else if ($interval == "week-picker-wrapper") {
                        return Carbon::createFromFormat('Y-m-d', $query->date)->format('D, m-d-Y');
                    } else if ($interval == "yearly") {
                        return Carbon::createFromFormat('m-d-Y', $query->date."-01-".$request->get('yearly_input'))->format('D, m-d-Y');
                    }
                    return $query->date;
                })
                // ->editColumn('orders', function ($query) {
                //     if ($query->orders != 0) {
                //         return "<span><a href='#'>".$query->orders."</a></span>";
                //     }
                //     return $query->orders;
                // })
                ->editColumn('sales', function ($query) {
                    return "₱ ".$query->sales.".00";
                })
                ->make('true');
    }

    public function getAdminOrder(Request $request)
    {
        $orders = \DB::table('orders')->select(['orders.id', 'customer.fname', 'customer.lname', 'restaurant.name', 'orders.code', 'orders.status as order_status', 'orders.created_at'])
                ->join('order_sub_order', 'order_sub_order.order_id', '=', 'orders.id')
                ->join('sub_orders', 'sub_orders.id', '=', 'order_sub_order.sub_order_id')
                ->join('restaurant_sub_order', 'restaurant_sub_order.sub_order_id', '=', 'sub_orders.id')
                ->join('restaurant', 'restaurant.id', '=', 'restaurant_sub_order.restaurant_id')
                ->join('customer_order', 'customer_order.order_id', '=', 'orders.id')
                ->join('customer', 'customer.id', '=', 'customer_order.customer_id')
                ->groupBy('orders.id');

        return DataTables::of($orders)
                ->filter(function ($orders) use ($request) {
                    if ($request->has('search')) {
                        $column = $request->get('column');
                        $search = '%'.$request->get('search').'%';
                        if ($column == "all") {
                            $orders->whereRaw("(orders.code LIKE ? OR customer.fname LIKE ? OR customer.lname LIKE ? OR orders.created_at LIKE ? OR restaurant.name LIKE ?)",
                            [
                                $search, $search, $search, $search, $search
                            ]);
                        } else if ($column == "customer.fname") {
                            $orders->whereRaw("(customer.fname LIKE ? OR customer.lname LIKE ?)", [
                                $search, $search
                            ]);
                        } else {
                            $orders->where($column, 'LIKE', "%{$search}%");
                        }
                    }

                    if ($request->has('show_trash')) {
                        $status = $request->get('show_trash');
                        if ($status != "all" && $status != "process") {
                            $orders->where('orders.status', $status);
                        } else if ($status == "process") {
                            $orders->whereIn('orders.status', [0, 1, 2]);
                        }
                    }

                    if ($request->has('sales')) {
                        if ($request->get('sales') !== null) {
                            $order_id_array = explode(',', $request->get('sales'));
                            $orders->whereIn('orders.id', $order_id_array);
                        }
                    }
                })
                ->editColumn('fname', function ($order) {
                    return ucfirst($order->fname)." ".ucfirst($order->lname);
                })
                ->editColumn('status', function ($order) {
                    $status = $order->order_status;
                    $title = "pending";
                    $color = "warning";
                    if ($status == 1) {
                        $title = "processing";
                        $color = "primary";
                    } else if ($status == 2) {
                        $title = "delivering";
                        $color = "info";
                    } else if ($status == 3) {
                        $title = "completed";
                        $color = "success";
                    } else if ($status == 4) {
                        $title = "rejected";
                        $color = "danger";
                    } else if ($status == 5) {
                        $title = "cancelled";
                        $color = "danger";
                    }
                    return "<span class='badge badge-dot'><i class='bg-$color'></i> $title</span>";
                })
                ->escapeColumns([])
                ->addColumn('action', function ($order) {
                    $route = route('admin.order.show.web', $order->id);
                    $view_btn = "<a class='btn btn-outline-primary btn-sm' href='$route'><span class='fas fa-eye py-1'></span></a>";
                    return $view_btn;
                })
                ->make('true');
    }

    public function getRestaurantLogs($restaurant_id, Request $request)
    {
        $logs = DB::table('logs')->select(['logs.ip_address', 'logs.type', 'user.email', 'logs.description', 'logs.origin', 'logs.created_at'])
                ->join('user', 'user.id', '=', 'logs.user_id')
                ->join('user_restaurant', 'user_restaurant.user_id', '=', 'user.id')
                ->join('restaurant', 'restaurant.id', '=', 'user_restaurant.restaurant_id')
                ->where('restaurant.id', $restaurant_id);

        return DataTables::of($logs)
                ->filter(function ($query) use ($request) {
                    if ($request->has('search')) {
                        $column = $request->get('column');
                        $search = '%'.$request->get('search').'%';
                        if ($column == "all") {
                            $query->whereRaw("(logs.ip_address LIKE ? OR logs.type LIKE ? OR logs.description LIKE ? OR logs.created_at LIKE ?)",
                            [
                                $search, $search, $search, $search
                            ]);
                        } else {
                            $query->where($column, 'LIKE', "%{$search}%");
                        }
                    }

                    $origin = $request->get('origin');
                    if ($origin != "all") {
                        $query->where('logs.origin', $origin);
                    }

                    $type = $request->get('show_trash');
                    if ($type != "all") {
                        $query->where('logs.type', $type);
                    }
                })
                ->editColumn('origin', function ($logs) {
                    return strtoupper($logs->origin);
                })
                ->make('true');
    }

    public function getCustomerLogs($customer_id, Request $request)
    {
        $logs = DB::table('logs')->select(['logs.ip_address', 'logs.type', 'user.email', 'logs.description', 'logs.origin', 'logs.created_at'])
                ->join('user', 'user.id', '=', 'logs.user_id')
                ->join('user_customer', 'user_customer.user_id', '=', 'user.id')
                ->join('customer', 'customer.id', '=', 'user_customer.customer_id')
                ->where('customer.id', $customer_id);

        return DataTables::of($logs)
                ->filter(function ($query) use ($request) {
                    if ($request->has('search')) {
                        $column = $request->get('column');
                        $search = '%'.$request->get('search').'%';
                        if ($column == "all") {
                            $query->whereRaw("(logs.ip_address LIKE ? OR logs.type LIKE ? OR logs.description LIKE ? OR logs.created_at LIKE ?)",
                            [
                                $search, $search, $search, $search
                            ]);
                        } else {
                            $query->where($column, 'LIKE', "%{$search}%");
                        }
                    }

                    $origin = $request->get('origin');
                    if ($origin != "all") {
                        $query->where('logs.origin', $origin);
                    }

                    $type = $request->get('show_trash');
                    if ($type != "all") {
                        $query->where('logs.type', $type);
                    }
                })
                ->editColumn('origin', function ($logs) {
                    return strtoupper($logs->origin);
                })
                ->make('true');
    }

    public function getAdminRestaurantOrder($restaurant_id, Request $request)
    {
        $sub_order = \DB::table('orders')
                ->selectRaw('sub_orders.id, orders.code, sub_orders.status, orders.created_at')
                ->join('order_sub_order', 'order_sub_order.order_id', '=', 'orders.id')
                ->join('sub_orders', 'sub_orders.id', '=', 'order_sub_order.sub_order_id')
                ->join('restaurant_sub_order', 'restaurant_sub_order.sub_order_id', '=', 'sub_orders.id')
                ->join('restaurant', 'restaurant.id', '=', 'restaurant_sub_order.restaurant_id')
                ->where('restaurant.id', $restaurant_id)
                ->groupBy('sub_orders.id')
                ->orderBy('orders.created_at', 'DESC');

        return DataTables::of($sub_order)
                ->filter(function ($query) use ($request) {
                    $status = $request->get('show_trash');
                    
                    if ($status == "process") {
                        $query->whereIn('sub_orders.status', [0, 1, 2]);
                    } else if ($status == "completed") {
                        $query->where('sub_orders.status', 3);
                    } else if ($status == "rejected") {
                        $query->where('sub_orders.status', 4);
                    } else if ($status == "cancelled") {
                        $query->where('sub_orders.status', 5);
                    }
                })
                ->addColumn('action', function ($sub_order) use ($restaurant_id) {
                    $route = route('admin.restaurant.order', ["restaurant_id" => $restaurant_id, "sub_order_id" => $sub_order->id]);
                    $view_btn = "<a class='btn btn-outline-primary btn-sm' href='$route'><span class='fas fa-eye py-1'></span></a>";
                    return $view_btn;
                })
                ->editColumn('status', function ($sub_order) {
                    $status = $sub_order->status;
                    $title = "pending";
                    $color = "warning";
                    if ($status == 1) {
                        $title = "processing";
                        $color = "primary";
                    } else if ($status == 2) {
                        $title = "delivering";
                        $color = "info";
                    } else if ($status == 3) {
                        $title = "completed";
                        $color = "success";
                    } else if ($status == 4) {
                        $title = "rejected";
                        $color = "danger";
                    } else if ($status == 5) {
                        $title = "cancelled";
                        $color = "danger";
                    }
                    
                    return "<span class='badge badge-dot'><i class='bg-$color'></i> $title</span>";
                })
                ->make('true');
    }

    public function getAdminRestaurantReport($restaurant_id, Request $request)
    {
        $reports = \DB::table('report')->select(['report.code as report_code', 'orders.code as order_code', 'report.status', 'report.created_at'])
                    ->join('sub_order_report', 'sub_order_report.report_id', '=', 'report.id')
                    ->join('sub_orders', 'sub_orders.id', '=', 'sub_order_report.sub_order_id')
                    ->join('order_sub_order', 'order_sub_order.sub_order_id', '=', 'sub_orders.id')
                    ->join('orders', 'orders.id', '=', 'order_sub_order.order_id')
                    ->join('restaurant_report', 'restaurant_report.report_id', '=', 'report.id')
                    ->join('restaurant', 'restaurant.id', '=', 'restaurant_report.restaurant_id')
                    ->where('restaurant.id', $restaurant_id);

        return Datatables::of ($reports)
                ->filter(function ($query) use ($request) {
                    $status = $request->get('show_trash');
                    if ($status != "all") {
                        $query->where('report.status', $status);
                    }
                })
                ->editColumn('status', function ($report) {
                    $status = $report->status;
                    $title = "Open";
                    $color = "success";
                    if ($status == 1) {
                        $title = "Under Investigation";
                        $color = "info";
                    } else if ($status == 2) {
                        $title = "Closed";
                        $color = "danger";
                    }
                    
                    return "<span class='badge badge-dot'><i class='bg-$color'></i> $title</span>";
                })
                ->addColumn('action', function ($reports) {
                    $route = route('admin.report.show', ['report_code' => $reports->report_code]);
                    $view_btn = "<a class='btn btn-outline-primary btn-sm' href='$route'><span class='fas fa-eye py-1'></span></a>";
                    return $view_btn;
                })
                ->make('true');
    }

    public function getAdminCustomerReport($customer_id, Request $request)
    {
        $reports = DB::table('report')->select(['report.code as report_code', 'orders.code as order_code', 'restaurant.name', 'report.status', 'report.created_at'])
                    ->join('sub_order_report', 'sub_order_report.report_id', '=', 'report.id')
                    ->join('sub_orders', 'sub_orders.id', '=', 'sub_order_report.sub_order_id')
                    ->join('order_sub_order', 'order_sub_order.sub_order_id', '=', 'sub_orders.id')
                    ->join('orders', 'orders.id', '=', 'order_sub_order.order_id')
                    ->join('restaurant_report', 'restaurant_report.report_id', '=', 'report.id')
                    ->join('restaurant', 'restaurant.id', '=', 'restaurant_report.restaurant_id')
                    ->join('customer_report', 'customer_report.report_id', '=', 'report.id')
                    ->join('customer', 'customer.id', '=', 'customer_report.customer_id')
                    ->where('customer.id', $customer_id);

        return Datatables::of ($reports)
                    ->filter(function ($query) use ($request) {
                        $status = $request->get('show_trash');
                        if ($status != "all") {
                            $query->where('report.status', $status);
                        }
                    })
                    ->editColumn('status', function ($report) {
                        $status = $report->status;
                        $title = "Open";
                        $color = "success";
                        if ($status == 1) {
                            $title = "Under Investigation";
                            $color = "info";
                        } else if ($status == 2) {
                            $title = "Closed";
                            $color = "danger";
                        }
                        
                        return "<span class='badge badge-dot'><i class='bg-$color'></i> $title</span>";
                    })
                    ->addColumn('action', function ($reports) {
                        $route = route('admin.report.show', ['report_code' => $reports->report_code]);
                        $view_btn = "<a class='btn btn-outline-primary btn-sm' href='$route'><span class='fas fa-eye py-1'></span></a>";
                        return $view_btn;
                    })
                    ->make('true');
    }

    public function getAdminUsers(Request $request)
    {
        $users = DB::table('user')->select(['id', 'email', 'email_verified_at', 'access_level', 'created_at']);

        return Datatables::of ($users)
                ->filter(function ($query) use ($request) {
                    if ($request->has('search')) {
                        $column = $request->get('column');
                        $search = '%'.$request->get('search').'%';
                        if ($column == "all") {
                            $query->whereRaw("(user.id LIKE ? OR user.email LIKE ?)",
                            [
                                $search, $search
                            ]);
                        } else {
                            $query->where($column, 'LIKE', "%{$request->get('search')}%");
                        }
                    }

                    $access_level = $request->get('show_trash');
                    if ($access_level != "all") {
                        $query->where('user.access_level', $access_level);
                    }
                })
                ->editColumn('email_verified_at', function ($users) {
                    $verified = $users->email_verified_at;
                    if ($verified === null) {
                        return "<span class='badge badge-dot'><i class='bg-danger'></i> Not Verified</span>";
                    }

                    return "<span class='badge badge-dot'><i class='bg-success'></i> Verified</span>";
                })
                ->editColumn('access_level', function ($users) {
                    $access_level = $users->access_level;

                    if ($access_level == 1) {
                        return "Customer";
                    } else if ($access_level == 2) {
                        return "Restaurant";
                    } else if ($access_level == 3) {
                        return "Administrator";
                    }
                })
                ->addColumn('action', function ($users) {
                    $user_id = $users->id;

                    $user = $this->user()->find($user_id);
                    $restaurant = $user->restaurant()->get()->first();
                    $customer = $user->customer()->get()->first();
                    if ($restaurant) {
                        $route = route('admin.restaurant.show', ["restaurant_id" => $restaurant['id']]);
                        return "<a class='btn btn-outline-primary btn-sm' href='$route'><span class='fas fa-eye py-1'></span></a>";
                    } else if ($customer) {
                        $route = str_replace('api/', '', route('customer.show', ['customer_id' => $customer['id']]));
                        return "<a class='btn btn-outline-primary btn-sm' href='$route'><span class='fas fa-eye py-1'></span></a>";
                    }

                    return "<button type='button' class='btn btn-outline-danger btn-sm' disabled><span class='fas fa-eye py-1'></span></button>";
                })
                ->make('true');
    }

    public function getChangeRequest(Request $request)
    {
        $change_request = DB::table('change_request')->select(['old_email', 'new_email', 'status', 'created_at', 'reason', 'id']);

        return Datatables::of ($change_request)
                ->filter(function ($query) use ($request) {
                    if ($request->has('search')) {
                        $column = $request->get('column');
                        $search = '%'.$request->get('search').'%';
                        if ($column == "all") {
                            $query->whereRaw("(old_email LIKE ? OR new_email LIKE ?)",
                            [
                                $search, $search
                            ]);
                        } else {
                            $query->where($column, 'LIKE', "%{$request->get('search')}%");
                        }
                    }

                    $status = $request->get('show_trash');
                    if ($status != "all") {
                        $query->where('status', $status);
                    }
                })
                ->editColumn('status', function ($change_request) {
                    $status = $change_request->status;
                    $title = "rejected";
                    $color = "danger";
                    if ($status == 0) {
                        $title = "pending";
                        $color = "warning";
                    } else if ($status == 1) {
                        $title = "accepted";
                        $color = "success";
                    }

                    return "<span class='badge badge-dot'><i class='bg-$color'></i> $title</span>";
                })
                ->addColumn('action', function ($change_request) {
                    $user_info = $this->user()->where('email', $change_request->old_email)->get()->first();
                    $user = $this->user()->find($user_info['id']);
                    $restaurant = $user->restaurant()->get()->first();
                    $customer = $user->customer()->get()->first();
                    $request_view = "";
                    if ($restaurant) {
                        $route = route('admin.restaurant.show', ["restaurant_id" => $restaurant['id']]);
                        $request_view = $route;
                    } else if ($customer) {
                        $route = str_replace('api/', '', route('customer.show', ['customer_id' => $customer['id']]));
                        $request_view = $route;
                    }

                    $view_btn = "<button type='button' data-toggle='modal' data-target='#show_request' class='btn btn-outline-primary btn-sm' 
                        data-old='$change_request->old_email'
                        data-new='$change_request->new_email'
                        data-status='$change_request->status'
                        data-view = '$request_view'
                        data-reason='$change_request->reason'><span class='fas fa-eye py-1'></span></button>";
                        $accept_btn = "<button class='btn btn-outline-success btn-sm' data-toggle='modal' data-target='#accept_confirmation' data-id='$change_request->id'><span class='fas fa-check py-1'></span></button>";
                        $decline_btn = "<button class='btn btn-outline-danger btn-sm' data-toggle='modal' data-target='#reject_confirmation' data-id='$change_request->id'><span class='fas fa-times' style='padding: 0.3rem 0.2rem'></span></button>";
                    
                    $status = $change_request->status;
                    if ($status == 0) {
                        return $view_btn.$accept_btn.$decline_btn;
                    }

                    return $view_btn;
                })
                ->make('true');
    }

    public function getAllLogs(Request $request)
    {
        $logs = DB::table('logs')->select(['logs.ip_address', 'logs.type', 'user.email', 'logs.description', 'logs.origin', 'logs.created_at', 'user.id'])
                ->leftJoin('user', 'user.id', '=', 'logs.user_id');

        return Datatables::of ($logs)
                ->filter(function ($query) use ($request) {
                    if ($request->has('search')) {
                        $column = $request->get('column');
                        $search = '%'.$request->get('search').'%';
                        if ($column == "all") {
                            $query->whereRaw("(user.email LIKE ? OR logs.ip_address LIKE ? OR logs.type LIKE ? OR logs.description LIKE ? OR logs.created_at LIKE ?)",
                            [
                                $search, $search, $search, $search, $search
                            ]);
                        } else {
                            $query->where($column, 'LIKE', "%{$search}%");
                        }
                    }

                    $origin = $request->get('origin');
                    if ($origin != "all") {
                        $query->where('logs.origin', $origin);
                    }

                    $type = $request->get('show_trash');
                    if ($type != "all") {
                        $query->where('logs.type', $type);
                    }
                })
                ->editColumn('origin', function ($logs) {
                    return strtoupper($logs->origin);
                })
                ->addColumn('action', function ($logs) {
                    $user_id = $logs->id;
                        if ($user_id !== null) {
                            $user = $this->user()->find($user_id);
                        $restaurant = $user->restaurant()->get()->first();
                        $customer = $user->customer()->get()->first();
                        if ($restaurant) {
                            $route = route('admin.restaurant.show', ["restaurant_id" => $restaurant['id']]);
                            return "<a class='btn btn-outline-primary btn-sm' href='$route'><span class='fas fa-eye py-1'></span></a>";
                        } else if ($customer) {
                            $route = str_replace('api/', '', route('customer.show', ['customer_id' => $customer['id']]));
                            return "<a class='btn btn-outline-primary btn-sm' href='$route'><span class='fas fa-eye py-1'></span></a>";
                        }   
                        
                        return "<button type='button' class='btn btn-outline-danger btn-sm' disabled><span class='fas fa-eye py-1'></span></button>";
                    }
                    return "";
                })
                ->make('true');
    }
}
