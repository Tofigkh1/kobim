<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ApplicationResource\Pages;
use App\Filament\Resources\ApplicationResource\RelationManagers;
use App\Helpers\ActivityAreaHelper;
use App\Helpers\CityHelper;
use App\Models\Application;
use App\Models\EmployeeTask;
use App\Models\Training;
use App\Models\User;
use App\Services\ActivityAreaClient;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Services\ApiClient;
use App\Services\CityClient;
use Closure;
use DateTime;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Infolists;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\Section as ComponentsSection;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\SelectColumn;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;

class ApplicationResource extends Resource
{
    protected static ?string $model = Application::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-bottom-center-text';

    protected static ?string $recordTitleAttribute = 'fullName';
    protected static ?string $modelLabel = 'Yeni Müraciət';
    protected static ?string $pluralModelLabel = 'Müraciətlər';
    protected static ?string $navigationGroup = 'Müraciətlər Bölməsi';

    protected $voenError ;

    protected $apiClient;

    public static function getApiClient(): ApiClient
    {
        return app(ApiClient::class);
    }
    
    public static function form(Form $form): Form
    {
        return $form
        ->schema([
            Hidden::make("sme_id")->default(auth()->user()->sme_id),
            Section::make()->schema([
                    Radio::make('employeeType')
                    ->label('')
                    ->options([
                        0 => 'Vətəndaş',
                        1 => 'Vergi ödəyicisi',
                    ])
                    ->disabled(function ($get) {
                        return  Application::find($get('id')) !== null;
                    })
                    ->default(0)
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $set) {
                        if ($state == 0) {
                            $set('voen', '');
                            $set('voenPersonName','');
                            $set('voenMeyar','');
                            $set('voenContactInfo','');
                            $set('voenAddress','');
                            $set('voenActivityName','');
                            $set('fullName','');
                        } elseif ($state == 1) {
                            $set('fullName', '');
                            $set('finBirthday','');
                            $set('city','');
                            $set('address','');
                            $set('finGender','');
                            $set('finRegistrationAddress','');
                            $set('finAge','');
                            $set('fin','');
                        }
                    })
                    ->required(!auth()->user()->isSuperAdmin()),
                TextInput::make('voen')
                    ->required(!auth()->user()->isSuperAdmin())
                    ->label('Vöen')
                    ->hidden(fn ($get) => $get('employeeType') != 1)
                    ->reactive()
                    ->minLength(10)
                    ->maxLength(10)
                    ->live()
                    ->afterStateUpdated(function (HasForms $livewire, TextInput $component) {
                        $livewire->validateOnly($component->getStatePath());
                        $state = $component->getState();
                        $set = $component->getSetCallback();
                        
                        function clearVoenDetails(callable $set) {
                            $set('voenPersonName', '');
                            $set('voenMeyar', '');
                            $set('voenContactInfo', '');
                            $set('voenAddress', '');
                            $set('voenActivityName', '');
                            $set('employeeCount', '');
                        }
                        
                        if ($state && strlen($state) === 10){
                            $details = self::getApiClient()->getUserDetailsByVoen($state);
                            if (isset($details['name'])) {
                                $set('voenPersonName', $details['name']);
                                $set('voenMeyar', $details['meyar']);
                                $set('voenContactInfo', $details['contact_number']);
                                $set('voenAddress', $details['address']);
                                $set('voenActivityName', $details['activity'][0]["activityName"]);
                                $set("employeeCount",$details['labor_count']);

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
                        if(strlen($state) !==10){
                            clearVoenDetails($set);
                        }

                    }),
                TextInput::make('fin')
                    ->label('Fin kod')
                    ->hidden(fn ($get) => $get('employeeType') != 0)
                    ->disabled(function ($get) {
                        return  Application::find($get('id')) !== null;
                    })
                    ->reactive()
                    ->required()
                    ->minLength(7)
                    ->maxLength(7)
                    ->live()
                    ->afterStateUpdated(function (HasForms $livewire, TextInput $component) {
                        $livewire->validateOnly($component->getStatePath());
                        
                        $state = $component->getState();
                        $set = $component->getSetCallback();
                        
                        function clearFinDetails(callable $set) {
                            $set('fullName', '');
                            $set('finBirthday','');
                            $set('city','');
                            $set('address','');
                            $set('finGender','');
                            $set('finRegistrationAddress','');
                            $set('finAge','');
                        }
                        
                        if ($state && strlen($state) === 7){
                            $details =  self::getApiClient()->getUserDetailsByFin($state);
                            function calculateAge($birthDate) {

                                $birthDate = new DateTime($birthDate);
                                $currentDate = new DateTime();
                                $interval = $currentDate->diff($birthDate);
                                
                                return $interval->y;
                            }
                            if (isset($details['Name'])) {
                                $birtday = $details['Birthday'];
                                $place = $details['RegistrationAddress'];
                                $parts = explode(',', $place);
                                $city = trim($parts[0]);
                                $address = $place;

                                $age = calculateAge($birtday);
                                $set('fullName', $details['Name'].' '.$details['Surname'].' '.$details['FatherName']);
                                $set('finBirthday',$details['Birthday']);
                                $set('finAge',$age);
                                $set('city',$city);
                                $set('address',$address);
                                if($details['Gender']=='M'){
                                    $gender = "Kişi";
                                }
                                else{
                                    $gender = "Qadın";
                                }
                                $set('finGender',$gender);
                                $set('finRegistrationAddress',$details['RegistrationAddress']);
                                Notification::make()
                                ->title('Finkod')
                                ->body('Vətəndaş məlumatları gətirildi')
                                ->icon("heroicon-o-check-circle")
                                ->success()
                                ->send();

                            }
                            else if (isset($details['error'])){
                                clearFinDetails($set);
                                Notification::make()
                                    ->title('Yanlış Finkod')
                                    ->body('Daxil edilən FinKod yanlışdır')
                                    ->icon("heroicon-o-exclamation-circle")
                                    ->danger()
                                    ->send();
                            }   
                             else {
                                clearFinDetails($set);                                    
                                Notification::make()
                                ->title('Server xətası')
                                ->body('Server cavab vermir')
                                ->icon("heroicon-o-exclamation-circle")
                                ->danger()
                                ->send();  

                            }
                        }
                        if(strlen($state) !==7){
                            clearFinDetails($set);
                        }

                    }),
            ])->columns(2),
            Section::make("Vergi ödəyicisi məlumatları")->schema([
                Section::make("")->schema([
                    TextInput::make("voenPersonName")->label("Ad")->readOnly()->required(),
                    TextInput::make("voenMeyar")->label("Meyar")->readOnly()->required(),
                    Select::make("fieldWantAct")
                    ->options(function () {
                        $area = app(ActivityAreaClient::class);
                        return $area->getAreaOptions();
                    })
                    ->searchable()
                    ->required()->label("Fəaliyyət göstərmək stədiyi sahə"),
                    ])->columns(3),
                    Hidden::make("voenContactInfo"),
                    Hidden::make("voenAddress"),
                    Hidden::make("voenActivityName"),
                    Hidden::make("employeeCount"),
                    Section::make("")->schema([
                        TextInput::make("voenContactInfo")->label("Əlaqə məlumatları")->readOnly(),
                        TextInput::make("voenAddress")->label("Address")->readOnly(),
                        TextInput::make("voenActivityName")->label("Fəaliyyət sahəsi")->readOnly(),
                        TextInput::make("employeeCount")->label("İşçi sayı")->readOnly()
                    ])->columns(2)->hidden(fn() => !auth()->user()->isAdmin()),

                ])
                ->hidden(fn ($get) => $get('employeeType') != 1)
                ->columns(2),
                Section::make("Fərdi məlumatlar")->schema([
                    TextInput::make("fullName")->label("Soyadı, adı, ata adı")
                    ->readOnly(fn($get)=> $get('employeeType') ==0)
                    ->live()
                    ->required()
                    ->reactive(),
                    Select::make("education")
                    ->options([
                        1 => 'Orta təhsil',
                        2 => 'Orta ixtisas',
                        3 => 'Ali',
                        4 => 'Tam ali',
                    ])
                    ->required()
                    ->label("Təhsili (orta ixtisas, ali, tam ali)"),
                        TextInput::make("finAge")->label("Yaşı")->hidden(fn ($get) => $get('employeeType') != 0)->readOnly(),
                        TextInput::make("finBirthday")->label("Doğum tarixi")->hidden()->readOnly(),
                        TextInput::make("finGender")->label("Cinsiyyəti")->hidden(fn ($get) => $get('employeeType') != 0)->readOnly(),
                        Select::make("fieldActivity")
                            ->helperText("Siyahıda olmayan fəaliyyət sahəsini '-' seçərək əlnən daxil edin")
                            ->label("Fəaliyyət sahəsi")
                            ->reactive()
                            ->options(function () {
                                $area = app(ActivityAreaClient::class);
                                return $area->getAreaOptions();
                            })
                            ->searchable()
                            ->required()
                            ->hidden(fn ($get) => $get('employeeType') != 0)->columnSpanFull(),
                        TextInput::make("otherFieldActivity")
                        ->label("Fəaliyyət sahəsinin adı")
                        ->columnSpanFull()
                        ->live()
                        ->required()
                        ->reactive()
                        ->hidden(fn($get)=>$get('fieldActivity') != 88888),    
                ])
                ->collapsible()
                ->columns(4)
                ->hidden(fn ($get) => $get('employeeType') != 0),
                Section::make("Vergi ödəyicisinin fərdi məlumatları")->schema([
                    TextInput::make('fin')
                    ->label('Fin kod')
                    ->hidden(fn ($get) => $get('employeeType') != 1)
                    ->disabled(function ($get) {
                        return  Application::find($get('id')) !== null;
                    })
                    ->reactive()
                    ->required()
                    ->minLength(7)
                    ->maxLength(7)
                    ->live()
                    ->afterStateUpdated(function (HasForms $livewire, TextInput $component) {
                        $livewire->validateOnly($component->getStatePath());
                        
                        $state = $component->getState();
                        $set = $component->getSetCallback();
                        
                        function clearVoenPersonDetails(callable $set) {
                            $set('fullName', '');
                        }
                        
                        if ($state && strlen($state) === 7){
                            $details = ApiClient::getUserDetailsByFin($state);
                            if (isset($details['Name'])) {
                                $set('fullName', $details['Name'].' '.$details['Surname'].' '.$details['FatherName']);
                                Notification::make()
                                ->title('Finkod')
                                ->body('Vətəndaş məlumatları gətirildi')
                                ->icon("heroicon-o-check-circle")
                                ->success()
                                ->send();

                            }
                            else if (isset($details['error'])){
                                clearVoenPersonDetails($set);
                                Notification::make()
                                    ->title('Yanlış Finkod')
                                    ->body('Daxil edilən FinKod yanlışdır')
                                    ->icon("heroicon-o-exclamation-circle")
                                    ->danger()
                                    ->send();
                            }   
                             else {
                                clearVoenPersonDetails($set);                                    
                                Notification::make()
                                ->title('Server xətası')
                                ->body('Server cavab vermir')
                                ->icon("heroicon-o-exclamation-circle")
                                ->danger()
                                ->send();  

                            }
                        }
                        if(strlen($state) !==7){
                            clearVoenPersonDetails($set);
                        }

                    }),
                    TextInput::make("fullName")->label("Soyadı, adı, ata adı")
                    ->readOnly()
                    ->live()
                    ->required()
                    ->reactive(),
                    Select::make("education")
                    ->options([
                        1 => 'Orta təhsil',
                        2 => 'Orta ixtisas',
                        3 => 'Ali',
                        4 => 'Tam ali',
                    ])
                    ->required()
                    ->label("Təhsili (orta ixtisas, ali, tam ali)"),

                ])->columns(3)->hidden(fn ($get) => $get('employeeType') != 1),
                Section::make("")->schema([
                   Select::make("serviceType")->options(
                    [
                       1 =>"Təlim",
                       2 =>"Məsləhət",
                       3 =>"Şəbəkələşmə"
                    ]
                   )
                   ->reactive()
                   ->label("Xidmətin növü"),
                   Select::make("training_id")
                   ->searchable()
                   ->options(function($get){
                    $isAdmin = auth()->user()->isAdmin();                    
                    $user_id = auth()->user()->id;

                    if($isAdmin){
                        $filteredOptions = Training::where('status', 3)
                        ->get()
                        ->mapWithKeys(function ($training) {
                            return [$training->id => $training->name];
                        })
                        ->toArray();
                    }
                    else{
                        $filteredOptions = Training::where('user_id', $user_id)
                        ->where('status', 3)
                        ->orWhere('executive_id',auth()->user()->id)
                        ->get()
                        ->mapWithKeys(function ($training) {
                            return [$training->id => $training->name];
                        })
                        ->toArray();
            
                    }
                    return $filteredOptions;
                   })
                   ->hidden(fn($get)=>$get('serviceType') != 1)
                   ->required()
                   ->label("Təlim xidməti seçin"),
                   Select::make("advice_id")
                   ->options([])
                   ->required()
                   ->hidden(fn ($get) =>$get("serviceType") !=2)
                   ->label("Məsləhət xidməti seçin"),
                   Select::make("networking_id")
                   ->options([])
                   ->required()
                   ->hidden(fn ($get) =>$get("serviceType") !=3)
                   ->label("Şəbəkələşmə xidmətini seçin"),
                ])->columns(2),
                Section::make("Əlaqə məlimatları")->schema([
                    TextInput::make("contactNumber")->numeric()->label("Əlaqə nömrəsi")->required(),
                    TextInput::make("contactEmail")->label("E-poçt adresi")->required(),
                    TextInput::make("signatureNumber")->label("Asan imza nömrəsi")
                ])->collapsible()->columns(3),
                Section::make("Fərdi ünvanı məlumatları")->schema([
                    Textinput::make('city')->label("Şəhər")->readOnly()->required(),
                    TextInput::make("address")->label("Ünvan")->readOnly()->required(),
                    Radio::make("actualResidentialAddress")->label("Yaşadığı yer və fakiti yaşayış ünvanı eynidi?")
                    ->options([
                        1=>"Beli",
                        0=>"Xeyr"
                     ])->reactive()->columnSpanFull()->default(1),
                     Section::make("Faktiki yaşadığı ünvan")->schema([
                        Select::make("actualCity")->label("Şəhər")
                        ->searchable()
                        ->options(function () {
                            $cityClient = app(CityClient::class);
                            return $cityClient->getCityOptions();
                        })->required(),
                        TextInput::make("actualAddress")->label("Ünvan")->required(),
                     ])->columns(2)->hidden(fn ($get) =>$get("actualResidentialAddress") !=0)
                        
                ])->collapsible()->columns(2),
                Section::make()->schema([
                   RichEditor::make("note")->label("Qeyd"),
                ]),
                Section::make("")->schema([
                  Select::make("status")->options([
                    0 => 'Yaradildi',
                    1 => 'Göndərildi',
                    2 => 'Təyin edilib',
                    3 => 'Planlanıb',
                    4 => 'İcra olundu',
                    5 => 'Ləğv edildi',
                    6 => 'İştirak etmədi'
                  ])
                  ->disabled(!auth()->user()->isAdmin())
                  ->default(0)
                  ->placeholder(false)
                //   ->required()
                  ])
                  ->hidden(function ($get) {
                    return  $get('id') == null;
                })
            ]);
    }

    public static function table(Table $table): Table
    {
        $statusOptions = [
            2 => 'Təyin edilib',
            3 => 'Planlanıb',
            4 => 'İcra olundu',
            5 => 'Ləğv edildi',
            6 => 'İştirak etmədi'
        ];

        return $table
            ->columns([
                TextColumn::make("applicationNumber")
                ->label("Müraciət nömrəsi")
                ->sortable('asc')
                ->searchable(),
                TextColumn::make('employeeType')
                ->label('Müraciət edən')
                ->toggleable(true,true)
                ->formatStateUsing(fn ($state) => $state === 0 ? 'Fiziki şəxs' : ($state === 1 ? 'Vergi ödəyicisi' : ''))
                ->sortable(),
                TextColumn::make("fullName")->label("Soyadı, adı, ata adı")
                ->toggleable()
                ->searchable()
                ->sortable(),
                TextColumn::make("education")
                ->label("Təhsili")
                ->toggleable(true,true)
                ->sortable()
                ->formatStateUsing(fn ($state) => $state == 1 ? 'Orta təhsil' : ($state == 2 ? 'Orta ixtisas' : ($state == 3 ? 'Ali' : 'Tam ali'))),
                TextColumn::make("contactNumber")
                ->label("Əlaqə nömrəsi")
                ->toggleable(true,true)
                ->searchable(),
                TextColumn::make("fieldActivity")
                ->toggleable()
                ->label("Fəaliyyət sahəsi")
                ->formatStateUsing(function ($state){
                    return ActivityAreaHelper::getActivityAreaNameByCode($state);
                }),
                TextColumn::make("city")
                ->toggleable(true,true)
                ->searchable()
                ->label("Şəhər")->toggleable()->sortable(),
                TextColumn::make("accepted_user_id")
                ->toggleable(true,true)
                ->formatStateUsing(
                    function($state){
                        $details = User::where('id',$state)->first();
                        if($details){
                            return $details->fullName;
                        }
                    }
                )->label("Müraciətə baxan əməkdaş")
                ->hidden((!auth()->user()->isKobimAdmin() && !auth()->user()->isAdmin()))
                ->searchable(),
                TextColumn::make("created_at")
                ->toggleable()
                ->label("Müraciət tarixi"),
                TextColumn::make("user.name")->label("Yaradan əməkdaş")->searchable()
                ->hidden(!auth()->user()->isSuperAdmin()),
                TextColumn::make("status")
                ->label('Status')
                ->formatStateUsing(fn ($state) => $statusOptions[$state] ?? 'Göndərilib')
                ->badge()
                ->color(function ($state) {
                    return match($state) {
                        0 => 'gray',
                        1 => 'info',
                        2 => 'warning',
                        3 => 'warning',
                        4 => 'success',
                        5=> 'danger',
                        6=> 'danger',
                        default => 'default'
                    };
                }),
                // SelectColumn::make("status")
                // ->options([
                //     0 => 'Yaradildi',
                //     1 => 'Göndərildi',
                //     2 => 'Təyin edilib',
                //     3 => 'Planlanıb',
                //     4 => 'İcra olundu',
                //     5 => 'Ləğv edildi',
                //     6 => 'İştirak etmədi'
                // ])
            ])
            ->filters([
                //
            ])
            ->actions([
                Action::make('send')
                ->label('')
                ->icon('heroicon-m-arrow-top-right-on-square')
                ->color("success")
                ->url(fn ($record): string => route('filament.admin.resources.tasks.create',))
                ->visible(fn ($record) => !$record->employeeTasks()->exists()),
                ActionGroup::make([
                    Action::make('updateStatus')
                    ->hidden(fn ($record) => auth()->user()->isAdmin() || in_array($record->status, [4,5,6]))
                    ->label('Statusunu dəyiş')
                    ->icon("heroicon-m-arrow-path")
                    ->form([
                        Textarea::make("otherNote")->readOnly()->label("Göndərən əməkdaşın notu"),
                        Select::make('status')
                            ->label('Status')
                            ->options($statusOptions)
                            ->reactive()
                            ->required(),
                        RichEditor::make("statusNote")->reactive()->live()->label("Qeyd")
                        ->required(fn ($get) =>  in_array($get('status'),[5,6])),
                    ])
                    ->mountUsing(function ($form, $record){
                        $otherNote = EmployeeTask::where('application_id',$record->id)->first();

                        $form->fill([
                            'status' => $record->status,
                            'statusNote' => $record->statusNote,
                            'otherNote' => $otherNote ? $otherNote->note : '',

                        ]);
                    })
                    ->action(function (array $data, Application $record) use ($statusOptions): void {
                        $record->status = $data['status'];
                        $record->statusNote = $data['statusNote'];
                        $record->save();

                        $statusName = $statusOptions[$data['status']] ?? 'Bilinməyən Status';

                        Notification::make()
                            ->title('Status müvəffəqiyətlə dəyiştirildi.')
                            ->body('Status '.'<b>'. $statusName.'</b>'.' olaraq təyin edildi')
                            ->icon('heroicon-o-check-circle')
                            ->success()
                            ->send();
                    }),
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
                    ExportBulkAction::make()

                ]),
            ]);
    }



    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    protected function getRedirectUrl(): ?string
    {
        return static::getResource()::getUrl('index');

    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
    
        if (auth()->user()->isAdmin()) {
            return $query;
        }
    
        return $query->where(function ($query) {
            $query->where('user_id', auth()->id()) 
                  ->orWhere('accepted_user_id', auth()->id());
        });
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListApplications::route('/'),
            'create' => Pages\CreateApplication::route('/create'),
            'edit' => Pages\EditApplication::route('/{record}/edit'),
            'view' => Pages\ViewApplication::route('/{record}'),

        ];
    }
}
