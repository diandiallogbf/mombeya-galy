<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\CurrencyController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\PressingController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\ShowroomController;
use App\Http\Controllers\TrackingController;
use App\Http\Controllers\VideoController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

// Catalogue
Route::get('/boutique', [ShopController::class, 'index'])->name('shop.index');
Route::get('/nouveautes', [ShopController::class, 'news'])->name('shop.news');
Route::get('/promotions', [ShopController::class, 'promotions'])->name('shop.promotions');
Route::get('/prestige', [ShopController::class, 'prestige'])->name('shop.prestige');
Route::get('/categorie/{category}', [ShopController::class, 'category'])->name('shop.category');
Route::get('/produit/{product}', [ProductController::class, 'show'])->name('product.show');
Route::get('/produit/{product}/apercu', [ProductController::class, 'quickView'])->name('product.quick-view');

// Cart & checkout
Route::get('/panier', [CartController::class, 'index'])->name('cart.index');
Route::post('/panier', [CartController::class, 'store'])->name('cart.store');
Route::patch('/panier/{key}', [CartController::class, 'update'])->name('cart.update');
Route::delete('/panier/{key}', [CartController::class, 'destroy'])->name('cart.destroy');
Route::get('/commande', [CheckoutController::class, 'create'])->name('checkout.create');
Route::post('/commande', [CheckoutController::class, 'store'])->name('checkout.store')->middleware('throttle:10,1');
Route::get('/commande/{order}/confirmation', [CheckoutController::class, 'confirmation'])->name('checkout.confirmation');
Route::get('/suivi-commande', [TrackingController::class, 'show'])->name('tracking');

// Pages
Route::get('/showrooms', [ShowroomController::class, 'index'])->name('showrooms');
Route::redirect('/contact', '/showrooms');
Route::get('/videos', [VideoController::class, 'index'])->name('videos');
Route::get('/devise/{code}', CurrencyController::class)->name('currency');

// Pressing
Route::get('/pressing', [PressingController::class, 'index'])->name('pressing.index');
Route::get('/pressing/reserver', [PressingController::class, 'create'])->name('pressing.create');
Route::post('/pressing/reserver', [PressingController::class, 'store'])->name('pressing.store')->middleware('throttle:10,1');
Route::get('/pressing/{pressingOrder}/confirmation', [PressingController::class, 'confirmation'])->name('pressing.confirmation');

// Customer accounts
Route::middleware('guest')->group(function () {
    Route::get('/connexion', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/connexion', [AuthController::class, 'login'])->name('login.attempt')->middleware('throttle:10,1');
    Route::post('/inscription', [AuthController::class, 'register'])->name('register')->middleware('throttle:10,1');
    Route::get('/mot-de-passe/oublie', [PasswordResetController::class, 'request'])->name('password.request');
    Route::post('/mot-de-passe/oublie', [PasswordResetController::class, 'email'])->name('password.email')->middleware('throttle:5,1');
    Route::get('/mot-de-passe/reinitialiser/{token}', [PasswordResetController::class, 'reset'])->name('password.reset');
    Route::post('/mot-de-passe/reinitialiser', [PasswordResetController::class, 'update'])->name('password.update');
});

Route::middleware('auth')->group(function () {
    Route::post('/deconnexion', [AuthController::class, 'logout'])->name('logout');
    Route::get('/mon-compte', [AccountController::class, 'index'])->name('account.index');
    Route::put('/mon-compte', [AccountController::class, 'update'])->name('account.update');
    Route::get('/mon-compte/commandes/{order}', [AccountController::class, 'order'])->name('account.order');
    Route::get('/mon-compte/pressing/{pressingOrder}', [AccountController::class, 'pressingOrder'])->name('account.pressing');
});
