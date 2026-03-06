<?php

namespace App\Http\Controllers\User;

use App\Models\Cart;
use App\Models\Order;
use App\Models\Rating;
use App\Models\Comment;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Models\PaySlipHistory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use RealRashid\SweetAlert\Facades\Alert;

class ShopController extends Controller
{
    /**
     * Display the list of products in the shop.
     * Filters products by search key and price range.
     * Filtering by category is now handled via request query parameters (e.g., ?category_id=X).
     */
    public function shop(){ // Le paramètre $category_id est retiré de la signature de la méthode
        // Retrieve product data
        $products = Product::when(request('searchKey'),function($query){
            $query->where('products.name','like','%'.request('searchKey').'%');
        });

        // Price range filtering logic
        if(request('minPrice') != null && request('maxPrice') != null){
            $products = $products->whereBetween('products.price',[request('minPrice'),request('maxPrice')]);
        }

        if(request('minPrice') != null && request('maxPrice') == null){
            $products = $products->where('products.price','>=', request('minPrice'));
        }

        if(request('minPrice') == null && request('maxPrice') != null){
            $products = $products->where('products.price','<=',request('maxPrice'));
        }

        // Join categories to display category name (assuming the Product -> Category relationship is retained)
        $products = $products->select('products.*','categories.name as category_name')
                             ->leftJoin('categories','products.category_id','categories.id');

        // Check for filtering by category ID via request (alternative to URL parameter)
        // This is added to keep category filtering capability if needed, without using a URL segment.
        if (request('category_id') !== null) {
             $products->where('products.category_id', request('category_id'));
        }

        // Paginate the results (la logique conditionnelle basée sur un paramètre d'URL est supprimée)
        $products = $products->paginate(9);

        return view('user.shop',compact('products','categories'));
    }


    //direct shop's product details
    public function details($id){
        // dd($id);
        $product = Product::select('products.id','products.name','products.price','products.description',
                                     'products.count as stock_count','products.category_id','products.image',
                                     'categories.name as category_name',
                                     DB::raw('products.count - IFNULL(SUM(orders.count), 0) as remaining_stock'))
                                 ->leftJoin('categories','products.category_id','categories.id')
                                 ->leftJoin('orders', function($join) {
                                     $join->on('products.id', '=', 'orders.product_id')
                                         ->where('orders.status', 1);
                                 })
                                 ->where('products.id',$id)
                                 ->groupBy(
                                     'products.id',
                                     'products.name',
                                     'products.price',
                                     'products.description',
                                     'products.count',
                                     'products.category_id',
                                     'products.image',
                                     'categories.name'
                                 )
                                 ->first();

                        // dd($product->toArray());

        $comment = Comment::select('comments.*','users.name as username','users.profile as userprofile')
                            ->leftJoin('users','comments.user_id','users.id')
                            ->where('comments.product_id',$id)
                            ->orderBy('created_at','desc')
                            ->get();

        $productRating = Rating::where('product_id',$id)->avg('count');

        $ratingCount = Rating::where('product_id',$id)->get();

        $user_Rating = Rating::select('count')->where('product_id',$id)->where('user_id',Auth::user()->id)->first();

        if ($user_Rating) {
            $user_Rating = $user_Rating['count'];
        } else {
            $user_Rating = 0;
        }
        // dd($user_Rating);

        $productList = Product::select('products.id','products.name','products.price','products.description','products.count','products.category_id','products.image','categories.name as category_name')
                             ->leftJoin('categories','products.category_id','categories.id')
                             ->get();

        $topProducts = Order::select(
                             'product_id','products.name as product_name','products.price','products.image',
                                 DB::raw('SUM(orders.count) as total_sold')
                             )
                             ->leftJoin('products', 'orders.product_id', '=', 'products.id')
                             ->groupBy('product_id')
                             ->orderBy('total_sold', 'desc')
                             ->take(3) // Top 5 products
                             ->get();


        $bestProduct =Product::find($id);

        return view('user.details',compact('product','comment','productRating','ratingCount','user_Rating',
                                             'productList','bestProduct','topProducts'));
    }

    //comment section
    public function comment(Request $request){
        $request->validate([
            'message' => 'required'
        ]);

        $data =[
            'product_id' => $request->productID,
            'user_id' =>$request->userID,
            'message' =>$request->message,
        ];
        Comment::create($data);

        Alert::success('Comment Success', 'Your Comment Successfully....');

        return back();
    }

    //add Rating
    public function addRating(Request $request){
        // dd($request->all());
        $ratingCheckData = Rating::where('product_id',$request->productID)->where('user_id',$request->userID)->first();
        // dd($ratingCheckData->toArray());

       if ($ratingCheckData == null) {
        Rating::create([
            'product_id' =>$request->productID,
            'user_id' =>$request->userID,
            'count' =>$request->productRating
        ]);
       } else {
        Rating::where('product_id',$request->productID)->where('user_id',$request->userID)->update([
                'count' => $request->productRating
        ]);

       }
        Alert::success('Rating Success', 'Rating Successfully....');

        return back();
    }

    //cart page
    public function cart(){
        $id = Auth::user()->id;
        $cart = Cart::select('carts.*','products.name','products.price','products.image')
                     ->leftJoin('products','carts.product_id','products.id')
                     ->where('user_id',$id)
                     ->get();

        $totalPrice = 0;
        foreach($cart as $item){
            $totalPrice += $item->price*$item->qty;
        }

        $payment = Payment::get();

        return view('user.cart',compact('cart','totalPrice','payment'));
    }

    // add to cart
    public function addToCart(Request $request){
        // dd($request->all());
        $productId = $request->productID;
        $qty     = $request->qty;
        $userId = Auth::user()->id;

        Cart::create([
            'user_id' => $userId,
            'product_id' => $productId,
            'qty' =>$qty
        ]);
        // return to_route('cart');
        return redirect()->route('cart')->with('success', 'Product added to cart!');
    }


    //remove cart by cart_id
    public function removeCart(Request $request){
        // dd($request->all());
        // logger ($request->productId);
        Cart::where('id',$request->cartId)->delete();

        $serverResponse =[
            'message' => 'success'
        ];
        return response()->json($serverResponse,200);
    }

    public function order(Request $request){

            $orderArr =[];
            foreach($request->all() as $item){
                array_push($orderArr,[
                    'user_id' => $item['user_id'],
                    'product_id' => $item['product_id'],
                    'status' => 0,
                    'order_code' => $item['order_code'],
                    'count' => $item['qty'],
                    'totalPrice' => $item['total_price']
                ]);
            }
            Session::put('orderList', $orderArr);
            // logger($orderArr);
            // return the response after all items have been processed
           return response()->json([
                "message" => 'success',
                 "status" => 200
           ],200);
        }

    //direct order list
    public function orderList(){

        $order = Order::where('user_id',Auth::user()->id)
                        ->orderBy('created_at','desc')
                        ->groupBy('order_code')
                        ->get();
        return view('user.orderList',compact('order'));
    }

    //Customer Order details from Order Code
    public function customerOrders($orderCode){
        $order = Order::select('products.image as productimage',
                               'products.name as productname',
                               'products.price as productprice',
                               'orders.order_code',
                               'orders.count as ordercount')
                        ->leftJoin('products','orders.product_id','products.id')
                        ->where('orders.order_code',$orderCode)
                        ->paginate(2);
        return view ('user.userOrderDetails',compact('order'));
    }

    //direct payment page
    public function userPayment(){
        $orderProduct = Session::get('orderList');
        $payment =Payment::orderBy('type','asc')->get();

        $total = 0;
        foreach($orderProduct as $item){
            $total += $item['totalPrice'];
        }
        // dd($total);
        return view('user.payment',compact('payment','total','orderProduct'));
    }

    //order Product
    public function orderProduct(Request $request){

        // dd($request->all());
        $validator = $request->validate([
            'name' => 'required',
            'phone' => 'required',
            'paymentMethod' => 'required',
            'paySlipImage' => 'required',

        ]);

        $cartProduct = Session::get('orderList');
        // dd($cartProduct);

        foreach($cartProduct as $item){
            Order::create($item);
            Cart::where('user_id',$item['user_id'])->where('product_id', $item['product_id'])->delete();
        }
        // dd($request->all());

        $data = [
            'customer_name' => $request->name,
            'phone' => $request->phone,
            'payment_method' => $request->paymentMethod,
            'order_code' => $request->orderCode,
            'order_amount' => $request->totalAmount,

        ];

        if($request->hasFile('paySlipImage')){
            $fileName = uniqid() . $request->file('paySlipImage')->getClientOriginalName();
            $request->file('paySlipImage')->move(public_path(). '/payslipRecords/' , $fileName);
            $data['payslip_image'] = $fileName;
        }

       PaySlipHistory::create($data);
       return to_route('orderList');

    }


}