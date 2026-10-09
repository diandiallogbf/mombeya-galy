# Rapport – site Mombeya Galy (nuit du 8 octobre 2026)

## Le site est prêt

- Site : **http://127.0.0.1:8010**. Si le PC a redémarré, double-cliquez sur `demarrer.bat`.
- Administration : **http://127.0.0.1:8010/admin**
  - Compte : `admin@mombeyagaly.com` / `MombeyaGaly@2026` (**à changer**).
- Compte client de test : `client@exemple.com` / `client1234`.
- Le port 8000 est occupé par ton autre projet `galyimmo`, d'où le port 8010.

**Technique :** Laravel 13.35, Filament 5.10, base SQLite (`database/database.sqlite`), Tailwind CSS 4, Alpine.js, Swiper. Tout le site est en français et les prix sont en **francs guinéens (GNF)**. Un sélecteur permet aussi d'afficher les prix en USD ou en EUR.

## Ce qui a été construit

### Site public

Il reprend la structure et l'ergonomie de 6point9.sn, aux couleurs du logo (rouge framboise et bleu).

- **En-tête :**
  - barre du haut : téléphone, suivi de commande, devise ;
  - menu : Accueil, Pressing, Prestige, Boutique (avec la liste des catégories), Vidéos, Nos showrooms ;
  - icônes compte et panier, avec un mini-panier au survol.
- **Mobile :** menu latéral, barre d'outils fixe en bas de l'écran.
- **Accueil :**
  - slider de bannières ;
  - vignettes de catégories (6 par ligne) ;
  - « Tendance de la semaine » en carrousel ;
  - bloc showrooms, bloc livraison internationale ;
  - « Tendance du mois » en grille ;
  - carrousel « Accessoires ».
- **Pages produits :** boutique, catégories, nouveautés, promotions, prestige. Chacune propose le tri (popularité, date, prix), une recherche et une pagination.
- **Carte produit :**
  - badge PROMO ou NOUVEAU, mention « Fin de stock » ;
  - au survol, choix de la taille ou de la couleur et ajout au panier sans rechargement ;
  - aperçu rapide dans une fenêtre.
- **Fiche produit :**
  - galerie avec zoom ;
  - taille, couleur et quantité, ajout au panier ;
  - bouton « Commander sur WhatsApp » ;
  - onglets Informations, Showrooms et Livraison ;
  - produits similaires.
- **Panier, puis commande en 3 étapes :** coordonnées, zone de livraison (frais calculés automatiquement), mode de paiement.
  - Une **référence de commande** est créée, avec les instructions de paiement et un bouton WhatsApp.
- **Suivi de commande :** recherche par référence, frise de l'avancement.
- **Showrooms :** horaires et lien « Localiser » vers Google Maps.
- **Pressing (ajouté le 9 octobre, remplace la Fondation) :** tarifs par catégorie (Homme, Femme, Spécial bazin, Maison), formules d'abonnement, réservation en ligne avec collecte à domicile ou dépôt en showroom, option express, total calculé en direct, référence `PR…` suivie sur la page « Suivi de commande ».
- **Vidéos :** liens YouTube, lus dans une fenêtre.
- **Espace client :** inscription, connexion, mot de passe oublié, historique des commandes.
- **Bouton WhatsApp** flottant sur toutes les pages, et page 404 personnalisée.

### Administration (Filament)

- **Tableau de bord :**
  - chiffre d'affaires du mois ;
  - commandes en attente et commandes du jour ;
  - produits en rupture de stock ;
  - graphique des ventes sur 30 jours ;
  - dernières commandes.
- **Commandes :**
  - onglets par statut ;
  - changement de statut directement depuis la liste ;
  - statut de paiement et notes internes ;
  - articles commandés ;
  - boutons « Contacter sur WhatsApp » et « Appeler ».
- **Produits :**
  - photos multiples réordonnables, avec un éditeur d'image ;
  - tailles et couleurs, prix promotionnel, stock ;
  - interrupteurs En ligne, Nouveauté, Prestige, Tendance ;
  - duplication de produit, actions groupées.
- **Catégories :** l'ordre se règle par glisser-déposer. Pour chaque catégorie, on choisit si elle apparaît sur l'accueil et dans le menu.
- **Contenu du site :** bannières d'accueil, showrooms (avec horaires), vidéos.
- **Pressing :** commandes pressing (onglets par statut, WhatsApp client), services et tarifs, zones de collecte (Ratoma active, Dixinn, Kaloum, Matam et Matoto prêtes à activer).
- **Zones de livraison :** frais et délais.
- **Clients & comptes :** donner ou retirer l'accès administrateur.
- **Paramètres de la boutique :**
  - téléphone, WhatsApp, email ;
  - numéros marchands Orange Money et MTN ;
  - taux USD et EUR ;
  - réseaux sociaux, textes du pied de page ;
  - pressing : supplément express, délai, créneaux de collecte, message après un achat.

### Données de démonstration

- 124 produits répartis dans 10 catégories.
- 8 showrooms (Kaloum, Kipé, Dixinn, Cosa, Lambanyi, Matoto, Nongo, Labé).
- 9 zones de livraison (Kaloum, Dixinn, Matam, Ratoma, Matoto, banlieue, intérieur du pays, international DHL, retrait gratuit en showroom).
- 4 bannières d'accueil, 14 commandes d'exemple.
- Pressing : 17 services (dont 3 formules) et 5 zones de collecte.

## Vérifications effectuées

- **10 tests automatiques** (`php artisan test`) passent. Ils couvrent :
  - les pages ;
  - le panier, dont la taille obligatoire et le refus d'un produit en rupture ;
  - la commande, le calcul du total et la baisse du stock ;
  - la confidentialité de la page de confirmation ;
  - le suivi de commande, la devise, l'inscription ;
  - l'accès à l'administration : un client est refusé, un administrateur est accepté.
- **Tests dans un vrai navigateur (Chrome automatisé) :**
  - parcours d'achat complet sur ordinateur : ajout au panier, commande, confirmation, suivi, connexion client ;
  - affichage mobile : aucun débordement horizontal ;
  - toutes les pages d'administration ;
  - création d'un produit, sauvegarde d'une commande et des paramètres.
  - Aucune erreur JavaScript.

## À savoir et à faire

1. **Photos provisoires.** Je n'ai copié **ni les photos, ni les textes, ni le logo de 6point9**, qui sont protégés par le droit d'auteur. Les produits, catégories, bannières et showrooms ont des illustrations générées aux couleurs de Mombeya Galy. Remplace-les par tes vraies photos dans l'administration.
2. **Coordonnées fictives.** Les numéros et adresses sont des exemples, par exemple +224 622 00 00 00. Mets les tiens dans *Paramètres de la boutique* et *Showrooms*.
3. **Paiement.** La commande est enregistrée et le client voit les instructions : numéro Orange Money ou MTN, montant, motif. Il n'y a **pas de paiement automatique** : brancher l'API Orange Money ou MTN demande un contrat marchand. Les statuts de paiement se mettent à jour à la main dans l'administration.
4. **Emails.** Ils ne sont pas encore envoyés (`MAIL_MAILER=log`). Les liens « mot de passe oublié » sont écrits dans `storage/logs/laravel.log` tant que le SMTP n'est pas configuré dans `.env`.
5. **Taux de change.** Ils sont approximatifs (1 USD = 8 650 GNF, 1 EUR = 10 100 GNF). Modifiable dans *Paramètres*.
6. **Écarts volontaires avec l'original :**
   - ajouts : menu déroulant des catégories sur ordinateur, recherche, boutons « Commander sur WhatsApp », frise de suivi de commande ;
   - le bloc « Télécharger l'application » est remplacé par un bloc WhatsApp, puisqu'il n'y a pas d'application ;
   - la page Vidéos est vide tant qu'aucune vidéo n'est ajoutée dans l'administration.

## Modifications faites sur ton PC

- **php.ini (XAMPP)** : j'ai activé les extensions `pdo_sqlite` et `sqlite3`, nécessaires pour SQLite. Une sauvegarde est dans `C:\xampp\php\php.ini.bak-avant-sqlite`.
- **Composer** : `repo.packagist.org` ne répond pas depuis ton réseau, alors que les miroirs fonctionnent. J'ai donc configuré globalement le miroir Tencent. Pour revenir en arrière : `composer config -g --unset repos.packagist`.
- Je n'ai rien touché au projet `galyimmo`, ni au serveur qui tourne sur le port 8000.

## Pour la mise en ligne, plus tard

Il faudra un hébergeur PHP 8.3 ou plus, puis :

1. Lancer `composer install --no-dev`, puis `npm run build`.
2. Dans `.env` : `APP_ENV=production`, `APP_DEBUG=false`, `APP_URL=https://ton-domaine`.
3. Lancer `php artisan migrate --force` et `php artisan optimize`. Il n'y a pas de `storage:link` à faire : les images sont enregistrées directement dans `public/storage`.
4. Configurer le SMTP pour les emails.
