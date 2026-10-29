<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ClaimTicketResource\Pages;
use App\Models\ClaimTicket;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ClaimTicketResource extends Resource
{
    protected static ?string $model = ClaimTicket::class;

    protected static ?string $navigationIcon = 'heroicon-o-ticket';

    protected static ?string $navigationGroup = 'Laporan Barang';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('lost_item_id')
                    ->label('Barang Hilang')
                    ->relationship('lostItem', 'title')
                    ->searchable()
                    ->preload()
                    ->required(),
                Forms\Components\Select::make('found_item_id')
                    ->label('Barang Temuan')
                    ->relationship('foundItem', 'title')
                    ->searchable()
                    ->preload()
                    ->required(),
                Forms\Components\TextInput::make('ai_match_score')
                    ->label('AI Match Score (%)')
                    ->numeric()
                    ->minValue(0)
                    ->maxValue(100)
                    ->default(0)
                    ->required(),
                Forms\Components\Select::make('status')
                    ->label('Status')
                    ->options([
                        'open' => 'Open',
                        'verified' => 'Verified',
                        'rejected' => 'Rejected',
                    ])
                    ->default('open')
                    ->required(),
                Forms\Components\Select::make('admin_status')
                    ->label('Status Moderasi Admin')
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                    ])
                    ->default('pending')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID Tiket')
                    ->sortable(),
                Tables\Columns\TextColumn::make('lostItem.title')
                    ->label('Item Hilang')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('foundItem.title')
                    ->label('Item Temuan')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('ai_match_score')
                    ->label('ai_match_score')
                    ->badge()
                    ->color(fn (int $state): string => match (true) {
                        $state >= 80 => 'success',
                        $state >= 50 => 'warning',
                        default => 'danger',
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('admin_status')
                    ->label('admin_status')
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        'pending' => 'warning',
                        'approved' => 'success',
                        'rejected' => 'danger',
                        default => 'gray',
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status Tiket')
                    ->badge()
                    ->colors([
                        'warning' => 'open',
                        'success' => 'verified',
                        'danger' => 'rejected',
                    ])
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),
                Tables\Columns\TextColumn::make('qr_token')
                    ->label('QR Token')
                    ->copyable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat Pada')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('admin_status')
                    ->label('Status Moderasi Admin')
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                    ]),
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'open' => 'Open',
                        'verified' => 'Verified',
                        'rejected' => 'Rejected',
                    ]),
            ])
            ->actions([
                Tables\Actions\Action::make('approve')
                    ->label('Approve')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (ClaimTicket $record): bool => $record->admin_status === 'pending')
                    ->requiresConfirmation()
                    ->action(function (ClaimTicket $record): void {
                        $record->update([
                            'admin_status' => 'approved',
                            'qr_token' => $record->qr_token ?: (string) \Illuminate\Support\Str::uuid(),
                        ]);
                    }),
                Tables\Actions\Action::make('reject')
                    ->label('Reject')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn (ClaimTicket $record): bool => $record->admin_status !== 'rejected')
                    ->requiresConfirmation()
                    ->action(function (ClaimTicket $record): void {
                        $record->update(['admin_status' => 'rejected']);
                    }),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListClaimTickets::route('/'),
            'create' => Pages\CreateClaimTicket::route('/create'),
            'edit' => Pages\EditClaimTicket::route('/{record}/edit'),
        ];
    }
}
