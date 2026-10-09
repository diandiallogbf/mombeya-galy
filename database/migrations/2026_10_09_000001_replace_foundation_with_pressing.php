<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('aid_requests');
        Schema::dropIfExists('foundation_posts');

        Schema::create('pressing_services', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('category')->default('homme');
            $table->string('description')->nullable();
            $table->unsignedBigInteger('price');
            $table->string('unit')->default('pièce');
            $table->string('icon')->nullable();
            $table->unsignedInteger('position')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('pressing_zones', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedBigInteger('fee')->default(0);
            $table->string('note')->nullable();
            $table->unsignedInteger('position')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('pressing_orders', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('customer_name');
            $table->string('customer_phone');
            $table->string('customer_email')->nullable();
            $table->string('mode')->default('collecte');
            $table->foreignId('pressing_zone_id')->nullable()->constrained()->nullOnDelete();
            $table->string('zone_name')->nullable();
            $table->string('address')->nullable();
            $table->foreignId('showroom_id')->nullable()->constrained()->nullOnDelete();
            $table->string('showroom_name')->nullable();
            $table->date('pickup_date')->nullable();
            $table->string('pickup_slot')->nullable();
            $table->boolean('express')->default(false);
            $table->unsignedBigInteger('subtotal')->default(0);
            $table->unsignedBigInteger('express_fee')->default(0);
            $table->unsignedBigInteger('collection_fee')->default(0);
            $table->unsignedBigInteger('total')->default(0);
            $table->string('payment_method');
            $table->string('payment_status')->default('en_attente');
            $table->string('status')->default('demande_recue');
            $table->text('notes')->nullable();
            $table->text('admin_notes')->nullable();
            $table->timestamps();
        });

        Schema::create('pressing_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pressing_order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('pressing_service_id')->nullable()->constrained()->nullOnDelete();
            $table->string('service_name');
            $table->unsignedInteger('quantity');
            $table->unsignedBigInteger('unit_price');
            $table->unsignedBigInteger('total');
            $table->timestamps();
        });

        Schema::table('showrooms', function (Blueprint $table) {
            $table->boolean('accepts_pressing')->default(true)->after('longitude');
        });
    }

    public function down(): void
    {
        Schema::table('showrooms', function (Blueprint $table) {
            $table->dropColumn('accepts_pressing');
        });
        Schema::dropIfExists('pressing_order_items');
        Schema::dropIfExists('pressing_orders');
        Schema::dropIfExists('pressing_zones');
        Schema::dropIfExists('pressing_services');

        Schema::create('foundation_posts', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('cover')->nullable();
            $table->json('gallery')->nullable();
            $table->longText('content')->nullable();
            $table->date('published_at')->nullable();
            $table->boolean('is_published')->default(true);
            $table->timestamps();
        });

        Schema::create('aid_requests', function (Blueprint $table) {
            $table->id();
            $table->string('full_name');
            $table->string('phone');
            $table->string('transfer_method')->nullable();
            $table->string('email')->nullable();
            $table->text('message')->nullable();
            $table->string('status')->default('nouvelle');
            $table->timestamps();
        });
    }
};
