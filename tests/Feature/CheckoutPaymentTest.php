<?php

namespace Tests\Feature;

use App\Models\Cart;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CheckoutPaymentTest extends TestCase
{
    use RefreshDatabase;

    public function test_checkout_page_shows_payment_options_and_vietqr(): void
    {
        $user = User::factory()->create();
        $this->createCartWithProduct($user);

        $response = $this->actingAs($user)->get(route('checkout.index'));

        $response
            ->assertOk()
            ->assertSee('Thanh toán khi nhận hàng')
            ->assertSee('Thanh toán chuyển khoản')
            ->assertSee('https://img.vietqr.io/image/BIDV-3711366505-qr_only.png');
    }

    public function test_customer_can_place_order_with_cod_payment(): void
    {
        $user = User::factory()->create();
        $product = $this->createCartWithProduct($user);

        $response = $this->actingAs($user)->post(route('checkout.store'), $this->checkoutPayload([
            'payment_method' => Order::PAYMENT_COD,
        ]));

        $response->assertRedirect();

        $order = Order::query()->firstOrFail();

        $this->assertSame(Order::PAYMENT_COD, $order->payment_method);
        $this->assertSame(8, $product->refresh()->stock_quantity);
        $this->assertDatabaseCount('cart_items', 0);
    }

    public function test_customer_can_place_order_with_bank_transfer_payment(): void
    {
        $user = User::factory()->create();
        $product = $this->createCartWithProduct($user);

        $response = $this->actingAs($user)->post(route('checkout.store'), $this->checkoutPayload([
            'payment_method' => Order::PAYMENT_BANK_TRANSFER,
        ]));

        $response->assertRedirect();

        $order = Order::query()->firstOrFail();

        $this->assertSame(Order::PAYMENT_BANK_TRANSFER, $order->payment_method);
        $this->assertSame(8, $product->refresh()->stock_quantity);
        $this->assertDatabaseCount('cart_items', 0);
    }

    public function test_checkout_rejects_invalid_payment_method(): void
    {
        $user = User::factory()->create();
        $product = $this->createCartWithProduct($user);

        $response = $this->actingAs($user)->post(route('checkout.store'), $this->checkoutPayload([
            'payment_method' => 'INVALID_METHOD',
        ]));

        $response->assertSessionHasErrors('payment_method');

        $this->assertDatabaseCount('orders', 0);
        $this->assertSame(10, $product->refresh()->stock_quantity);
    }

    public function test_checkout_page_redirects_when_cart_is_empty(): void
    {
        $user = User::factory()->create();

        Cart::query()->create([
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)->get(route('checkout.index'));

        $response->assertRedirect(route('cart.index'));
        $response->assertSessionHas('error');
    }

    public function test_customer_cannot_place_order_with_empty_cart(): void
    {
        $user = User::factory()->create();

        Cart::query()->create([
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)->post(
            route('checkout.store'),
            $this->checkoutPayload()
        );

        $response->assertStatus(422);

        $this->assertDatabaseCount('orders', 0);
    }

    private function createCartWithProduct(User $user): Product
    {
        $category = Category::query()->create([
            'name' => 'Laptop',
            'slug' => 'laptop',
            'is_active' => true,
        ]);

        $product = Product::query()->create([
            'category_id' => $category->id,
            'name' => 'Laptop Test',
            'slug' => 'laptop-test',
            'sku' => 'LT-001',
            'price' => 10000000,
            'stock_quantity' => 10,
            'is_active' => true,
        ]);

        $cart = Cart::query()->create([
            'user_id' => $user->id,
        ]);

        $cart->items()->create([
            'product_id' => $product->id,
            'quantity' => 2,
        ]);

        return $product;
    }

    private function checkoutPayload(array $overrides = []): array
    {
        return array_merge([
            'recipient_name' => 'Nguyen Van A',
            'recipient_phone' => '0909000000',
            'address_line' => '12 Nguyen Trai',
            'ward' => 'Ben Thanh',
            'district' => 'Quan 1',
            'city' => 'TP HCM',
            'payment_method' => Order::PAYMENT_COD,
        ], $overrides);
    }
}
