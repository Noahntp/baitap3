<?php
namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItem;
use App\Http\Requests\AddToCartRequest;
use App\Services\CartService;
use Illuminate\Http\Request;

class CartController extends Controller {
    protected $cartService;
    public function __construct(CartService $cartService) {
        $this->cartService = $cartService;
    }

    public function index(Request $request) {
        $cart = Cart::with('items.course')->firstOrCreate(['user_id' => $request->user()->id]);
        return response()->json(['success' => true, 'data' => $cart]);
    }

    public function store(AddToCartRequest $request) {
        try {
            $item = $this->cartService->addItem($request->user()->id, $request->course_id);
            return response()->json(['success' => true, 'message' => 'Đã thêm vào giỏ hàng.', 'data' => $item], 201);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    public function destroy(Request $request, $cartItemId) {
        $item = CartItem::whereHas('cart', fn($q) => $q->where('user_id', $request->user()->id))
                        ->where('id', $cartItemId)->firstOrFail();
        $item->delete();
        return response()->json(['success' => true, 'message' => 'Đã xóa khỏi giỏ hàng.']);
    }
}
