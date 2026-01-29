<?php

namespace App\Filament\Resources\ApplicationResource\Pages;

use App\Filament\Resources\ApplicationResource;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;

class ListApplications extends ListRecords
{
    protected static string $resource = ApplicationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        $tabs = [
            "Hamısı" => Tab::make(),
            "Elektron Müraciətlər"=>Tab::make()->modifyQueryUsing(function($query){
                $query->where('applicationType','O');
            }),
            "Fiziki  Müraciətlər"=>Tab::make()->modifyQueryUsing(function($query){
                $query->where('applicationType','F');
            }),
        ];
    
        if (auth()->user()->isAdmin() || auth()->user()->isKobimAdmin()) {
            $tabs["Yönləndirilməmiş müraciətlər"] = Tab::make()->modifyQueryUsing(function ($query) {
                $query->whereDoesntHave('employeeTasks');
            });
        }


    
        return $tabs;
    }
    

}
