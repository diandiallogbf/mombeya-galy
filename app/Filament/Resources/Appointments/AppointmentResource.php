<?php

namespace App\Filament\Resources\Appointments;

use App\Enums\AppointmentStatus;
use App\Filament\Resources\Appointments\Pages\ManageAppointments;
use App\Models\Appointment;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\ImageEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use UnitEnum;

class AppointmentResource extends Resource
{
    protected static ?string $model = Appointment::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedScissors;

    protected static string|UnitEnum|null $navigationGroup = 'Ventes';

    protected static ?string $navigationLabel = 'Rendez-vous sur mesure';

    protected static ?string $modelLabel = 'rendez-vous';

    protected static ?string $pluralModelLabel = 'rendez-vous sur mesure';

    protected static ?string $recordTitleAttribute = 'reference';

    protected static ?int $navigationSort = 2;

    public static function getNavigationBadge(): ?string
    {
        $count = Appointment::where('status', AppointmentStatus::Requested)->count();

        return $count ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                TextInput::make('customer_name')->label('Client')->required(),
                TextInput::make('customer_phone')->label('Téléphone')->required(),
                TextInput::make('customer_email')->label('Email')->email(),
                TextInput::make('outfit_type')->label('Tenue')->required(),
                TextInput::make('occasion')->label('Occasion'),
                TextInput::make('budget')->label('Budget'),
                DatePicker::make('preferred_date')->label('Date')->native(false)->displayFormat('d/m/Y')->required(),
                TextInput::make('preferred_slot')->label('Créneau'),
                Select::make('showroom_id')->label('Maison')->relationship('showroom', 'name'),
                Select::make('status')->label('Statut')->options(AppointmentStatus::class)->required()->native(false),
                Textarea::make('message')->label('Projet du client')->rows(3)->disabled()->columnSpanFull(),
                ImageEntry::make('inspiration_image')->label('Photo d\'inspiration')->disk('public')->imageHeight(220)->placeholder('Aucune photo')->columnSpanFull(),
                Textarea::make('admin_notes')->label('Notes internes')->rows(3)->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('preferred_date')
            ->columns([
                TextColumn::make('preferred_date')->label('Date')->date('d/m/Y')->description(fn (Appointment $record) => $record->preferred_slot)->sortable(),
                TextColumn::make('customer_name')->label('Client')->searchable()->description(fn (Appointment $record) => $record->customer_phone),
                TextColumn::make('outfit_type')->label('Tenue')->description(fn (Appointment $record) => $record->occasion),
                TextColumn::make('showroom_name')->label('Maison')->toggleable(),
                TextColumn::make('reference')->label('Référence')->fontFamily('mono')->toggleable(isToggledHiddenByDefault: true),
                SelectColumn::make('status')->label('Statut')->options(AppointmentStatus::class)->selectablePlaceholder(false),
            ])
            ->filters([
                SelectFilter::make('status')->label('Statut')->options(AppointmentStatus::class),
            ])
            ->recordActions([
                Action::make('whatsapp')
                    ->label('WhatsApp')
                    ->icon(Heroicon::OutlinedChatBubbleLeftRight)
                    ->color('success')
                    ->url(function (Appointment $record) {
                        $phone = preg_replace('/\D+/', '', $record->customer_phone);
                        $phone = strlen($phone) === 9 ? '224'.$phone : $phone;
                        $text = "Bonjour {$record->customer_name}, Mombeya Galy vous contacte pour votre rendez-vous sur mesure du {$record->preferred_date->format('d/m/Y')} ({$record->preferred_slot}).";

                        return 'https://wa.me/'.$phone.'?text='.rawurlencode($text);
                    }, shouldOpenInNewTab: true),
                EditAction::make()->label('Gérer'),
                DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return ['index' => ManageAppointments::route('/')];
    }
}
