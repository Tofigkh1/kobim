<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SmeInformationResource\Pages;
use App\Filament\Resources\SmeInformationResource\RelationManagers;
use App\Models\SmeInformation;
use App\Models\User;
use App\Services\ApiClient;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SmeInformationResource extends Resource
{
    protected static ?string $model = SmeInformation::class;

    protected static ?string $navigationIcon = 'heroicon-o-home-modern';

    
    protected static ?string $recordTitleAttribute = 'smeName';
    protected static ?string $modelLabel = 'Kobim';
    protected static ?string $pluralModelLabel = 'KOBİM Məlumatları';
    protected static ?string $navigationGroup = 'KOBİM Bölməsi';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Section::make()->schema([
                    FileUpload::make("icon")->label("Şəkil")->required()->avatar(),
                    TextInput::make("smeName")->label("KOMBIM adı")->disabled(fn() => !auth()->user()->isAdmin()),
                    TextInput::make("smeLocation")->label("KOMBIM ünvanı"),
                ])->columns(3),
                Section::make()->schema([
                    TextInput::make("contactNumber")->label("Əlaqə nömrəsi")->numeric(),
                    TextInput::make("contactEmail")->label("Email adresi"),
                    
                ])->columns(2),
                Section::make("Sosial media")->schema([
                    TextInput::make("facebook"),
                    TextInput::make("instagram"),
                    TextInput::make("linkedin"),
                    TextInput::make("youtube")
                ])->columns(2),
                Section::make()->schema([
                    TextInput::make('voen')
                    ->required()
                    ->label('Vöen')
                    ->reactive()
                    ->minLength(10)
                    ->maxLength(10)
                    ->live()
                    ->afterStateUpdated(function (HasForms $livewire, TextInput $component) {
                        $livewire->validateOnly($component->getStatePath());
                        $state = $component->getState();
                        $set = $component->getSetCallback();
                        
                        function clearVoenDetails(callable $set) {
                            $set('executiveCompany', '');
                        }
                        
                        if ($state && strlen($state) === 10){
                            $details = ApiClient::getUserDetailsByVoen($state);
                            if (isset($details['name'])) {
                                $set('executiveCompany', $details['name']);

                                Notification::make()
                                ->title('VÖEN')
                                ->body('VÖEN məlumatları gətirildi.')
                                ->icon("heroicon-o-check-circle")
                                ->success()
                                ->send();

                            }
                            else if (isset($details['statusCode']) && $details['statusCode'] == 417){
                                clearVoenDetails($set);

                                Notification::make()
                                    ->title('Yanlış VÖEN')
                                    ->body('Daxil edilən VÖEN yanlışdır və ya ləğv edilib')
                                    ->icon("heroicon-o-exclamation-circle")
                                    ->danger()
                                    ->send();
                            }   
                             else {
                                clearVoenDetails($set);                                    
                                Notification::make()
                                ->title('Server xətası')
                                ->body('Server cavab vermir')
                                ->icon("heroicon-o-exclamation-circle")
                                ->danger()
                                ->send();  

                            }
                        }
                        if(strlen($state)==0){
                            clearVoenDetails($set);

                        }
                        if(strlen($state) !==10){
                            clearVoenDetails($set);
                        }

                    }),
                    TextInput::make("executiveCompany")
                    ->label("İcraçı Şirkət")
                    ->readOnly()
                    ->reactive()
                    ->live(),
                    // Select::make("teamLeader_id")->options(function ($record) {
                    //     return User::where('visibility', 1)
                    //         ->where('sme_id', $record->id ?? null)
                    //         ->get()
                    //         ->mapWithKeys(function ($user) {
                    //             $fullName = $user->fullName ?? ($user->name ?? 'Unknown User');
                    //             return [$user->id => $fullName];
                    //         })
                    //         ->toArray();
                    // })->disabled(fn() => !auth()->user()->isAdmin())->label("Rəhbəri"),
                    TextInput::make("teamLeaderName")->label("Rəhbəri")->required()
                ])->columns(3),
            
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make("smeName")->label("KOMBIM adı")->searchable(),
                TextColumn::make("smeLocation")->label("KOMBIM ünvanı")->searchable()->toggleable(),
                TextColumn::make("contactNumber")->label("Əlaqə nömrəsi")->searchable()->toggleable(),
                TextColumn::make("contactEmail")->label("Email adresi")->searchable()->toggleable(),
                TextColumn::make("executiveCompany")->label("İcraçı Şirkət")->searchable(),
                // TextColumn::make("teamLeader")->label("Rəhbəri")->searchable(),
                TextColumn::make("teamLeaderName")->label("Rəhbəri")->searchable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                ActionGroup::make([
                    Tables\Actions\ReplicateAction::make(),
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                    ])->icon('heroicon-m-cog-8-tooth'),
            ])
            ->defaultSort('created_at','desc')
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
    
        if (auth()->user()->isAdmin()) {
            return $query;
        }
    
        return $query->where('teamLeader_id', auth()->id());
    }



    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSmeInformation::route('/'),
            'create' => Pages\CreateSmeInformation::route('/create'),
            'edit' => Pages\EditSmeInformation::route('/{record}/edit'),
            'view' => Pages\ViewSmeInformation::route('/{record}'),

        ];
    }
}
