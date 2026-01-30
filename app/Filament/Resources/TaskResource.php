<?php

namespace App\Filament\Resources;

use App\Enums\UserRole;
use App\Filament\Resources\TaskResource\Pages;
use App\Filament\Resources\TaskResource\RelationManagers\CommentsRelationManager;
use App\Models\Status;
use App\Models\Task;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\Section as InfolistSection;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Support\Enums\MaxWidth;
use Filament\Tables;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class TaskResource extends Resource
{
    protected static ?string $model = Task::class;

    protected static ?string $navigationIcon = 'heroicon-o-book-open';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        $isDeveloper = auth()->check() && auth()->user()->role === UserRole::Developer;

        return $form
            ->disabled(fn ($record) => $isDeveloper && $record->status_id == Status::where('name', 'Closed')->first()->id)
            ->schema([
                Section::make('Task Information')
                    // ->columnSpan(8)
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->required()
                            ->columnSpanFull()
                            ->disabled($isDeveloper),
                        Forms\Components\Select::make('status_id')
                            ->relationship('status', 'name')
                            ->native(false)
                            ->required()
                            ->live()
                            ->options(function () use ($isDeveloper) {
                                if ($isDeveloper) {
                                    return Status::where('is_active', true)
                                        ->whereIn('name', ['In Progress', 'Completed'])
                                        ->pluck('name', 'id');
                                }

                                return Status::where('is_active', true)->get()->pluck('name', 'id');
                            })
                            ->afterStateUpdated(function (Forms\Set $set, $state) {
                                $completedStatus = Status::where('name', 'Completed')->first();
                                $closedStatus = Status::where('name', 'Closed')->first();

                                if ($state == $closedStatus?->id || $state == $completedStatus?->id) {
                                    $set('finish_date', now()->toDateString());
                                } else {
                                    $set('finish_date', null);
                                }
                            })
                            ->helperText(function () use ($isDeveloper) {
                                if ($isDeveloper) {
                                    return 'You can only change status to In Progress or Completed';
                                }

                                return null;
                            })
                            ->in(fn (Forms\Components\Select $component) => array_keys($component->getOptions())),
                        Forms\Components\Select::make('severity_id')
                            ->relationship('severity', 'name')
                            ->native(false)
                            ->required()
                            ->disabled($isDeveloper),
                        Forms\Components\MarkdownEditor::make('description')
                            ->columnSpanFull()
                            ->disabled($isDeveloper),
                    ]),
                Section::make('Task Details')
                    // ->columnSpan([
                    //     'default' => 1,
                    //     'md' => 4,
                    // ])
                    ->columns(2)
                    ->schema([
                        Forms\Components\Select::make('developer_id')
                            ->label('Assigned to')
                            ->relationship('developer', 'name')
                            ->searchable()
                            ->preload()
                            ->native(false)
                            ->required()
                            ->visible(fn () => auth()->check() && auth()->user()?->role === UserRole::Admin),
                        Forms\Components\DatePicker::make('start_date')
                            ->disabled($isDeveloper),
                        Forms\Components\DatePicker::make('due_date')
                            ->disabled($isDeveloper),
                        Forms\Components\DatePicker::make('finish_date')
                            ->helperText('Automatically filled when status is Completed')
                            ->readOnly($isDeveloper),
                    ]),
            ])->columns(12);
    }

    public static function table(Table $table): Table
    {
        $table->modifyQueryUsing(function ($query) {
            $query->with(['developer','creator']);
            if (auth()->check() && auth()->user()->role !== UserRole::Admin) {
                $query->where('developer_id', auth()->id());
            }
        });

        return $table
            ->columns([
                Tables\Columns\TextColumn::make('severity.name')
                    ->badge()
                    ->color(function ($record) {
                        return $record->severity->color->value;
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('title')
                    ->description(fn($record)=> auth()->user()->role == UserRole::Admin ? "Assigned to: {$record->developer->name}" : "")
                    ->searchable(query: function ($query, string $search) {
                        $query->where('title', 'like', "%{$search}%")
                            ->when(auth()->user()->role == UserRole::Admin, function ($q) use ($search){
                                $q->orWhereHas('developer', function ($q) use ($search) {
                                    $q->where('name', 'like', "%{$search}%");
                                });
                            });
                        }),
                Tables\Columns\TextColumn::make('status.name')
                    ->badge()
                    ->color(function ($record) {
                        return $record->status->color->value;
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('due_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('start_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('finish_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('creator.name')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
                Filter::make('created_at')
                ->columns(2)
                ->columnSpan(2)
                ->form([
                    DatePicker::make('created_from'),
                    DatePicker::make('created_until')
                        ->default(now()),
                ])
                ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    }),
                SelectFilter::make('severity_id')
                    ->label('Severity')
                    ->relationship('severity', 'name')
                    ->columnSpan(1),
            ], layout: FiltersLayout::AboveContentCollapsible)
            ->filtersFormWidth(MaxWidth::Large)
            ->filtersFormColumns(3)
          
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                InfolistSection::make('Task Information')
                    // ->columnSpan(8)
                    ->heading(function ($record) {
                        return $record->title;
                    })
                    ->schema([
                        Grid::make()->schema([
                            TextEntry::make('status.name')
                                ->badge()
                                ->inlineLabel()
                                ->color(fn ($record) => $record->status->color->value),
                            TextEntry::make('severity.name')
                                ->badge()
                                ->inlineLabel()
                                ->color(fn ($record) => $record->severity->color->value),
                            TextEntry::make('description')
                                ->label('')
                                ->formatStateUsing(fn (string $state): string => str($state)->markdown())
                                ->html()
                                ->extraAttributes([
                                    'class' => 'prose dark:prose-invert max-w-none',
                                ])
                                ->columnSpanFull(),
                        ]),
                    ]),
                InfolistSection::make('Details')
                    ->columns(2)
                    // ->columnSpan(4)
                    ->schema([

                        TextEntry::make('developer.name')
                            ->label('Assigned to')
                            ->badge(),
                        TextEntry::make('start_date')
                            ->date(),
                        TextEntry::make('due_date')
                            ->date(),
                        TextEntry::make('finish_date')
                            ->date()
                            ->placeholder('Not finished'),
                    ]),
            ]);
        // ->columns(12);
    }

    public static function getRelations(): array
    {
        return [
            CommentsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTasks::route('/'),
            'create' => Pages\CreateTask::route('/create'),
            'view' => Pages\ViewTask::route('/{record}'),
            'edit' => Pages\EditTask::route('/{record}/edit'),
        ];
    }
}
