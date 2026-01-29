<?php

namespace App\Filament\Resources\SmeInformationResource\Pages;

use App\Filament\Resources\SmeInformationResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateSmeInformation extends CreateRecord
{
    protected static string $resource = SmeInformationResource::class;


    protected function getRedirectUrl(): string
    {
         return $this->getResource()::getUrl('index');
         
    }
}
