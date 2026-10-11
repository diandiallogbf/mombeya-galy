<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

/**
 * @property-read Schema $form
 */
class Settings extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    protected static string|UnitEnum|null $navigationGroup = 'Paramètres';

    protected static ?string $navigationLabel = 'Paramètres de la boutique';

    protected static ?string $title = 'Paramètres de la boutique';

    protected static ?string $slug = 'parametres';

    protected static ?int $navigationSort = 1;

    /** @var array<string, mixed> */
    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill(Setting::allValues());
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->components([
                Section::make('Boutique & contact')
                    ->columns(2)
                    ->schema([
                        TextInput::make('shop_name')->label('Nom de la boutique')->required(),
                        TextInput::make('slogan')->label('Slogan'),
                        TextInput::make('phone')->label('Téléphone principal')->required(),
                        TextInput::make('whatsapp')->label('Numéro WhatsApp')->helperText('Format international sans + ni espaces, ex : 224622000000')->required()->regex('/^\d{8,15}$/'),
                        TextInput::make('email')->label('Email')->email(),
                        TextInput::make('address')->label('Adresse'),
                        TextInput::make('topbar_message')->label('Message de la barre du haut')->columnSpanFull(),
                    ]),
                Section::make('Paiement & devises')
                    ->columns(2)
                    ->schema([
                        TextInput::make('orange_money_number')->label('Numéro marchand Orange Money'),
                        TextInput::make('mtn_money_number')->label('Numéro marchand MTN MoMo'),
                        TextInput::make('usd_rate')->label('Taux : 1 USD =')->numeric()->minValue(1)->suffix('GNF')->required(),
                        TextInput::make('eur_rate')->label('Taux : 1 EUR =')->numeric()->minValue(1)->suffix('GNF')->required(),
                        TextInput::make('promo_threshold')->label('Seuil « Petits prix » (pied de page)')->numeric()->suffix('GNF'),
                    ]),
                Section::make('Textes du pied de page')
                    ->schema([
                        TextInput::make('delivery_text')->label('Livraison'),
                        TextInput::make('payment_text')->label('Moyens de paiement'),
                    ]),
                Section::make('Réseaux sociaux')
                    ->description('Laisser vide pour masquer l\'icône.')
                    ->columns(2)
                    ->schema([
                        TextInput::make('facebook_url')->label('Facebook')->url(),
                        TextInput::make('instagram_url')->label('Instagram')->url(),
                        TextInput::make('tiktok_url')->label('TikTok')->url(),
                        TextInput::make('youtube_url')->label('YouTube')->url(),
                        TextInput::make('twitter_url')->label('X (Twitter)')->url(),
                    ]),
                Section::make('Page d\'accueil')
                    ->columns(2)
                    ->schema([
                        TextInput::make('announcement_messages')
                            ->label('Messages du bandeau défilant')
                            ->helperText('Séparés par le caractère | (barre verticale). Laisser vide pour masquer le bandeau.')
                            ->columnSpanFull(),
                        TextInput::make('atelier_title')->label('Titre de la section « Notre atelier »')->columnSpanFull(),
                        Textarea::make('atelier_text')->label('Texte de la section atelier')->rows(3)->columnSpanFull(),
                        TextInput::make('atelier_years')->label('Chiffre : années de savoir-faire')->helperText('À remplacer par votre vrai chiffre.'),
                        TextInput::make('atelier_pieces')->label('Chiffre : pièces confectionnées')->helperText('À remplacer par votre vrai chiffre.'),
                        FileUpload::make('atelier_image')
                            ->label('Photo de l\'atelier')
                            ->helperText('Format portrait conseillé. Utilisée aussi en tête de la page Sur mesure.')
                            ->image()
                            ->disk('public')
                            ->directory('site')
                            ->maxSize(5120)
                            ->columnSpanFull(),
                        TextInput::make('whatsapp_community_url')
                            ->label('Lien de la communauté WhatsApp (chaîne ou groupe)')
                            ->url()
                            ->helperText('Laisser vide pour ouvrir une conversation WhatsApp avec la boutique.')
                            ->columnSpanFull(),
                    ]),
                Section::make('Sur mesure')
                    ->schema([
                        TextInput::make('sur_mesure_types')->label('Types de tenues proposés')->helperText('Séparés par des virgules.')->required(),
                        TextInput::make('sur_mesure_slots')->label('Créneaux de rendez-vous')->helperText('Séparés par des virgules, ex : 09:00 – 11:00, 14:00 – 16:00')->required(),
                    ]),
                Section::make('Pressing')
                    ->description('Les services, tarifs et zones de collecte se gèrent dans le menu Pressing.')
                    ->columns(2)
                    ->schema([
                        TextInput::make('pressing_express_percent')->label('Supplément express')->numeric()->minValue(0)->maxValue(300)->suffix('%')->required(),
                        TextInput::make('pressing_delay_text')->label('Délai standard affiché')->placeholder('48h')->required(),
                        TextInput::make('pressing_slots')
                            ->label('Créneaux de collecte')
                            ->helperText('Séparés par des virgules, ex : 08:00 – 12:00, 12:00 – 16:00')
                            ->required()
                            ->columnSpanFull(),
                        TextInput::make('pressing_offer_text')
                            ->label('Message pressing après un achat')
                            ->helperText('Affiché sur la page de confirmation de commande. Laisser vide pour le masquer.')
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public function content(Schema $schema): Schema
    {
        return $schema->components([
            Form::make([EmbeddedSchema::make('form')])
                ->id('form')
                ->livewireSubmitHandler('save')
                ->footer([
                    Actions::make([
                        Action::make('save')->label('Enregistrer')->submit('save')->keyBindings(['mod+s']),
                    ])->sticky(),
                ]),
        ]);
    }

    public function save(): void
    {
        Setting::put($this->form->getState());

        Notification::make()->success()->title('Paramètres enregistrés')->send();
    }
}
