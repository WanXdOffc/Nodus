<?php

namespace App\Filament\Resources\ClaimTicketResource\Pages;

use App\Filament\Resources\ClaimTicketResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListClaimTickets extends ListRecords
{
    protected static string $resource = ClaimTicketResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
