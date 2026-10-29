<?php

namespace App\Filament\Resources\AppReviewResource\Pages;

use App\Filament\Resources\AppReviewResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAppReview extends EditRecord
{
    protected static string $resource = AppReviewResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
