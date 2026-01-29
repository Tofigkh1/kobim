<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TaskResource\Pages;
use App\Models\Application;
use App\Models\EmployeeTask;
use App\Models\Training;
use App\Models\User;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Actions\ActionGroup;

class TaskResource extends Resource
{
    protected static ?string $model = EmployeeTask::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document';

    protected static ?string $modelLabel = 'Yeni tapşırıq';
    protected static ?string $pluralModelLabel = 'Tapşırıqlar';
    protected static ?string $navigationGroup = 'Müraciətlər Bölməsi';

    public static function form(Form $form): Form
    {
        return $form
        ->schema([
            self::applicationSelect(),
            self::serviceFormSelect(),
            self::trainingSelect(),
            self::networkSelect(),
            self::userIdHidden(), 
            self::dateTimePicker(),
            self::statusNoteEmployee(),
            self::serviceTypeSelect(),
            MarkdownEditor::make("note")->label("Qeyd")->columnSpanFull(),
        ]);
    }

    private static function applicationSelect(): Select
    {
        return Select::make('application_id')
            ->label('Müraciətin nömrəsi')
            ->relationship("application", "applicationNumber")
            ->options(fn ($get) => self::getApplicationOptions($get))
            ->afterStateUpdated(fn (Set $set, ?string $state) => self::updateApplicationState($set, $state))
            ->required()
            ->reactive();
    }

    
    private static function serviceFormSelect(): Select
    {
        return Select::make("serviceForm")
            ->label("Müraciət olunan xidmət")
            ->options([
                "telim" => "Təlim",
                "sebekelesme" => "Şəbəkələşmə",
            ])
            ->reactive()
            ->required();
    }

    private static function userIdHidden(): Hidden
    {
        return Hidden::make("user_id")
            ->reactive();
    }

    private static function trainingSelect(): Select
    {
        return Select::make('training_id')
            ->label("Təlimi seçin")
            ->options(fn () => self::getTrainingOptions())
            ->reactive()
            ->afterStateUpdated(fn (Set $set, ?string $state) => self::updateUserIdFromTraining($set, $state))
            ->visible(fn ($get) => $get('serviceForm') === "telim")
            ->required();
    }


    private static function updateUserIdFromTraining(Set $set, ?string $trainingId): void
    {
        if (!$trainingId) {
            $set('user_id', null);
            return;
        }

        
        $training = Training::find($trainingId);
        $set('user_id', $training?->executive_id ?? null);
    }


    private static function networkSelect(): Select
    {
        return Select::make('network_id')
            ->label("Şəbəkəni seçin")
            ->options(fn () => self::getNetworkOptions())
            ->reactive()
            ->visible(fn ($get) => $get('serviceForm') === "sebekelesme") 
            ->required();
    }

    private static function getNetworkOptions(): array
    {
        return [
            1 => "Şəbəkə 1",
            2 => "Şəbəkə 2",
            3 => "Şəbəkə 3",
        ];
    }
    private static function dateTimePicker(): DateTimePicker
    {
        return DateTimePicker::make("created_at")
            ->label("Tapşırığın verilmə tarixi")
            ->live()
            ->reactive()
            ->native(false)
            ->disabled()
            ->locale('az')
            ->hidden(fn ($get) => $get('created_at') === null);
    }


    private static function statusNoteEmployee(): RichEditor
    {
        return RichEditor::make("statusNoteEmployee")
            ->label("Əməkdaşın notu")
            ->disabled()
            ->hidden(fn (?EmployeeTask $record) => !$record?->application?->statusNote)
            ->formatStateUsing(fn (?EmployeeTask $record) => $record?->application?->statusNote ?? 'Qeyd olunmayıb!');
    }

    private static function getApplicationOptions($get): array
    {
        $currentUser = auth()->user();
        $userIsSuperAdmin = $currentUser->isSuperAdmin();

        $applicationsQuery = Application::with('user');
        if (!$userIsSuperAdmin) {
            $applicationsQuery->where('accepted_user_id', $currentUser->id);
        }

        return $applicationsQuery->get()
            ->filter(fn ($application) => $get('application_id') == $application->id || ($application->employeeTasks && $application->employeeTasks->isEmpty()))
            ->mapWithKeys(fn ($application) => [$application->id => "{$application->applicationNumber} - " . " {$application->fullName}"])
            ->toArray();
    }



    private static function serviceTypeSelect(): Select
    {
        return Select::make("serviceType")
            ->label("Xidmət forması")
            ->options([
                1 => "Fiziki",
                2 => "Məsafədən"
            ])
            ->required();
    }

    private static function getTrainingOptions(): array
    {
        return Training::where('status', 3)
            ->where(function ($query) {
                if (!auth()->user()->isSuperAdmin()) {
                    $query->where('user_id', auth()->id());
                }
            })
            ->get()
            ->mapWithKeys(fn ($training) => [$training->id => $training->name ?? 'Qeyd Olunmayıb'])
            ->toArray();
    }

    private static function updateApplicationState(Set $set, ?string $state): void
    {
        if (!$state) {
            $set('date', '');
            $set('serviceForm', '');
            return;
        }

        $details = Application::find($state);
        $set('date', $details->created_at->format('d.m.Y H:i'));
        $set('statusu', $details->status);
        $set('statusNote', $details->statusNote);
        $set('serviceForm', $details->applicationType === "F" ? "Müraciət fiziki şəkildə yaradılıb" : "Müraciət online daxil olub");
    }


    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make("application.applicationNumber")->label("Müraciətin nömrəsi")->searchable(),
                TextColumn::make("user.fullName")->label("Müraciətə baxan əməkdaş")->searchable(),
                TextColumn::make("application.fullName")->label("Müraciət edən")->searchable(),
                self::statusColumn(),
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([
                ActionGroup::make([
                    Tables\Actions\ReplicateAction::make(),
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ])->icon('heroicon-m-cog-8-tooth'),
            ]);
    }

    private static function statusColumn(): TextColumn
    {
        return TextColumn::make("application.status")
            ->label("Status")
            ->badge()
            ->formatStateUsing(fn ($state) => EmployeeTask::getStatusLabel($state))
            ->color(fn ($state) => EmployeeTask::getStatusColor($state));
    }


    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
    
        if (!auth()->user()->isSuperAdmin()) {
            $query->where('user_id', auth()->id());
        }
    
        return $query;
    }
   
   
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTasks::route('/'),
            'create' => Pages\CreateTask::route('/create'),
            'edit' => Pages\EditTask::route('/{record}/edit'),
            'view' => Pages\ViewTask::route('/{record}'),
        ];
    }
}
