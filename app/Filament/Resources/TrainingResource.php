<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TrainingResource\Pages;
use App\Models\Training;
use App\Models\User;
use App\Services\IncludeBusiness;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Form;
use Filament\Infolists\Components\Tabs;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\TextColumn\TextColumnSize;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use PhpOffice\PhpSpreadsheet\RichText\RichText;

class TrainingResource extends Resource
{
    protected static ?string $model = Training::class;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

    
    protected static ?string $recordTitleAttribute = 'name';
    protected static ?string $navigationGroup = 'Xidmətlər Bölməsi';
    protected static ?string $modelLabel = 'Təlim';
    protected static ?string $pluralModelLabel = 'Təlimlər';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                    Section::make("")->schema([
                        TextInput::make("name")->label("Təlimin adı")->required()->columnSpanFull(),
                    ])->columns(2),
                    Section::make("")->schema([
                        FileUpload::make("photo")
                        ->directory('uploads/telim')
                        ->image()
                        ->label("Şəkil")
                    ]),
                    Section::make("")->schema([
                        DatePicker::make("date")->label("Tarixi")->required()->native(false),
                        TimePicker::make("hour")->label("Saatı")->required()->native(false),
                        TimePicker::make("duration")->label("Müddət")->required()->native(false),
                        RichEditor::make("note")->label("Xidmət haqqında ilkin məlumat")->columnSpanFull()
                    ])->columns(3),
                    Section::make("")->schema([
                       Select::make("serviceType")
                       ->options([
                          0=>"Online",
                          1=>"Fiziki"

                       ])
                       ->required()
                       ->reactive()
                       ->label("Keçirilmə forması"),
                       Select::make("scope")->options([
                           0=>"Koporativ şirkət əməkdaşları",
                           1=>"Sahibkarlıq subyektləri",
                           2=>"Sahibkar olmaq istəyən şəxslər",
                       ])->multiple()->label("Əhatə dairəsi")->required(),
                       Select::make("executive_id")
                       ->options(function () {
                        $currentUser = auth()->user();
                        $superAdmin = auth()->user()->isSuperAdmin();
                        if($superAdmin){
                            return User::where('visibility', 1)
                            ->get()
                            ->mapWithKeys(function ($user) {
                                $fullName = $user->fullName ?? ($user->name ?? 'Unknown User');
                                return [$user->id => $fullName];
                            })
                            ->toArray();
                        }
                        return User::where('visibility', 1)
                            ->where('user_id', $currentUser->id)
                            ->get()
                            ->mapWithKeys(function ($user) {
                                $fullName = $user->fullName ?? ($user->name ?? 'Unknown User');
                                return [$user->id => $fullName];
                            })
                            ->toArray();
                        })
                       ->label("İcraçı"),

                       Select::make("includesBusiness")
                       ->required()
                       ->multiple()
                       ->options(
                        function (){
                            $area = app(IncludeBusiness::class);
                            return $area->getBusinessStageOptions();
                        }
                       )
                       ->label("Hansı biznes mərhələlərin ehtiva edir?"),
                       TextInput::make("address")->label("Keçiriləcəyi ünvan")->hidden(fn ($get) => $get('serviceType') != 1)->required(),
                       TextInput::make("link")->label("Online qoşulacağı link")->hidden(fn ($get) => $get('serviceType') != 0)->required(),
                       Hidden::make("status")->default(1)
                       ])->columns(3),
                       Section::make("")->schema([
                        Radio::make("certificate")->options([
                            1=>"Bəli",
                            0=>"Xeyr"
                        ])
                        ->required()
                        ->label("Sonunda sertifikat verilir?"),
                        Select::make("status")->options([
                            1 => 'Göndərildi',
                            2 => 'Təyin edilib',
                            3 => 'Planlanıb',
                            4 => 'İcra olundu',
                            5 => 'Ləğv edildi',
                        ])->hidden(fn ($record) => $record == null)->disabled(!auth()->user()->isAdmin()),
                        Section::make("")->schema([
                            RichEditor::make("haveSkills")->label("İştirakçı bu xidmətdən yararlandıqdan sonra hansı bacarıqlara sahib olacaq?"),
                            RichEditor::make("orderNote")->label("Qeyd")
                        ]),
                        RichEditor::make("statusNote")->label("Əməkdaşın qeydi")->columnSpanFull()
                        ->hidden(fn ($get) => $get('statusNote') === null)
                        ->disabled()
                    ])->columns(2),

                    Section::make("")->schema([
                        Repeater::make('sessions')
                          ->label("Sessia")
                          ->schema([
                              TextInput::make('name')->label("Sessianın adı")->required()->columnSpanFull(),
                              RichEditor::make("sessionContent")->label("Sessianın tərkibi")->required()->columnSpanFull()
          
                          ])
                          ->addActionLabel('Yeni sessia əlavə et')
                          ->collapsible()
                          ->maxItems(20)
                          ->columns(2)
                          ->itemLabel(fn (array $state): ?string => $state['name'] ?? null),
                    ])

                    


            ]);
    }

    public static function table(Table $table): Table
    {
        $statusOptions = [
            2 => 'Təyin edilib',
            3 => 'Planlanıb',
            4 => 'İcra olundu',
            5 => 'Ləğv edildi',
        ];

        return $table
            // ->recordUrl(null)
            ->columns([
                TextColumn::make('name')->label('Təlimin adı')
                ->placeholder('Qeyd olunmayıb!')
                ->searchable()
                ->tooltip(fn($record) => $record->name) // Tooltip için doğrudan erişim
                ->sortable(),
                TextColumn::make('date')->label('Tarixi')
                ->weight(FontWeight::Thin)
                ->size(TextColumnSize::ExtraSmall)
                ->placeholder('Qeyd olunmayıb!')
                ->toggleable()->sortable(),
                TextColumn::make('hour')
                ->placeholder('Qeyd olunmayıb!')
                ->label('Saatı')->toggleable(true,true)->sortable(),
                TextColumn::make('duration')
                ->placeholder('Qeyd olunmayıb!')
                ->label('Müddət')->toggleable(true,true)->sortable(),
                TextColumn::make("executive_id")
                ->alignment(Alignment::Center)
                ->searchable()
                ->placeholder('Qeyd olunmayıb!')
                ->sortable()
                ->toggleable(true,true)
                ->formatStateUsing(
                    function($state){
                        $details = User::where('id',$state)->first();
                        if($details){
                            return $details->fullName;
                        }
                    }
                )->label("İcraçı"),
                TextColumn::make('serviceType')
                    ->size(TextColumnSize::ExtraSmall)
                    ->alignment(Alignment::Center)
                    ->toggleable()
                    ->placeholder('Qeyd olunmayıb!')
                    ->label('Keçirilmə forması')
                    ->sortable()
                    ->badge()
                    ->color(fn ($state) => $state == 0 ? 'gray' : 'info')
                    ->formatStateUsing(fn ($state) => $state == 0 ? 'Online' : 'Fiziki'),     
                TextColumn::make("scope")
                ->sortable()
                ->placeholder('Qeyd olunmayıb!')
                ->label("Əhatə dairəsi")
                ->badge()
                ->toggleable(true,true)
                ->formatStateUsing(function ($state) {
                    $options = [
                        '0' => 'Korporativ şirkət əməkdaşları',
                        '1' => 'Sahibkarlıq subyektləri',
                        '2' => 'Sahibkar olmaq istəyən şəxslər'
                    ];
            
                    $stateArray = explode(',', $state);
            
                    return implode(', ', array_map(function ($item) use ($options) {
                        return $options[trim($item)] ?? '';
                    }, $stateArray));
                }),
                TextColumn::make('address')
                ->placeholder('Qeyd olunmayıb!')
                ->searchable()
                ->sortable()
                ->formatStateUsing(fn($state)=>$state? $state:"Qeyd olunmayıb")
                ->toggleable(true,true)
                ->label('Keçiriləcəyi ünvan'),
                TextColumn::make('link')
                ->placeholder('Qeyd olunmayıb!')
                ->searchable()
                ->toggleable(true,true)
                ->label('Online qoşulacağı link'),
                TextColumn::make('certificate')
                    ->placeholder('Qeyd olunmayıb!')
                    ->label('Sertifikat')
                    ->sortable()
                    ->toggleable(true,true)
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state ? 'Verilir' : 'Verilmir'),
                    TextColumn::make("status")
                    ->placeholder('Qeyd olunmayıb!')
                    ->sortable()
                    ->label("Status")
                    ->badge()
                    ->formatStateUsing(function ($state) {
                        return match($state) {
                            0 => 'Yaradildi',
                            1 => 'Göndərildi',
                            2 => 'Təyin edilib',
                            3 => 'Planlanıb',
                            4 => 'İcra olundu',
                            5 => 'Ləğv edildi',
                            default => 'Bilinməyən'
                        };
                    })
                    ->color(function ($state) {
                        return match($state) {
                            0 => 'gray',
                            1 => 'info',
                            2 => 'warning',
                            3 => 'warning',
                            4 => 'success',
                            5=> 'danger',
                            default => 'default'
                        };
                    }),
                    TextColumn::make("created_at")->label("Yaradılma tarixi")->toggleable(true,true),               
            ])
            ->filters([
                SelectFilter::make('status')
                ->placeholder('Qeyd olunmayıb!')
                ->label("Təlimin statusana görə filtrələ")
                ->options([
                    1 => 'Göndərildi',
                    2 => 'Təyin edilib',
                    3 => 'Planlanıb',
                    4 => 'İcra olundu',
                    5 => 'Ləğv edildi',
                ])
            ])
            ->actions([
                Tables\Actions\Action::make('list')
                ->tooltip("İştirak edən və etməyənlərin siyahısı")
                ->label('Siyahı')
                ->icon('heroicon-m-clipboard-document')
                ->color('success')
                ->modalSubmitAction(false)            //Remove Submit Button
                ->hidden(fn($record) => $record->status != 4) // Status 4 deyilsə görünməsin 4 icra olunub statusudur
                ->infolist([
                    Tabs::make('Təlim')
                    ->tabs([
                        Tabs\Tab::make('İştirak edənlər')
                            ->schema([
                                TextEntry::make("applicationsAccepted.fullName")
                                ->label("Siyahı")
                                ->listWithLineBreaks()
                                ->bulleted()
                                ,
                            ]),
                        Tabs\Tab::make('İştirak etməyənlər')
                            ->schema([
                                TextEntry::make("applicationsCancelled.fullName")
                                ->label("Siyahı")
                                ->listWithLineBreaks()
                                ->bulleted()
                                ,
                            ]),
                    ])

                ])
                ->slideOver(),
                // Müraciət edənlərin siyahısıdır
                Tables\Actions\Action::make('listSent')
                ->tooltip("Müraciət edənlərin siyahısı")
                ->label('Siyahı')
                ->icon('heroicon-m-clipboard-document')
                ->color('primary')
                ->modalSubmitAction(false)            //Remove Submit Button
                ->hidden(fn($record) => $record->status != 3) // Status 3 deyilsə görünməsin 3 planlanıb statusudur
                ->infolist([
                    Tabs::make('Təlim')
                    ->tabs([
                        Tabs\Tab::make('Müraciət edənlər')
                            ->schema([
                                TextEntry::make("applicationsSent.fullName")
                                ->placeholder("Hələki heç bir müraciət daxil olmayıb")
                                ->label("Siyahı")
                                ->listWithLineBreaks()
                                ->bulleted()
                                ,
                            ]),
                    ])

                ])
                ->slideOver(),
               Action::make("sessionAction")
               ->hidden(!auth()->user()->isAdmin())
               ->icon("heroicon-m-queue-list")
               ->label("Sessialar")
               ->color('danger')
               ->form([
                Repeater::make('sessions')
                ->label("Sessia")
                ->schema([
                    TextInput::make('name')->label("Sessianın adı")->required()->columnSpanFull(),
                    RichEditor::make("sessionContent")->label("Sessianın tərkibi")->required()->columnSpanFull()

                ])
                ->addActionLabel('Yeni sessia əlavə et')
                ->collapsible()
                ->maxItems(20)
                ->columns(2)
                ->itemLabel(fn (array $state): ?string => $state['name'] ?? null),
               ])
               ->mountUsing(function ($form, $record){
                $form->fill([
                    'sessions' => $record->sessions,
                ]);
                })
                ->action(function (array $data, Training $record): void {

                    $record->sessions = $data['sessions'];
                    $record->save();

                    Notification::make()
                        ->title('Sessia müvəffəqiyətlə əlavə edildi.')
                        ->icon('heroicon-o-check-circle')
                        ->success()
                        ->send();
                }),
                Tables\Actions\ActionGroup::make([
                    Action::make('updateStatus')
                    ->hidden(fn ($record) => auth()->user()->isAdmin() || in_array($record->status, [4, 5]))
                    ->label('Statusunu dəyiş')
                    ->icon("heroicon-m-arrow-path")
                    ->form([
                        RichEditor::make("orderNote")->label("Göndərən əməkdaşın notu")->disabled(),
         
                        Select::make('status')
                            ->label('Status')
                            ->options($statusOptions)
                            ->required(),
                        RichEditor::make("statusNote")->label("Qeyd"), 
                    ])
                    ->mountUsing(function ($form, $record){
                        $form->fill([
                            'status' => $record->status,
                            'statusNote' => $record->statusNote,
                            'orderNote' => $record->orderNote
                        ]);
                    })
                    ->action(function (array $data, Training $record) use ($statusOptions): void {
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
                    ])
                    ->tooltip("Əlavə parametirlər")
                    ->icon('heroicon-m-cog-8-tooth'),
            ])
            ->defaultSort('created_at','desc')
            ->recordAction(null)
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }


    // public static function infoList(Infolist $infolist): Infolist
    // {
    //     return $infolist
    //         ->schema([
                    
    //             ]);
    // }

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
    
        return $query->where(function ($query) {
            $query->where('user_id', auth()->id())
                  ->orWhere('executive_id', auth()->id());
        });
    }
    
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTrainings::route('/'),
            'create' => Pages\CreateTraining::route('/create'),
            'edit' => Pages\EditTraining::route('/{record}/edit'),
            'view' => Pages\ViewTraining::route('/{record}'),
        ];
    }
}
