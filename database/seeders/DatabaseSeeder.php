<?php

namespace Database\Seeders;

use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Enums\PressingStatus;
use App\Models\Category;
use App\Models\DeliveryZone;
use App\Models\Order;
use App\Models\PressingOrder;
use App\Models\PressingService;
use App\Models\PressingZone;
use App\Models\Product;
use App\Models\Showroom;
use App\Models\Slide;
use App\Models\User;
use Database\Seeders\Support\PlaceholderArt;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class DatabaseSeeder extends Seeder
{
    private const DESCRIPTIONS = [
        'boubou' => '<p>Grand boubou prêt-à-porter</p><ul><li>Manches amples avec finitions brodées</li><li>Matière : bazin riche importé</li><li>Broderie artisanale réalisée dans nos ateliers de Conakry</li><li>Livré avec pantalon assorti</li></ul><p>Disponible dans tous les showrooms Mombeya Galy.</p>',
        'tunique' => '<p>Tenue traditionnelle deux pièces</p><ul><li>Tunique cintrée, col rond brodé</li><li>Manches simples avec poignets manchette</li><li>Matière : super italien</li><li>Pantalon droit assorti</li></ul><p>Disponible dans tous les showrooms Mombeya Galy.</p>',
        'costume' => '<p>Costume africain coupe moderne</p><ul><li>Veste col officier, boutons recouverts</li><li>Tissu léger et respirant</li><li>Doublure intérieure</li><li>Pantalon slim assorti</li></ul>',
        'chemise' => '<p>Chemise traditionnelle Mombeya Galy</p><ul><li>Coupe ajustée</li><li>100% coton</li><li>Broderie ton sur ton au col</li></ul>',
        'robe' => '<p>Tenue femme Mombeya Galy</p><ul><li>Coupe fluide et élégante</li><li>Finitions brodées à la main</li><li>Tissu wax / bazin premium</li></ul>',
        'enfant' => '<p>Ensemble enfant</p><ul><li>Confortable et facile à porter</li><li>Coton doux</li><li>Idéal pour les fêtes et cérémonies</li></ul>',
        'pantalon' => '<p>Pantalon MG Autrement</p><ul><li>Coupe droite moderne</li><li>Ceinture élastiquée avec cordon</li><li>Tissu léger</li></ul>',
        'chaussure' => '<p>Chaussures en cuir véritable</p><ul><li>Semelle cuir cousue</li><li>Doublure cuir</li><li>Fabrication italienne</li></ul>',
        'sandale' => '<p>Sandales en cuir</p><ul><li>Cuir pleine fleur</li><li>Semelle antidérapante</li><li>Confort toute la journée</li></ul>',
        'accessoire' => '<p>Accessoire Mombeya Galy</p><ul><li>Finitions soignées</li><li>Livré dans un écrin cadeau</li></ul>',
    ];

    public function run(): void
    {
        mt_srand(2026);

        $disk = Storage::disk('public');
        foreach (['products', 'categories', 'slides', 'showrooms'] as $dir) {
            $disk->deleteDirectory($dir);
        }

        $this->users();
        $categories = $this->categories();
        $this->products($categories);
        $this->deliveryZones();
        $this->showrooms();
        $this->slides();
        $this->orders();
        $this->call(PressingSeeder::class);
        $this->pressingOrders();
    }

    private function users(): void
    {
        User::updateOrCreate(['email' => 'admin@mombeyagaly.com'], [
            'name' => 'Administrateur Mombeya Galy',
            'phone' => '+224 622 00 00 00',
            'password' => 'MombeyaGaly@2026',
            'is_admin' => true,
            'email_verified_at' => now(),
        ]);

        User::updateOrCreate(['email' => 'client@exemple.com'], [
            'name' => 'Mamadou Diallo',
            'phone' => '+224 620 11 22 33',
            'password' => 'client1234',
            'is_admin' => false,
            'email_verified_at' => now(),
        ]);
    }

    /**
     * @return array<string, Category>
     */
    private function categories(): array
    {
        $rows = [
            ['homme', 'Mode Homme', 'boubou', 'fa-solid fa-user-tie'],
            ['femme', 'Mombeya Galy Pour Elles', 'robe', 'fa-solid fa-person-dress'],
            ['enfant', 'Mombeya Galy Kids', 'enfant', 'fa-solid fa-child'],
            ['prix-unique', 'Madina (Tout à 350.000 GNF)', 'tunique', 'fa-solid fa-tags'],
            ['chemises', 'Chemises Mombeya Galy', 'chemise', 'fa-solid fa-shirt'],
            ['autrement', 'MG Autrement', 'pantalon', 'fa-solid fa-wand-magic-sparkles'],
            ['labe', 'Mombeya Galy Labé', 'costume', 'fa-solid fa-flag'],
            ['accessoires', 'Accessoires', 'sac', 'fa-solid fa-gem'],
            ['bazin-getzner', 'Bazin Getzner', 'tissu', 'fa-solid fa-scroll'],
            ['promotion', 'Promotion', 'boubou', 'fa-solid fa-percent'],
        ];

        $categories = [];
        foreach ($rows as $i => [$slug, $name, $shape, $icon]) {
            $categories[$slug] = Category::updateOrCreate(['slug' => $slug], [
                'name' => $name,
                'icon' => $icon,
                'image' => PlaceholderArt::category($shape, $i, "categories/{$slug}.svg"),
                'description' => null,
                'position' => $i + 1,
                'is_active' => true,
                'show_on_home' => true,
                'show_in_menu' => true,
            ]);
        }

        return $categories;
    }

    /**
     * @param  array<string, Category>  $c
     */
    private function products(array $c): void
    {
        $clothes = ['M', 'L', 'XL', 'XXL'];
        $shoes = ['Pointure 40', 'Pointure 41', 'Pointure 42', 'Pointure 43', 'Pointure 44', 'Pointure 45'];
        $kids = ['2 ans', '4 ans', '6 ans', '8 ans', '10 ans', '12 ans'];
        $colors = ['Blanc', 'Bleu ciel', 'Bleu nuit', 'Bordeaux', 'Noir', 'Beige', 'Vert', 'Gris'];

        // [category, name, shape, description key, price, discount, sizes, count, flags]
        $catalog = [
            ['homme', 'Grand boubou Fouta', 'boubou', 'boubou', 2100000, null, $clothes, 6, ['prestige' => 2]],
            ['homme', 'Tenue traditionnelle', 'tunique', 'tunique', 750000, null, $clothes, 10, []],
            ['homme', 'Costume africain', 'costume', 'costume', 1650000, null, $clothes, 6, ['prestige' => 3]],
            ['homme', 'Grand boubou 3/4', 'boubou', 'boubou', 2100000, null, $clothes, 3, ['prestige' => 3]],
            ['homme', 'Ensemble Kaloum', 'tunique', 'tunique', 900000, 750000, $clothes, 3, []],
            ['femme', 'Robe brodée', 'robe', 'robe', 900000, null, ['S', 'M', 'L', 'XL'], 5, []],
            ['femme', 'Ensemble bazin femme', 'robe', 'robe', 1200000, null, ['S', 'M', 'L', 'XL'], 3, ['prestige' => 1]],
            ['enfant', 'Ensemble kids', 'enfant', 'enfant', 375000, null, $kids, 5, []],
            ['enfant', 'Grand boubou kids', 'enfant', 'enfant', 900000, 750000, $kids, 3, []],
            ['prix-unique', 'Tenue traditionnelle', 'tunique', 'tunique', 350000, null, $clothes, 8, []],
            ['chemises', 'Chemise Mombeya Galy', 'chemise', 'chemise', 375000, null, $clothes, 8, []],
            ['chemises', 'Chemise traditionnelle brodée', 'chemise', 'chemise', 525000, null, $clothes, 3, []],
            ['autrement', 'MG Autrement', 'costume', 'costume', 1200000, null, $clothes, 4, []],
            ['autrement', 'Pantalon Autrement', 'pantalon', 'pantalon', 525000, null, $clothes, 3, []],
            ['labe', 'Costume africain Labé', 'costume', 'costume', 1650000, null, $clothes, 4, []],
            ['labe', 'Tunique Labé', 'tunique', 'tunique', 825000, null, $clothes, 3, []],
            ['accessoires', 'Sandales Mombeya Galy', 'sandale', 'sandale', 375000, null, $shoes, 4, []],
            ['accessoires', 'Chaussures italiennes', 'chaussure', 'chaussure', 1200000, null, $shoes, 4, ['prestige' => 2]],
            ['accessoires', 'Chaussures fermées', 'chaussure', 'chaussure', 900000, 750000, $shoes, 2, []],
            ['accessoires', 'Mocassins cuir prestige', 'chaussure', 'chaussure', 1500000, null, $shoes, 2, ['prestige' => 2]],
            ['accessoires', 'Bracelet', 'bracelet', 'accessoire', 150000, null, [], 4, []],
            ['accessoires', 'Boutons de manchette', 'manchette', 'accessoire', 150000, null, [], 3, []],
            ['accessoires', 'Lunettes de soleil', 'lunettes', 'accessoire', 375000, null, [], 3, []],
            ['accessoires', 'Sacoche cuir', 'sac', 'accessoire', 525000, null, [], 3, []],
            ['accessoires', 'Montre classique', 'montre', 'accessoire', 750000, null, [], 2, []],
            ['bazin-getzner', 'Bazin Getzner', 'boubou', 'boubou', 1350000, null, $clothes, 5, []],
            ['bazin-getzner', 'Grand boubou Getzner', 'boubou', 'boubou', 2700000, null, $clothes, 3, ['prestige' => 3]],
            ['promotion', 'Tenue traditionnelle', 'tunique', 'tunique', 600000, 450000, $clothes, 8, []],
            ['promotion', 'Grand boubou', 'boubou', 'boubou', 900000, 750000, $clothes, 4, []],
        ];

        $variant = 0;
        $created = 0;
        foreach ($catalog as [$catSlug, $name, $shape, $descKey, $price, $discount, $sizes, $count, $flags]) {
            for ($n = 1; $n <= $count; $n++) {
                $variant++;
                $productName = $name;
                $slug = Product::uniqueSlug($productName);
                $images = [];
                $imageCount = $shape === 'bracelet' || $shape === 'manchette' ? 1 : mt_rand(1, 3);
                for ($k = 0; $k < $imageCount; $k++) {
                    $images[] = PlaceholderArt::product($shape, $variant + $k * 3, "products/{$slug}-{$k}.svg");
                }

                $isClothing = in_array($shape, ['boubou', 'tunique', 'costume', 'chemise', 'robe', 'enfant', 'pantalon'], true);
                $productColors = $isClothing && mt_rand(0, 2) === 0
                    ? array_slice($this->shuffled($colors), 0, mt_rand(2, 4))
                    : [];

                Product::create([
                    'category_id' => $c[$catSlug]->id,
                    'name' => $productName,
                    'slug' => $slug,
                    'reference' => 'MG-'.str_pad((string) (1000 + $variant), 5, '0', STR_PAD_LEFT),
                    'price' => $price,
                    'discount_price' => $discount,
                    'description' => self::DESCRIPTIONS[$descKey],
                    'images' => $images,
                    'sizes' => $sizes,
                    'colors' => $productColors,
                    'stock' => mt_rand(0, 12) === 0 ? 0 : mt_rand(3, 40),
                    'is_active' => true,
                    'is_deliverable' => true,
                    'is_new' => mt_rand(0, 4) === 0,
                    'is_prestige' => isset($flags['prestige']) && $n <= $flags['prestige'],
                    'is_trend_week' => mt_rand(0, 4) === 0,
                    'is_trend_month' => mt_rand(0, 2) === 0,
                    'views' => mt_rand(10, 900),
                    'sales_count' => mt_rand(0, 120),
                    'created_at' => now()->subDays(mt_rand(0, 120))->subMinutes(mt_rand(0, 1440)),
                ]);
                $created++;
            }
        }

        $this->command?->info("{$created} produits créés.");
    }

    private function shuffled(array $items): array
    {
        for ($i = count($items) - 1; $i > 0; $i--) {
            $j = mt_rand(0, $i);
            [$items[$i], $items[$j]] = [$items[$j], $items[$i]];
        }

        return $items;
    }

    private function deliveryZones(): void
    {
        $zones = [
            ['Retrait en showroom (gratuit)', 0, 'Dès confirmation'],
            ['Conakry – Kaloum', 25000, '24h'],
            ['Conakry – Dixinn', 25000, '24h'],
            ['Conakry – Matam', 30000, '24h'],
            ['Conakry – Ratoma', 35000, '24h'],
            ['Conakry – Matoto', 35000, '24h'],
            ['Banlieue (Coyah, Dubréka, Manéah)', 60000, '24 à 48h'],
            ['Intérieur du pays (Labé, Kindia, Kankan, N\'Zérékoré…)', 100000, '2 à 4 jours'],
            ['International – DHL (Afrique, Europe, Amérique)', 650000, '3 à 5 jours ouvrés'],
        ];
        foreach ($zones as $i => [$name, $fee, $delay]) {
            DeliveryZone::updateOrCreate(['name' => $name], ['fee' => $fee, 'delay' => $delay, 'position' => $i + 1, 'is_active' => true]);
        }
    }

    private function showrooms(): void
    {
        $week = fn (string $open, string $close, string $sunday) => [
            'lundi' => "$open – $close", 'mardi' => "$open – $close", 'mercredi' => "$open – $close",
            'jeudi' => "$open – $close", 'vendredi' => "$open – $close", 'samedi' => "$open – $close",
            'dimanche' => $sunday,
        ];

        $rooms = [
            ['Mombeya Galy Kaloum (Siège)', 'Avenue de la République, Kaloum', '+224 622 00 00 01', 9.5092, -13.7122, $week('09:00', '21:00', 'Fermé')],
            ['Mombeya Galy Prestige Kipé', 'Centre commercial de Kipé, Ratoma', '+224 622 00 00 02', 9.6178, -13.6365, $week('10:00', '22:00', '15:00 – 21:00')],
            ['Mombeya Galy Dixinn', 'Route de Donka, Dixinn', '+224 622 00 00 03', 9.5468, -13.6788, $week('09:00', '21:00', 'Fermé')],
            ['Mombeya Galy Pour Elles', 'Cosa, Ratoma', '+224 622 00 00 04', 9.6040, -13.6460, $week('10:00', '21:00', 'Fermé')],
            ['Mombeya Galy Kids', 'Lambanyi, Ratoma', '+224 622 00 00 05', 9.6420, -13.6000, $week('09:00', '20:00', 'Fermé')],
            ['Mombeya Galy Matoto', 'Carrefour Matoto, Matoto', '+224 622 00 00 06', 9.5850, -13.6200, $week('09:00', '21:00', 'Fermé')],
            ['Mombeya Galy Nongo', 'Nongo Taady, Ratoma', '+224 622 00 00 07', 9.6550, -13.6100, $week('09:00', '21:00', '15:00 – 20:00')],
            ['Mombeya Galy Labé', 'Centre-ville, Labé', '+224 622 00 00 08', 11.3180, -12.2830, $week('09:00', '20:00', 'Fermé')],
        ];

        foreach ($rooms as $i => [$name, $address, $phone, $lat, $lng, $hours]) {
            Showroom::updateOrCreate(['name' => $name], [
                'image' => PlaceholderArt::cover('store', $i, "showrooms/showroom-{$i}.svg"),
                'address' => $address,
                'phone' => $phone,
                'latitude' => $lat,
                'longitude' => $lng,
                'opening_hours' => $hours,
                'accepts_pressing' => ! str_contains($name, 'Labé'),
                'position' => $i + 1,
                'is_active' => true,
            ]);
        }
    }

    private function slides(): void
    {
        $slides = [
            ['L\'élégance africaine, de Conakry au monde', 'Grands boubous, bazin Getzner et tenues traditionnelles confectionnés avec passion.', 'Découvrir la boutique', '/boutique', ['boubou', 'tunique']],
            ['Le style n\'a pas d\'âge', 'Découvrez la nouvelle collection Mombeya Galy Kids.', 'Voir la collection', '/categorie/enfant', ['enfant', 'tunique']],
            ['Collection Prestige', 'Des pièces d\'exception pour vos grandes occasions.', 'Voir le Prestige', '/prestige', ['costume', 'boubou']],
            ['Livraison partout dans le monde', 'Payez par Orange Money, MTN MoMo, carte bancaire ou à la livraison.', 'Commander maintenant', '/boutique', ['chaussure', 'sac']],
        ];
        foreach ($slides as $i => [$title, $subtitle, $button, $link, $shapes]) {
            Slide::updateOrCreate(['title' => $title], [
                'subtitle' => $subtitle,
                'button_text' => $button,
                'button_link' => $link,
                'image' => PlaceholderArt::hero($i, $shapes, "slides/slide-{$i}.svg"),
                'position' => $i + 1,
                'is_active' => true,
            ]);
        }
    }

    /**
     * A few demo pressing bookings so the admin screens are not empty.
     */
    private function pressingOrders(): void
    {
        $services = PressingService::where('category', '!=', 'formule')->get();
        $zone = PressingZone::active()->first();
        $dropPoint = Showroom::where('accepts_pressing', true)->first();
        $statuses = [PressingStatus::Requested, PressingStatus::Requested, PressingStatus::Collected, PressingStatus::Cleaning, PressingStatus::Ready, PressingStatus::Delivered];
        $customers = [['Mariama Camara', '+224 660 30 30 30'], ['Ibrahima Sow', '+224 664 22 33 44'], ['Alpha Oumar Baldé', '+224 628 12 12 12']];

        foreach ($statuses as $i => $status) {
            [$name, $phone] = $customers[$i % count($customers)];
            $collecte = $zone && $i % 3 !== 2;
            $express = $i === 1;
            $picked = $services->random(mt_rand(1, 3));
            $subtotal = 0;
            $lines = [];
            foreach ($picked as $service) {
                $qty = mt_rand(1, 4);
                $subtotal += $service->price * $qty;
                $lines[] = ['pressing_service_id' => $service->id, 'service_name' => $service->name, 'quantity' => $qty, 'unit_price' => $service->price, 'total' => $service->price * $qty];
            }
            $expressFee = $express ? (int) round($subtotal / 2) : 0;
            $fee = $collecte ? $zone->fee : 0;

            $order = PressingOrder::create([
                'customer_name' => $name,
                'customer_phone' => $phone,
                'mode' => $collecte ? 'collecte' : 'depot',
                'pressing_zone_id' => $collecte ? $zone->id : null,
                'zone_name' => $collecte ? $zone->name : null,
                'address' => $collecte ? ['Kipé', 'Lambanyi', 'Nongo Taady', 'Cosa'][$i % 4] : null,
                'showroom_id' => $collecte ? null : $dropPoint?->id,
                'showroom_name' => $collecte ? null : $dropPoint?->name,
                'pickup_date' => $collecte ? today()->addDays($i % 3)->toDateString() : null,
                'pickup_slot' => $collecte ? '08:00 – 12:00' : null,
                'express' => $express,
                'subtotal' => $subtotal,
                'express_fee' => $expressFee,
                'collection_fee' => $fee,
                'total' => $subtotal + $expressFee + $fee,
                'payment_method' => PaymentMethod::CashOnDelivery,
                'payment_status' => $status === PressingStatus::Delivered ? PaymentStatus::Paid : PaymentStatus::Pending,
                'status' => $status,
            ]);
            $order->items()->createMany($lines);
        }
    }

    private function orders(): void
    {
        $customers = [
            ['Mamadou Diallo', '+224 620 11 22 33', 'client@exemple.com'],
            ['Fatoumata Binta Barry', '+224 621 45 67 89', null],
            ['Ibrahima Sow', '+224 664 22 33 44', 'ibrahima.sow@exemple.com'],
            ['Aïssatou Bah', '+224 655 98 76 54', null],
            ['Alpha Oumar Baldé', '+224 628 12 12 12', null],
            ['Mariama Camara', '+224 660 30 30 30', 'mariama@exemple.com'],
        ];
        $statuses = [OrderStatus::Pending, OrderStatus::Pending, OrderStatus::Confirmed, OrderStatus::Preparing, OrderStatus::Shipped, OrderStatus::Delivered, OrderStatus::Delivered, OrderStatus::Cancelled];
        $methods = PaymentMethod::cases();
        $zones = DeliveryZone::where('fee', '>', 0)->get();
        $products = Product::where('stock', '>', 0)->get();
        $clientUser = User::where('email', 'client@exemple.com')->first();

        for ($i = 0; $i < 14; $i++) {
            [$name, $phone, $email] = $customers[$i % count($customers)];
            $zone = $zones[$i % $zones->count()];
            $status = $statuses[$i % count($statuses)];
            $picked = $products->random(mt_rand(1, 3));
            $date = now()->subDays(mt_rand(0, 25))->subHours(mt_rand(0, 23));

            $order = new Order([
                'user_id' => $email === 'client@exemple.com' ? $clientUser?->id : null,
                'customer_name' => $name,
                'customer_phone' => $phone,
                'customer_email' => $email,
                'delivery_zone_id' => $zone->id,
                'delivery_zone_name' => $zone->name,
                'address' => 'Quartier '.['Almamya', 'Kipé', 'Hamdallaye', 'Cosa', 'Taouyah', 'Lambanyi'][$i % 6],
                'payment_method' => $methods[$i % count($methods)],
                'status' => $status,
                'payment_status' => in_array($status, [OrderStatus::Delivered, OrderStatus::Shipped], true) ? PaymentStatus::Paid : PaymentStatus::Pending,
                'delivery_fee' => $zone->fee,
            ]);
            $order->created_at = $date;
            $order->updated_at = $date;
            $order->save();

            $subtotal = 0;
            foreach ($picked as $product) {
                $qty = mt_rand(1, 2);
                $unit = $product->finalPrice();
                $order->items()->create([
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'product_image' => $product->images[0] ?? null,
                    'size' => $product->sizeList()[0] ?? null,
                    'color' => $product->colorList()[0] ?? null,
                    'quantity' => $qty,
                    'unit_price' => $unit,
                    'total' => $unit * $qty,
                ]);
                $subtotal += $unit * $qty;
            }
            $order->update(['subtotal' => $subtotal, 'total' => $subtotal + $zone->fee]);
        }
    }
}
