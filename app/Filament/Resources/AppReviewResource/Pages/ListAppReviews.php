<?php

namespace App\Filament\Resources\AppReviewResource\Pages;

use App\Filament\Resources\AppReviewResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAppReviews extends ListRecords
{
    protected static string $resource = AppReviewResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
