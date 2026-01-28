<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TaskResource\Pages;
use App\Filament\Resources\TaskResource\RelationManagers\CommentsRelationManager;
use App\Models\Task;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\Section as InfolistSection;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class TaskResource extends Resource
{
    protected static ?string $model = Task::class;

    protected static ?string $navigationIcon = 'heroicon-o-book-open';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Task Information')
                    ->columnSpan(8)
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->required()
                            ->columnSpanFull(),
                        Forms\Components\Select::make('status_id')
                            ->relationship('status', 'name')
                            ->native(false)
                            ->required(),
                        Forms\Components\Select::make('severity_id')
                            ->relationship('severity', 'name')
                            ->native(false)
                            ->required(),
                        Forms\Components\MarkdownEditor::make('description')
                            ->columnSpanFull(),
                    ]),
                Section::make('Task Details')
                    ->columnSpan([
                        'default' => 1,
                        'md' => 4,
                    ])
                    ->schema([

                        Forms\Components\Select::make('developer_id')
                            ->label('Assigned to')
                            ->relationship('developer', 'name')
                            ->searchable()
                            ->preload()
                            ->native(false)
                            ->required()
                            ->visible(fn () => auth()->check() && auth()->user()?->role === \App\Enums\UserRole::Admin),
                        Forms\Components\DatePicker::make('start_date'),
                        Forms\Components\DatePicker::make('due_date'),
                        Forms\Components\DatePicker::make('finish_date'),
                    ]),
            ])->columns(12);
    }

    public static function table(Table $table): Table
    {
        $table->modifyQueryUsing(function ($query) {
            if (auth()->check() && auth()->user()->role !== \App\Enums\UserRole::Admin) {
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
                    ->searchable(),
                Tables\Columns\TextColumn::make('status.name')
                    ->numeric()
                    ->sortable(),

                Tables\Columns\TextColumn::make('developer.name')
                    ->label('Assigned to')
                    ->numeric()
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
                //
            ])
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
                    ->columnSpan(8)
                    ->schema([
                        Grid::make()->schema([
                            TextEntry::make('title')
                                ->columnSpanFull(),
                            TextEntry::make('status.name')
                                ->badge()
                                ->color('primary'),
                            TextEntry::make('severity.name')
                                ->badge()
                                ->color(fn ($record) => $record->severity->color->value),
                            TextEntry::make('description')
                                ->formatStateUsing(fn (string $state): string => str($state)->markdown())
                                ->html()
                                ->extraAttributes([
                                    'class' => 'prose dark:prose-invert max-w-none',
                                ])
                                ->columnSpanFull(),
                        ]),
                    ]),
                InfolistSection::make('Task Details')
                    ->columnSpan(4)
                    ->schema([

                        TextEntry::make('developer.name')
                            ->label('Assigned to'),
                        TextEntry::make('start_date')
                            ->date(),
                        TextEntry::make('due_date')
                            ->date(),
                        TextEntry::make('finish_date')
                            ->date()
                            ->placeholder('Not finished'),
                    ]),
            ])->columns(12);
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
