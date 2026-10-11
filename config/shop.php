<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default shop settings
    |--------------------------------------------------------------------------
    |
    | Every value can be overridden from the admin panel (Paramètres page),
    | which stores them in the `settings` table.
    |
    */

    'defaults' => [
        'shop_name' => 'Mombeya Galy',
        'slogan' => 'L\'élégance africaine, de Conakry au monde',
        'phone' => '+224 622 00 00 00',
        'whatsapp' => '224622000000',
        'email' => 'contact@mombeyagaly.com',
        'address' => 'Kaloum, Conakry – Guinée',
        'topbar_message' => 'Assistance clientèle disponible 24h/24 et 7j/7',
        'orange_money_number' => '+224 622 00 00 00',
        'mtn_money_number' => '+224 664 00 00 00',
        'usd_rate' => '8650',
        'eur_rate' => '10100',
        'facebook_url' => 'https://www.facebook.com/',
        'instagram_url' => 'https://www.instagram.com/',
        'tiktok_url' => 'https://www.tiktok.com/',
        'youtube_url' => 'https://www.youtube.com/',
        'twitter_url' => 'https://x.com/',
        'promo_threshold' => '450000',
        'pressing_express_percent' => '50',
        'pressing_delay_text' => '48h',
        'pressing_slots' => '08:00 – 12:00, 12:00 – 16:00, 16:00 – 20:00',
        'pressing_offer_text' => 'Prenez soin de vos tenues : confiez-les à Mombeya Galy Pressing, collecte et livraison à domicile.',
        'announcement_messages' => 'Retrait gratuit dans nos maisons | Pressing : collecte à domicile à Ratoma | Sur mesure : prenez rendez-vous avec nos tailleurs | Livraison internationale en moins de 5 jours',
        'atelier_title' => 'Le savoir-faire de nos tailleurs',
        'atelier_text' => 'Chaque pièce Mombeya Galy est coupée, brodée et finie à la main dans nos ateliers de Conakry. Bazin riche, broderies traditionnelles, coupes modernes : nous habillons vos grands moments depuis plus de quinze ans.',
        'atelier_years' => '15',
        'atelier_pieces' => '5 000',
        'atelier_image' => '',
        'whatsapp_community_url' => '',
        'sur_mesure_types' => 'Grand boubou, Costume africain, Tenue de mariage, Tenue femme, Tenue enfant, Uniformes (famille, entreprise, association)',
        'sur_mesure_slots' => '09:00 – 11:00, 11:00 – 13:00, 14:00 – 16:00, 16:00 – 18:00',
        'delivery_text' => 'Livraison à Conakry en 24h et partout dans le monde par DHL',
        'payment_text' => 'Orange Money, MTN MoMo, Carte bancaire, Western Union, Ria',
    ],

    'currencies' => [
        'GNF' => ['label' => 'GNF', 'symbol' => 'GNF'],
        'USD' => ['label' => '$ USD', 'symbol' => '$'],
        'EUR' => ['label' => '€ EUR', 'symbol' => '€'],
    ],

    'per_page' => 36,

    'max_quantity' => 20,
];
