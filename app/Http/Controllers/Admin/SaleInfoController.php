<?php
namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class SaleInfoController extends Controller
{
    //saleInfoList
    public function saleInfoList()
    {
        $today = Carbon::today();
        $order = Order::select('orders.id as order_id', 'orders.totalPrice as price',
            'orders.created_at', 'orders.order_code', 'orders.status',
            'users.name as username', 'products.name as productname',
            'products.image as productimage', 'orders.count as ordercount')
            ->leftJoin('products', 'orders.product_id', 'products.id')
            ->leftJoin('users', 'orders.user_id', 'users.id')
            ->where('orders.status', 1)
            ->whereDate('orders.created_at', $today)
            ->groupBy('orders.order_code')
            ->orderBy('orders.created_at', 'desc')
            ->paginate(5);
        // dd($order->toArray());

        return view('admin.saleInfo.list', compact('order'));
    }

    public function salesReportPage()
    {
        return view('admin.saleInfo.salesReport');
    }

    public function salesReport(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate   = $request->input('end_date');

        $sales = Order::select('orders.id as order_id', 'orders.totalPrice as price',
                                'orders.created_at', 'orders.order_code', 'products.count as instock',
                                'products.name as productname',
                                'orders.count as sold', 'orders.created_at as date')
                        ->leftJoin('products', 'orders.product_id', 'products.id')
                        ->where('orders.status', 1)
                        ->whereDate('orders.created_at', '>=', $startDate)
                        ->whereDate('orders.created_at', '<=', $endDate)
                        ->groupBy(DB::raw('DATE(orders.created_at)'))
                        ->orderBy('orders.id')
                        ->get();

        //   dd($sales->toArray());
        return view('admin.saleInfo.salesReport', compact('sales','startDate', 'endDate'));
    }



    public function profitlossreportpage(){
        return view ('admin.saleInfo.profit&lost');
    }

    public function profitlossReport(Request $request){
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $productsales = Product::join('orders', 'products.id', '=', 'orders.product_id')
                                ->select(
                                    'products.id',
                                    'products.name',
                                    'products.price as sell_price',
                                    'products.purchase_price',
                                    DB::raw('SUM(orders.count) as units_sold'),
                                    DB::raw('(products.price - products.purchase_price) * SUM(orders.count) as total_profit')
                                )
                                ->whereBetween('orders.created_at', [$startDate, $endDate])
                                ->groupBy('products.id', 'products.name', 'products.price', 'products.purchase_price')
                                ->get();

        // dd($productsales->toArray());

    return view('admin.saleInfo.profit&lost', compact('productsales','startDate', 'endDate'));
    }
}
