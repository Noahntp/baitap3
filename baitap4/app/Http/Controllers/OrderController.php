<?php
namespace App\Http\Controllers;

use App\Models\Order;
use App\Http\Requests\CreateOrderRequest;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class OrderController extends Controller {
    protected $orderService;
    public function __construct(OrderService $orderService) {
        $this->orderService = $orderService;
    }

    public function index(Request $request) {
        $orders = Order::with('items.course')->where('user_id', $request->user()->id)->latest()->get();
        return response()->json(['success' => true, 'data' => $orders]);
    }

    public function show(Request $request, Order $order) {
        Gate::authorize('viewOrPay', $order); // PAY-06
        return response()->json(['success' => true, 'data' => $order->load('items.course')]);
    }

    public function store(CreateOrderRequest $request) {
        try {
            $order = $this->orderService->createOrder($request->user()->id, $request->cart_item_ids);
            return response()->json(['success' => true, 'message' => 'Tạo đơn hàng thành công.', 'data' => $order], 201);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }

    public function payDemo(Request $request, Order $order) {
        Gate::authorize('viewOrPay', $order); // PAY-06

        $request->validate(['method' => 'required|string']);

        try {
            $this->orderService->processDemoPayment($order, $request->method);
            return response()->json(['success' => true, 'message' => 'Thanh toán thành công. Bạn đã có thể vào học!']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }
}
