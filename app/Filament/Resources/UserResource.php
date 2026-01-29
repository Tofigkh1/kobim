<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\SmeInformation;
use App\Models\User;
use App\Services\ApiClient;
use App\Services\CityClient;
use BezhanSalleh\FilamentShield\Traits\HasPanelShield;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Rawilk\FilamentPasswordInput\Password;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Traits\HasRoles;

class UserResource extends Resource
{
    use HasRoles;
    use HasPanelShield;

    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $recordTitleAttribute = 'name';
    protected static ?string $navigationGroup = 'KOBİM Bölməsi';
    protected static ?string $modelLabel = 'KOBİM əməkdaşı';
    protected static ?string $pluralModelLabel = 'KOBİM əməkdaşları';







    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make("Panelə giriş məlumatları")->schema([
                Section::make("")->schema([
                    Hidden::make("visibility")->default(1),
                    TextInput::make("name")->label("İstifadəçi adı")->required(!auth()->user()->isSuperAdmin()),
                    TextInput::make("email")->label("İstifadəçinin email adresi")->required()->unique(ignoreRecord: true),
                    Password::make("password")->label("İstifadəçinin şifrəsi")
                    ->required()
                    ->copyable()
                    ->copyMessage('Şirfə kopyalandı')
                    ->regeneratePassword()
                    ->maxLength(8)
                    ,
                    Select::make('roles')
                    ->relationship('roles', 'name')
                    ->multiple()
                    ->preload()
                    ->label('Rol')
                    ->searchable()
                    ->default(1)
                    ->placeholder(false)
                    ->options(function () {
                        $currentUser = auth()->user();
                        if ($currentUser->isSuperAdmin()) {
                            return Role::pluck('name', 'id');
                        }
                        return Role::where('name', 'Əməkdaş')->pluck('name', 'id');
                    })
                ])->columns(4),
                TextInput::make('user_id')
                ->label('Yaradan admin')
                ->formatStateUsing(function ($state) {
                    $user = User::find($state);
                    return $user ? $user->name.'  ('.$user->roles[0]->name.")" : null;
                })
                ->hidden(fn() => !auth()->user()->isAdmin())
                ->disabled()
                ]),
               Section::make("Fərdi məlumatlar")->schema([
                    TextInput::make('fin')
                    ->label('Fin kod')
                    ->reactive()
                    ->required(!auth()->user()->isSuperAdmin())
                    ->minLength(7)
                    ->maxLength(7)
                    ->live()
                    ->afterStateUpdated(function (HasForms $livewire, TextInput $component) {
                        $livewire->validateOnly($component->getStatePath());
                        
                        $state = $component->getState();
                        $set = $component->getSetCallback();
                        
                        function clearFinDetails(callable $set) {
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
                TextInput::make("fullName")->label("Soyadı, adı, ata adı")
                ->readOnly(!auth()->user()->isSuperAdmin())
                ->live()
                ->required(!auth()->user()->isSuperAdmin())
                ->reactive(),
                  Select::make("education")
                  ->options([
                      1 => 'Orta təhsil',
                      2 => 'Orta ixtisas',
                      3 => 'Ali',
                      4 => 'Tam ali',
                  ])
                  ->label("Təhsili (orta ixtisas, ali, tam ali)"),
                  Select::make("sme_id")
                  ->options(function () {
                      $user = auth()->user();
                      
                      if ($user->isAdmin()) {
                          return SmeInformation::all()
                              ->mapWithKeys(function ($sme) {
                                  $fullName = $sme->smeName ?? 'Unknown SME';
                                  return [$sme->id => $fullName];
                              })
                              ->toArray();
                      } else {

                          $sme = SmeInformation::find($user->sme_id);
                          if ($sme) {
                              $fullName = $sme->smeName ?? 'Unknown SME';
                              return [$sme->id => $fullName];
                          }
                      }
                      
                      return [];
                  })
                  ->selectablePlaceholder(fn() => auth()->user()->isAdmin())
                  ->live()
                  ->disabled(fn() => !auth()->user()->isAdmin())
                  ->default(1)
                  ->required(!auth()->user()->isSuperAdmin())
                  ->label("Təyin Olunacağı KOBİM"),
               ])->columns(4),
               Section::make("İş yeri barədə məlumat")->schema([
                   MarkdownEditor::make("mainWorkplace")->label("Əsas iş yeri"),
                   MarkdownEditor::make("duty")->label("Vəzifəsi")

               ])->columns(2),
               Section::make("Əlaqə məlimatları")->schema([
                   TextInput::make("contactNumber")->label("Əlaqə nömrəsi"),
                   TextInput::make("contactEmail")->label("E-poçt adresi")

               ])->columns(2),
               Section::make("Ünvanı məlumatları")->schema([
                Select::make("city")->label("Şəhər")
                ->searchable()
                ->options(function () {
                    $cityClient = app(CityClient::class);
                    return $cityClient->getCityOptions();
                })->required(!auth()->user()->isSuperAdmin()),
                TextInput::make("address")->label("Ünvan")
               ])->columns(2),
            //    Section::make("Xidmətlər")->schema([
            //     DatePicker::make("date")->label("Tarix"),
            //     TextInput::make("serviceName")->label("Xidmətin adı"),
            //     TextInput::make("duration")->label("Müddət"),
            //     Select::make("serviceForm")->options([
            //         0 => "Fiziki",
            //         1 =>"Online"  
            //     ])->label("Xidmət forması"),
            //     Select::make("status")->options([
            //         0 =>"Planlanıb",
            //         1 =>"İcra edilib"
            //     ])->label("Statusu"),
            //     TextInput::make("serviceType")->label("Xidmət növü")
            // ])->columns(3),

            Section::make()->schema([
                RichEditor::make("note")->label("Qeyd"),
            ]),
            Section::make("")->schema([
                FileUpload::make("trailer")->label("Qoşma")->helperText("Qoşma maksimum 5 mb ola bilər")->maxSize(5120),
                FileUpload::make("photo")->label("Şəkil")->helperText("Şəkil maksimum 2 mb ola bilər")->image()->imageEditor()->downloadable()->maxSize(2048)

            ])->columns(2)
               
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make("fullName")->label("Əməkdaş"),
                TextColumn::make("name")->label("İstifadəçi adı")->toggleable(true,true),
                ImageColumn::make("photo")->label("Profil şəkli")->toggleable(true,true),
                TextColumn::make("email")->label("Email adresi")->copyable()->copyMessage('Email adres kopyalandı')->searchable()->sortable(),
                TextColumn::make('roles.name')
                ->toggleable()
                ->label('Rolu')
                ->sortable()
                ->badge(),
                TextColumn::make('created_at')
                ->toggleable()
                ->label("Yaradıldı")
                ->since()->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
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
        $superAdminUserIds = User::role('super_admin')->pluck('id');
        
        if (auth()->user()->isSuperAdmin()) {
            return $query->where('visibility', 1);
        }

        else if(auth()->user()->isAdmin()){
            return $query->whereNotIn("id",$superAdminUserIds)->where('visibility',1);
        }
        else{
            return $query->where('user_id', auth()->id())->where('visibility', 1);
            
        }
        
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
            'view' => Pages\ViewUser::route('/{record}'),
        ];
    }
}
