<?php

namespace Tests\Feature;

use App\Enums\OrderStatus;
use App\Models\Category;
use App\Models\DeliveryZone;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShopTest extends TestCase
{
    use RefreshDatabase;

    private Category $category;

    private Product $product;

    private DeliveryZone $zone;

    protected function setUp(): void
    {
        parent::setUp();

        $this->category = Category::create(['name' => 'Mode Homme', 'slug' => 'homme', 'position' => 1]);
        $this->product = Product::create([
            'category_id' => $this->category->id,
            'name' => 'Grand boubou test',
            'price' => 900000,
            'discount_price' => 750000,
            'sizes' => ['M', 'L'],
            'stock' => 5,
            'is_trend_month' => true,
        ]);
        $this->zone = DeliveryZone::create(['name' => 'Conakry – Kaloum', 'fee' => 25000, 'delay' => '24h']);
    }

    public function test_public_pages_render(): void
    {
        foreach (['/', '/boutique', '/boutique?tri=croissant&q=boubou', '/nouveautes', '/promotions', '/prestige', '/categorie/homme',
            '/produit/'.$this->product->slug, '/produit/'.$this->product->slug.'/apercu', '/panier', '/suivi-commande',
            '/showrooms', '/videos', '/pressing', '/pressing/reserver', '/connexion', '/mot-de-passe/oublie'] as $url) {
            $this->get($url)->assertOk();
        }

        $this->get('/')->assertSee('Grand boubou test')->assertSee('750.000 GNF');
        $this->get('/page-qui-nexiste-pas')->assertNotFound()->assertSee('Page introuvable');
    }

    public function test_product_with_sizes_requires_a_size(): void
    {
        $this->postJson('/panier', ['product_id' => $this->product->id, 'quantity' => 1])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('size');

        $this->postJson('/panier', ['product_id' => $this->product->id, 'size' => 'XXL'])
            ->assertJsonValidationErrors('size');
    }

    public function test_add_to_cart_and_checkout(): void
    {
        $this->postJson('/panier', ['product_id' => $this->product->id, 'size' => 'L', 'quantity' => 2])
            ->assertOk()
            ->assertJson(['count' => 2, 'total' => '1.500.000 GNF']);

        $this->get('/panier')->assertSee('Grand boubou test')->assertSee('1.500.000 GNF');

        $response = $this->post('/commande', [
            'customer_name' => 'Mamadou Diallo',
            'customer_phone' => '+224 620 11 22 33',
            'delivery_zone_id' => $this->zone->id,
            'address' => 'Almamya',
            'payment_method' => 'orange_money',
        ]);

        $order = Order::with('items')->firstOrFail();
        $response->assertRedirect(route('checkout.confirmation', $order));

        $this->assertSame(1500000, $order->subtotal);
        $this->assertSame(1525000, $order->total);
        $this->assertSame(OrderStatus::Pending, $order->status);
        $this->assertSame('L', $order->items->first()->size);
        $this->assertSame(3, $this->product->fresh()->stock);
        $this->assertSame([], session('cart.lines', []));

        $this->get(route('checkout.confirmation', $order))->assertOk()->assertSee($order->reference);
        $this->get('/suivi-commande?reference='.strtolower($order->reference))->assertOk()->assertSee('En attente');
    }

    public function test_confirmation_page_is_private(): void
    {
        $order = Order::create([
            'customer_name' => 'X', 'customer_phone' => '620000000', 'payment_method' => 'livraison',
            'subtotal' => 1, 'total' => 1,
        ]);

        $this->get(route('checkout.confirmation', $order))->assertNotFound();
    }

    public function test_out_of_stock_product_cannot_be_added(): void
    {
        $this->product->update(['stock' => 0]);

        $this->postJson('/panier', ['product_id' => $this->product->id, 'size' => 'M'])
            ->assertJsonValidationErrors('product_id');
    }

    public function test_checkout_rejects_quantity_above_stock(): void
    {
        $this->postJson('/panier', ['product_id' => $this->product->id, 'size' => 'M', 'quantity' => 3])->assertOk();
        $this->product->update(['stock' => 1]);

        $this->post('/commande', [
            'customer_name' => 'Test', 'customer_phone' => '+224 620 00 00 00',
            'delivery_zone_id' => $this->zone->id, 'address' => 'Kipé', 'payment_method' => 'livraison',
        ])->assertSessionHasErrors('cart');

        $this->assertSame(0, Order::count());
    }

    public function test_currency_switch(): void
    {
        $this->get('/devise/USD')->assertRedirect();
        $this->get('/produit/'.$this->product->slug)->assertSee('$86,71');
    }

    public function test_customer_registration_and_account(): void
    {
        $this->post('/inscription', [
            'name' => 'Aïssatou Bah',
            'email' => 'aissatou@example.com',
            'password' => 'motdepasse123',
            'password_confirmation' => 'motdepasse123',
            'terms' => '1',
        ])->assertRedirect(route('account.index'));

        $this->assertAuthenticated();
        $this->get('/mon-compte')->assertOk()->assertSee('Mes commandes');
    }

    public function test_admin_panel_access(): void
    {
        $this->get('/admin')->assertRedirect('/admin/login');

        $customer = User::factory()->create(['is_admin' => false]);
        $this->actingAs($customer)->get('/admin')->assertForbidden();

        $admin = User::factory()->create(['is_admin' => true]);
        $this->actingAs($admin)->get('/admin')->assertOk();
        $this->actingAs($admin)->get('/admin/products')->assertOk()->assertSee('Grand boubou test');
        $this->actingAs($admin)->get('/admin/parametres')->assertOk();
    }
}
