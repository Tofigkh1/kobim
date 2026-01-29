<?php

namespace App\Filament\Resources\SmeInformationResource\Pages;

use App\Filament\Resources\SmeInformationResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSmeInformation extends ListRecords
{
    protected static string $resource = SmeInformationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
