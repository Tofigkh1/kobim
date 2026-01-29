<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AdviceResource\Pages;
use App\Filament\Resources\AdviceResource\RelationManagers;
use App\Models\Advice;
use App\Models\Application;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;


class AdviceResource extends Resource
{
    protected static ?string $model = Advice::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-group';


    protected static ?string $recordTitleAttribute = 'name';
    protected static ?string $navigationGroup = 'Xidmətlər Bölməsi';
    protected static ?string $modelLabel = 'Məsləhət ve dəstək';
    protected static ?string $pluralModelLabel = 'Məsləhət və dəstək';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('')->schema([
                    Forms\Components\TextInput::make('name')
                        ->label("Məsləhət və dəstək xidmətinin adı")
                        ->required()
                        ->maxLength(255)
                        ->default(null),
                    Forms\Components\DateTimePicker::make('dateTime')
                    ->required()
                    ->label("Tarix")
                    ->helperText("Məsləhət və dəstək xidmətinin göstərilmə tarixi")
                    ->native(false),
                ])->columns(2),
                Forms\Components\Section::make('')->schema([
                    Forms\Components\RichEditor::make('description')
                    ->label("Məsləhət xidmətinin istiqaməti")
                    ->helperText("Müqavilənin texniki tapşırığa əsasən Məsləhət xidmətinin istiqaməti")
                    ->maxLength(500),
                    Forms\Components\RichEditor::make('category')
                        ->label("Kateqoriyası")
                ])->columns(2),
                Forms\Components\Section::make('')->schema([
                    Forms\Components\TimePicker::make('duration')
                    ->time()
                    ->label("Müddəti/saat")
                    ->required()
                    ->native(false)
                    ->default(null),
                Forms\Components\Select::make('application_id')
                    ->required()
                    ->searchable()
                    ->options(function (){
                        $currentUser = auth()->user()->id;
                        $userIsSuperAdmin = auth()->user()->isSuperAdmin();

                        if($userIsSuperAdmin){
                             Application::where('user_id',$currentUser)->select('fullName','id')
                            ->get()
                            ->mapWithKeys(function($application){
                                $fullName = $application->fullName;
                                return[$application->id => $fullName];
                            });
                        }
                    })
                    ->label("KOB subyektinin adı"),
                    
                Forms\Components\Select::make('excpert_id')
                    ->label("Ekspertin adı, soyadı")
                    ->options(function () {
                        $currentUser = auth()->user();
                        $userIsSuperAdmin = auth()->user()->isSuperAdmin();
                        if($userIsSuperAdmin){
                            return User::where('visibility',1)->select('fullName','id')->get()->mapWithKeys(function($user){
                                $fullName = $user->fullName ?? ($user->name ?? 'Unkown User');
                                return[$user->id => $fullName];
                            });
                        }
                        else{
                            return User::where('visibility', 1)
                                ->where('user_id', $currentUser->id)
                                ->get()
                                ->mapWithKeys(function ($user) {
                                    $fullName = $user->fullName ?? ($user->name ?? 'Unknown User');
                                    return [$user->id => $fullName];
                                })
                                ->toArray();
                        }
                    })
                    ->searchable()
                    ->helperText("Əməkdaşlar arasından təyin edilir")
                    ->required()
                    ->default(null),
                    Forms\Components\TextInput::make('contactInfo')
                    ->label("KOB subyeti ilə Əlaqə")
                    ->maxLength(255)
                    ->default(null),
                ])->columns(4),
                Forms\Components\Section::make('')->schema([
                    Forms\Components\RichEditor::make('result')
                    ->maxLength(8000)
                    ->label('Əldə olunan nəticə')
                    ->columnSpanFull(),
                ]),
                Forms\Components\Section::make('')->schema([
                    Forms\Components\Select::make('status')
                    ->default(null)->columnSpanFull(),
                ])->aside()->hidden()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('dateTime')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('duration')
                    ->searchable(),
                Tables\Columns\TextColumn::make('application_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('excpert_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('contactInfo')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAdvice::route('/'),
            'create' => Pages\CreateAdvice::route('/create'),
            'edit' => Pages\EditAdvice::route('/{record}/edit'),
        ];
    }
}
