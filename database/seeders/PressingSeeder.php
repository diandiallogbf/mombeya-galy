<?php

namespace Database\Seeders;

use App\Models\PressingService;
use App\Models\PressingZone;
use Illuminate\Database\Seeder;

/**
 * Pressing services, prices and pick-up zones. Safe to run on a live site
 * (php artisan db:seed --class=PressingSeeder --force): it only creates
 * missing rows and never touches prices already edited in the admin.
 */
class PressingSeeder extends Seeder
{
    public function run(): void
    {
        // [category, name, price GNF, unit, icon, description]
        $services = [
            ['homme', 'Chemise', 15000, 'pièce', 'fa-solid fa-shirt', 'Lavage et repassage'],
            ['homme', 'Pantalon', 15000, 'pièce', 'fa-solid fa-person', 'Lavage et repassage'],
            ['homme', 'Costume 2 pièces', 75000, 'pièce', 'fa-solid fa-user-tie', 'Nettoyage à sec et repassage'],
            ['homme', 'Tenue traditionnelle (2 pièces)', 40000, 'pièce', 'fa-solid fa-shirt', 'Lavage et repassage'],
            ['femme', 'Robe / ensemble femme', 40000, 'pièce', 'fa-solid fa-person-dress', 'Lavage et repassage'],
            ['femme', 'Pagne / foulard', 15000, 'pièce', 'fa-solid fa-scroll', 'Lavage délicat et repassage'],
            ['femme', 'Tenue de cérémonie brodée', 75000, 'pièce', 'fa-solid fa-gem', 'Nettoyage à sec, broderies protégées'],
            ['bazin', 'Grand boubou bazin', 60000, 'pièce', 'fa-solid fa-crown', 'Lavage, amidonnage et repassage'],
            ['bazin', 'Bazin femme (complet)', 60000, 'pièce', 'fa-solid fa-crown', 'Lavage, amidonnage et repassage'],
            ['bazin', 'Amidonnage seul', 25000, 'pièce', 'fa-solid fa-spray-can-sparkles', 'Pour bazin déjà lavé'],
            ['bazin', 'Teinture de bazin', 150000, 'pièce', 'fa-solid fa-palette', 'Couleur au choix'],
            ['maison', 'Drap / housse de couette', 25000, 'pièce', 'fa-solid fa-bed', 'Lavage et repassage'],
            ['maison', 'Rideau', 30000, 'pièce', 'fa-solid fa-person-booth', 'Lavage et repassage'],
            ['maison', 'Couverture / tapis léger', 50000, 'pièce', 'fa-solid fa-rug', 'Lavage en profondeur'],
            ['formule', 'Forfait 10 chemises', 120000, 'mois', 'fa-solid fa-shirt', '10 chemises lavées et repassées par mois, collecte incluse à Ratoma.'],
            ['formule', 'Forfait famille', 350000, 'mois', 'fa-solid fa-people-roof', 'Jusqu\'à 30 pièces par mois (hors bazin), collecte hebdomadaire.'],
            ['formule', 'Forfait cérémonie', 250000, 'événement', 'fa-solid fa-champagne-glasses', '5 tenues bazin ou brodées prêtes en express pour votre événement.'],
        ];

        foreach ($services as $i => [$category, $name, $price, $unit, $icon, $description]) {
            PressingService::firstOrCreate(['name' => $name], [
                'category' => $category,
                'price' => $price,
                'unit' => $unit,
                'icon' => $icon,
                'description' => $description,
                'position' => $i + 1,
                'is_active' => true,
            ]);
        }

        // Ratoma is served from day one; the other communes are ready to be switched on from the admin.
        $zones = [
            ['Ratoma', 20000, 'Kipé, Lambanyi, Nongo, Cosa, Taouyah…', true],
            ['Dixinn', 25000, null, false],
            ['Kaloum', 30000, null, false],
            ['Matam', 30000, null, false],
            ['Matoto', 35000, null, false],
        ];

        foreach ($zones as $i => [$name, $fee, $note, $active]) {
            PressingZone::firstOrCreate(['name' => $name], [
                'fee' => $fee,
                'note' => $note,
                'position' => $i + 1,
                'is_active' => $active,
            ]);
        }
    }
}
