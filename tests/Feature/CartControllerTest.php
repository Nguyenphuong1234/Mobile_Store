<?php

namespace Tests\Feature;

use App\Models\Cart;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CartControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_can_add_product_to_cart(): void
    {
        $user = User::factory()->create();
        $product = $this->createProduct();

        $response = $this->actingAs($user)
            ->post(route('cart.store', $product), [
                'quantity' => 2,
            ]);

        $response->assertRedirect(route('cart.index'));

        $cart = Cart::where('user_id', $user->id)->firstOrFail();

        $this->assertDatabaseHas('cart_items', [
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'quantity' => 2,
        ]);
    }

    public function test_quantity_must_be_at_least_one(): void
    {
        $user = User::factory()->create();
        $product = $this->createProduct();

        $response = $this->actingAs($user)
            ->post(route('cart.store', $product), [
                'quantity' => 0,
            ]);

        $response->assertSessionHasErrors('quantity');
    }

    public function test_customer_cannot_add_inactive_product(): void
    {
        $user = User::factory()->create();
        $product = $this->createProduct([
            'is_active' => false,
        ]);

        $response = $this->actingAs($user)
            ->post(route('cart.store', $product), [
                'quantity' => 1,
            ]);

        $response->assertNotFound();
    }

    public function test_customer_cannot_add_quantity_exceeding_stock(): void
    {
        $user = User::factory()->create();
        $product = $this->createProduct([
            'stock_quantity' => 5,
        ]);

        $response = $this->actingAs($user)
            ->from(route('home'))
            ->post(route('cart.store', $product), [
                'quantity' => 6,
            ]);

        $response->assertRedirect(route('home'));
        $response->assertSessionHas('error');

        $this->assertDatabaseCount('cart_items', 0);
    }

    public function test_customer_can_add_more_of_existing_product(): void
    {
        $user = User::factory()->create();
        $product = $this->createProduct();

        $cart = Cart::query()->create([
            'user_id' => $user->id,
        ]);

        $cart->items()->create([
            'product_id' => $product->id,
            'quantity' => 2,
        ]);

        $response = $this->actingAs($user)
            ->post(route('cart.store', $product), [
                'quantity' => 3,
            ]);

        $response->assertRedirect(route('cart.index'));

        $this->assertDatabaseHas('cart_items', [
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'quantity' => 5,
        ]);
    }

    public function test_customer_cannot_increase_existing_cart_quantity_beyond_stock(): void
    {
        $user = User::factory()->create();
        $product = $this->createProduct([
            'stock_quantity' => 5,
        ]);

        $cart = Cart::query()->create([
            'user_id' => $user->id,
        ]);

        $cart->items()->create([
            'product_id' => $product->id,
            'quantity' => 4,
        ]);

        $response = $this->actingAs($user)
            ->from(route('home'))
            ->post(route('cart.store', $product), [
                'quantity' => 2,
            ]);

        $response->assertRedirect(route('home'));
        $response->assertSessionHas('error');

        $this->assertDatabaseHas('cart_items', [
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'quantity' => 4,
        ]);
    }

    private function createProduct(array $overrides = []): Product
    {
        $category = Category::query()->create([
            'name' => 'Äiá»‡n thoáº¡i',
            'slug' => 'dien-thoai',
            'is_active' => true,
        ]);

        return Product::query()->create(array_merge([
            'category_id' => $category->id,
            'name' => 'Äiá»‡n thoáº¡i kiá»ƒm thá»­',
            'slug' => 'dien-thoai-kiem-thu',
            'sku' => 'TEST-001',
            'price' => 5000000,
            'stock_quantity' => 10,
            'is_active' => true,
        ], $overrides));
    }
}
