<?php

namespace Tests\Feature;

use App\Enums\PressingStatus;
use App\Models\PressingOrder;
use App\Models\PressingService;
use App\Models\PressingZone;
use App\Models\Showroom;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PressingTest extends TestCase
{
    use RefreshDatabase;

    private PressingService $shirt;

    private PressingService $boubou;

    private PressingZone $ratoma;

    private PressingZone $matoto;

    private Showroom $showroom;

    protected function setUp(): void
    {
        parent::setUp();

        $this->shirt = PressingService::create(['name' => 'Chemise', 'category' => 'homme', 'price' => 15000]);
        $this->boubou = PressingService::create(['name' => 'Grand boubou bazin', 'category' => 'bazin', 'price' => 60000]);
        $this->ratoma = PressingZone::create(['name' => 'Ratoma', 'fee' => 20000, 'is_active' => true]);
        $this->matoto = PressingZone::create(['name' => 'Matoto', 'fee' => 35000, 'is_active' => false]);
        $this->showroom = Showroom::create(['name' => 'Showroom Kipé', 'accepts_pressing' => true]);
    }

    private function booking(array $overrides = []): array
    {
        return array_merge([
            'customer_name' => 'Mariama Camara',
            'customer_phone' => '+224 660 30 30 30',
            'mode' => 'collecte',
            'pressing_zone_id' => $this->ratoma->id,
            'address' => 'Kipé, près de la pharmacie',
            'pickup_date' => today()->addDay()->toDateString(),
            'pickup_slot' => '08:00 – 12:00',
            'items' => [$this->shirt->id => 3, $this->boubou->id => 1],
            'payment_method' => 'livraison',
        ], $overrides);
    }

    public function test_pressing_pages_render(): void
    {
        $this->get('/pressing')->assertOk()->assertSee('Vos tenues entre de bonnes mains')->assertSee('Ratoma')->assertDontSee('Matoto');
        $this->get('/pressing/reserver?service='.$this->boubou->id)->assertOk()->assertSee('Grand boubou bazin');
        $this->get('/')->assertSee('Mombeya Galy Pressing');
        $this->get('/fondation')->assertNotFound();
    }

    public function test_home_pickup_booking_computes_totals(): void
    {
        $this->post('/pressing/reserver', $this->booking(['express' => '1']))->assertRedirect();

        $order = PressingOrder::with('items')->firstOrFail();
        // 3 × 15 000 + 60 000 = 105 000 ; express +50 % = 52 500 ; Ratoma = 20 000
        $this->assertSame(105000, $order->subtotal);
        $this->assertSame(52500, $order->express_fee);
        $this->assertSame(20000, $order->collection_fee);
        $this->assertSame(177500, $order->total);
        $this->assertSame(PressingStatus::Requested, $order->status);
        $this->assertCount(2, $order->items);
        $this->assertStringStartsWith('PR', $order->reference);

        $this->get(route('pressing.confirmation', $order))->assertOk()->assertSee($order->reference);
        $this->get('/suivi-commande?reference='.$order->reference)->assertOk()->assertSee('Demande reçue')->assertDontSee('près de la pharmacie');
    }

    public function test_showroom_drop_off_is_free(): void
    {
        $this->post('/pressing/reserver', $this->booking([
            'mode' => 'depot',
            'pressing_zone_id' => null,
            'address' => null,
            'pickup_date' => null,
            'pickup_slot' => null,
            'showroom_id' => $this->showroom->id,
        ]))->assertRedirect();

        $order = PressingOrder::firstOrFail();
        $this->assertSame(0, $order->collection_fee);
        $this->assertSame(105000, $order->total);
        $this->assertSame('Showroom Kipé', $order->showroom_name);
    }

    public function test_inactive_zone_and_empty_basket_are_rejected(): void
    {
        $this->post('/pressing/reserver', $this->booking(['pressing_zone_id' => $this->matoto->id]))
            ->assertSessionHasErrors('pressing_zone_id');

        $this->post('/pressing/reserver', $this->booking(['items' => [$this->shirt->id => 0]]))
            ->assertSessionHasErrors('items');

        $this->post('/pressing/reserver', $this->booking(['pickup_date' => today()->subDay()->toDateString()]))
            ->assertSessionHasErrors('pickup_date');

        $this->assertSame(0, PressingOrder::count());
    }

    public function test_confirmation_is_private(): void
    {
        $order = PressingOrder::create([
            'customer_name' => 'X', 'customer_phone' => '620000000', 'mode' => 'depot',
            'payment_method' => 'livraison', 'total' => 1,
        ]);

        $this->get(route('pressing.confirmation', $order))->assertNotFound();
    }

    public function test_admin_pressing_pages(): void
    {
        $this->post('/pressing/reserver', $this->booking());
        $order = PressingOrder::firstOrFail();
        $admin = User::factory()->create(['is_admin' => true]);

        $this->actingAs($admin)->get('/admin/pressing-orders')->assertOk()->assertSee($order->reference);
        $this->actingAs($admin)->get('/admin/pressing-orders/'.$order->reference.'/edit')->assertOk();
        $this->actingAs($admin)->get('/admin/pressing-services')->assertOk()->assertSee('Grand boubou bazin');
        $this->actingAs($admin)->get('/admin/pressing-zones')->assertOk()->assertSee('Ratoma');
    }
}
