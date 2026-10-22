<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Billing\AddOrderItemRequest;
use App\Http\Requests\Billing\ApplyDiscountRequest;
use App\Http\Requests\Billing\CheckoutRequest;
use App\Http\Requests\Billing\StartOrderRequest;
use App\Http\Requests\Billing\UpdateOrderItemRequest;
use App\Http\Resources\InvoiceResource;
use App\Http\Resources\OrderResource;
use App\Models\MenuItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Services\InvoiceService;
use App\Services\OrderService;
use App\Services\PaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function __construct(
        private readonly OrderService $orders,
        private readonly PaymentService $payments,
        private readonly InvoiceService $invoices,
    ) {
    }

    public function store(StartOrderRequest $request): JsonResponse
    {
        $this->authorize('create', Order::class);

        $order = $this->orders->start($request->user()->branch, $request->user(), $request->validated());

        return (new OrderResource($order->load('items')))
            ->response()
            ->setStatusCode(201);
    }

    public function show(Order $order): OrderResource
    {
        $this->authorize('view', $order);

        return new OrderResource($order->load(['items', 'invoice']));
    }

    public function addItem(AddOrderItemRequest $request, Order $order): OrderResource
    {
        $this->authorize('update', $order);

        $menuItem = MenuItem::findOrFail($request->input('menu_item_id'));

        $this->orders->addItem($order, $menuItem, $request->integer('quantity'), $request->input('notes'));

        return new OrderResource($order->refresh()->load('items'));
    }

    public function updateItem(UpdateOrderItemRequest $request, Order $order, OrderItem $item): OrderResource
    {
        $this->authorize('update', $order);

        $this->orders->updateItem($item, $request->integer('quantity'), $request->input('notes'));

        return new OrderResource($order->refresh()->load('items'));
    }

    public function removeItem(Request $request, Order $order, OrderItem $item): OrderResource
    {
        $this->authorize('update', $order);

        $this->orders->removeItem($item);

        return new OrderResource($order->refresh()->load('items'));
    }

    public function discount(ApplyDiscountRequest $request, Order $order): OrderResource
    {
        $this->authorize('update', $order);

        $this->orders->applyDiscount($order, (string) $request->input('discount_total'));

        return new OrderResource($order->refresh()->load('items'));
    }

    public function checkout(CheckoutRequest $request, Order $order): InvoiceResource
    {
        $this->authorize('checkout', $order);

        $this->payments->record(
            $order,
            \App\Enums\PaymentMethod::from($request->input('payment_method')),
            (string) $request->input('amount'),
            $request->input('transaction_reference')
        );

        $invoice = $this->invoices->generate($order->refresh());

        return new InvoiceResource($invoice);
    }

    public function cancel(Order $order): OrderResource
    {
        $this->authorize('update', $order);

        $this->orders->cancel($order);

        return new OrderResource($order->refresh());
    }
}
