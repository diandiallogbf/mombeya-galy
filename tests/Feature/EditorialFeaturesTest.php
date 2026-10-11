<?php

namespace Tests\Feature;

use App\Enums\AppointmentStatus;
use App\Models\Appointment;
use App\Models\Category;
use App\Models\Product;
use App\Models\Review;
use App\Models\Showroom;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EditorialFeaturesTest extends TestCase
{
    use RefreshDatabase;

    private Product $product;

    private Showroom $showroom;

    protected function setUp(): void
    {
        parent::setUp();

        $category = Category::create(['name' => 'Mode Homme', 'slug' => 'homme']);
        $this->product = Product::create([
            'category_id' => $category->id,
            'name' => 'Grand boubou brodé',
            'price' => 900000,
            'sizes' => ['M', 'L'],
            'stock' => 5,
        ]);
        $this->showroom = Showroom::create(['name' => 'Maison Kaloum']);
    }

    public function test_guest_wishlist_toggle_and_merge_at_login(): void
    {
        $this->postJson(route('wishlist.toggle', $this->product))->assertOk()->assertJson(['added' => true, 'count' => 1]);
        $this->get('/favoris')->assertOk()->assertSee('Grand boubou brodé');

        $user = User::factory()->create(['password' => 'secret123']);
        $this->post('/connexion', ['email' => $user->email, 'password' => 'secret123'])->assertRedirect();
        $this->assertTrue($user->favoriteProducts()->whereKey($this->product->id)->exists());

        $this->postJson(route('wishlist.toggle', $this->product))->assertJson(['added' => false, 'count' => 0]);
        $this->assertFalse($user->favoriteProducts()->whereKey($this->product->id)->exists());
    }

    public function test_reviews_are_moderated_before_publication(): void
    {
        $url = route('product.show', $this->product);

        $this->post(route('reviews.store', $this->product), ['name' => 'Aïssatou Bah', 'rating' => 5, 'comment' => 'Superbe broderie, coupe parfaite.'])
            ->assertRedirect($url.'#avis');

        $review = Review::firstOrFail();
        $this->assertFalse($review->is_approved);
        $this->get($url)->assertDontSee('Superbe broderie');

        $review->update(['is_approved' => true]);
        $this->get($url)->assertSee('Superbe broderie')->assertSee('Aïssatou B.')->assertDontSee('Aïssatou Bah');
        $this->get('/')->assertSee('Ils nous font confiance');

        $this->post(route('reviews.store', $this->product), ['name' => 'X', 'rating' => 9, 'comment' => 'court'])
            ->assertSessionHasErrorsIn('review', ['rating', 'comment']);
    }

    public function test_size_guide(): void
    {
        $this->get('/guide-des-tailles')->assertOk()->assertSee('Prendre vos mesures')->assertSee('Pointure (EU)');
        $this->get(route('product.show', $this->product))->assertSee('Guide des tailles');
    }

    public function test_bespoke_appointment_booking(): void
    {
        $this->get('/sur-mesure')->assertOk()->assertSee('Grand boubou')->assertSee('Maison Kaloum');

        $this->post('/sur-mesure', [
            'customer_name' => 'Ibrahima Sow',
            'customer_phone' => '+224 664 22 33 44',
            'outfit_type' => 'Tenue de mariage',
            'preferred_date' => today()->addDays(3)->toDateString(),
            'preferred_slot' => '09:00 – 11:00',
            'showroom_id' => $this->showroom->id,
        ])->assertRedirect();

        $appointment = Appointment::firstOrFail();
        $this->assertSame(AppointmentStatus::Requested, $appointment->status);
        $this->assertSame('Maison Kaloum', $appointment->showroom_name);
        $this->assertStringStartsWith('SM', $appointment->reference);
        $this->get(route('appointments.confirmation', $appointment))->assertOk()->assertSee($appointment->reference);

        $this->post('/sur-mesure', ['customer_name' => 'X', 'customer_phone' => '1', 'outfit_type' => 'Inconnu', 'preferred_date' => today()->subDay()->toDateString()])
            ->assertSessionHasErrors(['customer_phone', 'outfit_type', 'preferred_date', 'preferred_slot', 'showroom_id']);
    }

    public function test_appointment_confirmation_is_private(): void
    {
        $appointment = Appointment::create([
            'customer_name' => 'X', 'customer_phone' => '620000000', 'outfit_type' => 'Grand boubou',
            'preferred_date' => today()->addDay(),
        ]);

        $this->get(route('appointments.confirmation', $appointment))->assertNotFound();
    }

    public function test_admin_pages_for_new_features(): void
    {
        Review::create(['product_id' => $this->product->id, 'name' => 'Client', 'rating' => 4, 'comment' => 'Très belle pièce.']);
        $admin = User::factory()->create(['is_admin' => true]);

        $this->actingAs($admin)->get('/admin/reviews')->assertOk()->assertSee('Très belle pièce.');
        $this->actingAs($admin)->get('/admin/appointments')->assertOk();
        $this->actingAs($admin)->get('/admin/gallery-photos')->assertOk();
        $this->actingAs($admin)->get('/admin/parametres')->assertOk()->assertSee('Notre atelier');
    }
}
