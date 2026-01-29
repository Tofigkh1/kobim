<?php

namespace App\Filament\Resources\SmeInformationResource\Pages;

use App\Filament\Resources\SmeInformationResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSmeInformation extends EditRecord
{
    protected static string $resource = SmeInformationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
         return $this->getResource()::getUrl('index');
         
    }
}
