<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOrderRequest;
use App\Http\Requests\UpdateOrderRequest;
use App\Models\Order;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::with(['customer', 'products'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return response()->json($orders);
    }

    public function store(StoreOrderRequest $request)
    {
        try {
            DB::beginTransaction();

            $order = Order::create([
                'customer_id' => $request->customer_id,
                'status' => 'pending',
                'total_amount' => 0,
            ]);

            $totalAmount = 0;

            foreach ($request->products as $productData) {
                $product = \App\Models\Product::findOrFail($productData['product_id']);

                $price = $product->price;
                $quantity = $productData['quantity'];

                $order->products()->attach($product->id, [
                    'quantity' => $quantity,
                    'price' => $price,
                ]);

                $totalAmount += $quantity * $price;
            }

            $order->update(['total_amount' => $totalAmount]);

            DB::commit();

            Mail::to($order->customer->email)->send(new \App\Mail\OrderCreated($order));

            return response()->json($order->load(['products', 'customer']), 201);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Erro ao criar novo pedido',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function show($id)
    {
        $order = Order::with(['customer', 'products'])->findOrFail($id);

        return response()->json($order);
    }

    public function update(UpdateOrderRequest $request, $id)
    {
        try {
            DB::beginTransaction();

            $order = Order::findOrFail($id);

            if ($request->has('status')) {
                $order->update(['status' => $request->status]);
            }

            if ($request->has('products')) {
                $order->products()->detach();

                $totalAmount = 0;

                foreach ($request->products as $productData) {
                    $product = \App\Models\Product::findOrFail($productData['id']);
                    $quantity = $productData['quantity'];
                    $price = $product->price;

                    $order->products()->attach($product->id, [
                        'quantity' => $quantity,
                        'price' => $price,
                    ]);

                    $totalAmount += ($price * $quantity);
                }

                $order->update(['total_amount' => $totalAmount]);
            }

            DB::commit();

            return response()->json(
                $order->load(['customer', 'products'])
            );

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Erro ao atualizar o pedido',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function destroy($id)
    {
        $order = Order::findOrFail($id);
        // Soft delete
        $order->delete();

        // deletar os itens do pedido associados
        foreach ($order->products as $product) {
            $order->products()->detach($product->id);
        }

        return response()->json([
            'message' => 'Order deleted successfully',
        ], 204);
    }

    public function getByCustomer($customerId)
    {
        $orders = Order::where('customer_id', $customerId)
            ->with('products')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return response()->json($orders);
    }
}
