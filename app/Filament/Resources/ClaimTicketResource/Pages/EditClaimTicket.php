<?php

namespace App\Filament\Resources\ClaimTicketResource\Pages;

use App\Filament\Resources\ClaimTicketResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditClaimTicket extends EditRecord
{
    protected static string $resource = ClaimTicketResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
