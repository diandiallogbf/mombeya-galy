# Mombeya Galy – boutique en ligne

Boutique de mode africaine (Conakry) construite avec **Laravel 13**, **Filament 5** (administration), **Tailwind CSS 4**, **Alpine.js** et **SQLite**.

## Démarrer le site

Double-cliquez sur `demarrer.bat`, ou dans un terminal :

```bash
php artisan serve --host=127.0.0.1 --port=8010
```

- Site : http://127.0.0.1:8010
- Administration : http://127.0.0.1:8010/admin

> Le port 8000 est déjà utilisé par un autre projet (`galyimmo`), d'où le port 8010.

## Comptes

Les données de démonstration créent un compte administrateur et un compte client de test. Leurs identifiants ne figurent volontairement pas dans ce fichier.

**Changez le mot de passe administrateur** dans *Administration → Clients & comptes*, et supprimez le compte client de test avant toute mise en ligne.

## Commandes utiles

```bash
php artisan migrate:fresh --seed   # réinitialise la base avec les données de démonstration (efface tout !)
npm run build                      # recompile le CSS/JS après une modification des vues
php artisan test                   # lance les tests automatiques
```

## Où modifier quoi

| Je veux… | Où |
|---|---|
| Ajouter / modifier des produits, photos, tailles, prix | Admin → Catalogue → Produits |
| Gérer les catégories et les vignettes de l'accueil | Admin → Catalogue → Catégories |
| Traiter les commandes (statut, paiement, WhatsApp client) | Admin → Ventes → Commandes |
| Changer les bannières de l'accueil | Admin → Contenu du site → Bannières d'accueil |
| Téléphone, WhatsApp, numéros Orange Money / MTN, taux USD/EUR, réseaux sociaux | Admin → Paramètres → Paramètres de la boutique |
| Frais et délais de livraison | Admin → Paramètres → Zones de livraison |
| Traiter les réservations pressing | Admin → Pressing → Commandes pressing |
| Tarifs du pressing | Admin → Pressing → Services & tarifs |
| Ouvrir la collecte dans une nouvelle commune | Admin → Pressing → Zones de collecte (activer la zone) |
| Showrooms qui acceptent les dépôts pressing | Admin → Contenu du site → Showrooms (« Point de dépôt pressing ») |
| Couleurs du site | `resources/css/app.css` (bloc `@theme`), puis `npm run build` |
| Logo | `public/images/logo.png` (horizontal) et `public/images/favicon.png` |

## Structure

- `app/Http/Controllers` – pages publiques (boutique, panier, commande, compte…)
- `app/Filament` – panneau d'administration (ressources, page Paramètres, widgets du tableau de bord)
- `app/Support` – panier (session), devises, médias
- `resources/views` – gabarits Blade du site
- `database/seeders` – données de démonstration et générateur d'illustrations provisoires
