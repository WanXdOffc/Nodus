<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserReportResource\Pages;
use App\Models\UserReport;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class UserReportResource extends Resource
{
    protected static ?string $model = UserReport::class;

    protected static ?string $navigationIcon = 'heroicon-o-shield-exclamation';

    protected static ?string $navigationGroup = 'Moderasi';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('reporter_id')
                    ->label('Pelapor')
                    ->relationship('reporter', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                Forms\Components\Select::make('reported_user_id')
                    ->label('Pengguna yang Dilaporkan')
                    ->relationship('reportedUser', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                Forms\Components\Textarea::make('reason')
                    ->label('Alasan Laporan')
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\Select::make('status')
                    ->label('Status')
                    ->options([
                        'pending' => 'Pending',
                        'reviewed' => 'Reviewed',
                    ])
                    ->default('pending')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('reporter.name')
                    ->label('Pelapor')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('reportedUser.name')
                    ->label('Yang Dilaporkan')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('reason')
                    ->label('Alasan')
                    ->limit(50)
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'reviewed',
                    ])
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Waktu Lapor')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'reviewed' => 'Reviewed',
                    ]),
            ])
            ->actions([
                Tables\Actions\Action::make('validateFraud')
                    ->label('Validasi Laporan (Penipuan)')
                    ->icon('heroicon-o-shield-exclamation')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Validasi Laporan Penipuan')
                    ->modalDescription('Tindakan ini akan menandai laporan sebagai reviewed dan mengurangi Trust Score pengguna yang dilaporkan sebesar -2.0. Jika skor mencapai 0 atau kurang, akun akan otomatis diblokir.')
                    ->visible(fn (UserReport $record): bool => $record->status === 'pending')
                    ->action(function (UserReport $record): void {
                        $record->update(['status' => 'reviewed']);

                        $reportedUser = $record->reportedUser;
                        if ($reportedUser) {
                            $currentScore = (float) $reportedUser->trust_score;
                            $newScore = round($currentScore - 2.0, 1);
                            $isBanned = false;

                            if ($newScore <= 0) {
                                $newScore = 0.0;
                                $isBanned = true;
                                $reportedUser->is_banned = true;
                                $reportedUser->tokens()->delete();
                            }

                            $reportedUser->trust_score = $newScore;
                            $reportedUser->save();

                            Notification::make()
                                ->title('Laporan Penipuan Divalidasi')
                                ->body("Trust score {$reportedUser->name} dikurangi menjadi {$newScore}" . ($isBanned ? " dan akun telah otomatis diblokir." : "."))
                                ->warning()
                                ->send();
                        }
                    }),
                Tables\Actions\Action::make('markAsReviewed')
                    ->label('Tandai Reviewed')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Tandai Laporan Telah Ditinjau')
                    ->modalDescription('Apakah Anda yakin ingin mengubah status laporan ini menjadi reviewed?')
                    ->visible(fn (UserReport $record): bool => $record->status !== 'reviewed')
                    ->action(function (UserReport $record): void {
                        $record->update(['status' => 'reviewed']);
                        Notification::make()
                            ->title('Laporan berhasil ditinjau')
                            ->success()
                            ->send();
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
            'index' => Pages\ListUserReports::route('/'),
            'create' => Pages\CreateUserReport::route('/create'),
            'edit' => Pages\EditUserReport::route('/{record}/edit'),
        ];
    }
}
