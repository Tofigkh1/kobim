<?php

namespace App\Filament\Resources\SmeInformationResource\Pages;

use App\Filament\Resources\SmeInformationResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewSmeInformation extends ViewRecord
{
    protected static string $resource = SmeInformationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
