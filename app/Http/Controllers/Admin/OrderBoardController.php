<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Models\Order;
use Illuminate\Http\Request;
use App\Models\PaySlipHistory;
use App\Http\Controllers\Controller;

class OrderBoardController extends Controller
{
    //
    public function orderListPage(){
        $today = Carbon::today();
        $order = Order::select('orders.id as order_id','orders.totalPrice as price',
                               'orders.created_at','orders.order_code','orders.status',
                               'users.id as user_id','users.name as username','products.name as productname')
                      ->leftJoin('products','orders.product_id','products.id')
                      ->leftJoin('users','orders.user_id','users.id')
                      ->groupBy('orders.order_code')
                      ->orderBy('orders.created_at','desc')
                      ->whereDate('orders.created_at', $today)
                      ->paginate(3);

            // dd($order->toArray());

        return view('admin.orderBoard.list',compact('order'));
    }

    //check order products
    public function userOrderDetails($orderCode){
        // dd($orderCode);
        $order = Order::select('users.name as customername','users.phone','orders.created_at','products.image as productimage','products.name as productname','products.price as productprice','orders.order_code','orders.count as ordercount')
                        ->leftJoin('products','orders.product_id','products.id')
                        ->leftJoin('users','orders.user_id','users.id')
                        ->where('orders.order_code',$orderCode)
                        ->get();

        $orderState = Order::select('order_code', 'status')
                        ->groupBy('order_code', 'status')
                        ->where('orders.order_code',$orderCode)
                        ->get();

                        // dd($orderState->toArray());
        $paySlipData = PaySlipHistory::select('pay_slip_histories.*','payments.type as payment_type')
                                    ->leftJoin('payments','pay_slip_histories.payment_method','payments.id')
                                    ->where('pay_slip_histories.order_code',$orderCode)
                                    ->first();
        // dd($paySlipData->toArray());

        $total = 0;
        foreach($order as $item){
            $total += $item->ordercount * $item->productprice;
        }

        return view('admin.orderBoard.details',compact('order','total','paySlipData','orderState'));
    }


    public function changeStatus(Request $request){
        // logger($request->all());
        Order::where('order_code',$request->orderCode)->update([
            'status' => $request->status
        ]);
    }


    public function updateStatus(Request $request) {
        $request->validate([
            'id' => 'required',
            'status' => 'required|in:0,1,2',
        ]);

        $order = Order::where('order_code', $request->id)->first();

        if (!$order) {
            return response()->json(['error' => 'Order not found'], 404);
        }

        // Update the status
        $order->status = $request->status;

        // If changing from "Reject" to another status, clear reject reason
        if ($order->status != 2) {
            $order->reject_reason = null;
        }

        $order->save();

        return response()->json(['success' => 'Order status updated successfully!']);
    }

    // For reject option, need to provide a reason
    public function rejectOrder(Request $request) {
        $order = Order::where('order_code', $request->order_code)->first();

        if (!$order) {
            return response()->json(['error' => 'Order not found'], 404);
        }

        $order->status = 2; // Set to Rejected
        $order->reject_reason = $request->reason; // Save the reason
        $order->save();

        return response()->json(['success' => 'Order rejected with reason']);
    }

    // In case admin chose "Reject" but wants to change it back
    public function removeRejectReason(Request $request) {
        $order = Order::where('order_code', $request->order_code)->first();

        if (!$order) {
            return response()->json(['error' => 'Order not found'], 404);
        }

        // Always allow status change and reset reject reason
        $order->status = $request->status;
        $order->reject_reason = null;
        $order->save();

        return response()->json(['success' => 'Order status updated, reject reason removed']);
    }


}
