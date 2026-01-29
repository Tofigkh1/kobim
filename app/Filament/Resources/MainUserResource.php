<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MainUserResource\Pages;
use App\Filament\Resources\MainUserResource\RelationManagers;
use App\Helpers\CityHelper;
use App\Models\MainUser;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Spatie\Permission\Models\Permission;

class MainUserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-circle';

    protected static ?string $modelLabel = 'Admin';
    protected static ?string $pluralModelLabel = 'Adminlər';
    protected static ?string $navigationGroup = 'Mühafizə';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make("Panelə giriş məlumatları")->schema([
                    Hidden::make("visibility")->default(0),
                    Section::make("")->schema([
                        TextInput::make("name")->label("İstifadəçi adı")->required(),
                        TextInput::make("fullName")->label("Ad Soyad")->required(),
                        TextInput::make("email")->label("İstifadəçinin email adresi")->required()->unique(ignoreRecord: true),
                        TextInput::make("password")->label("İstifadəçinin şifrəsi")->required()->password()->revealable(),
                    ])->columns(3),
                    Section::make()->schema([
                        DateTimePicker::make("email_verified_at")->label("İcazə tarixi"),
                            Select::make('roles')
                            ->relationship('roles', 'name')
                            ->multiple()
                            ->preload()
                            ->label('Rol')
                            ->searchable()
                            ->disabled(fn() => !auth()->user()->isSuperAdmin())
                    ])->columns(2)
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                
                TextColumn::make("name")->label("İstifadəçi adı"),
                TextColumn::make("email")->label("Email adresi")->copyable()->copyMessage('Email adres kopyalandı')->searchable()->sortable(),
                TextColumn::make('roles.name')
                ->toggleable()
                ->label('Rolu')
                ->sortable()
                ->badge(),
                TextColumn::make("city")
                ->formatStateUsing(function ($state) {
                    return CityHelper::getCityNameById($state);
                }),
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


    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        
        if (auth()->user()->isAdmin()) {
            return $query->where('visibility', 0);
        }
        
        return $query->where('user_id', auth()->id())->where('visibility', 0);
    }
    

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function canViewAny(): bool
    {
        if(auth()->user() && auth()->user()->isSuperAdmin()){
            return true;
        }
        return false;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMainUsers::route('/'),
            'create' => Pages\CreateMainUser::route('/create'),
            'edit' => Pages\EditMainUser::route('/{record}/edit'),
            'view' => Pages\ViewMainUser::route('/{record}'),
        ];
    }
}
