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
        'foundation_donation_name' => 'Fondation Mombeya Galy',
        'foundation_donation_number' => '+224 622 00 00 00',
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
