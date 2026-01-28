<?php

namespace App\Filament\Resources;

use App\Enums\SeverityColor;
use App\Filament\Resources\SeverityResource\Pages;
use App\Models\Severity;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Support\Enums\MaxWidth;
use Filament\Tables;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Table;

class SeverityResource extends Resource
{
    protected static ?string $model = Severity::class;

    protected static ?string $navigationIcon = 'heroicon-o-exclamation-triangle';

    protected static ?string $navigationGroup = 'System';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->columns(1)
            ->schema([
                Forms\Components\TextInput::make('sort_order')
                    ->required()
                    ->numeric()
                    ->default(0),
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->unique(),
                Forms\Components\ToggleButtons::make('color')
                    ->options(SeverityColor::class)
                    ->inline(),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('sort_order')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('color')
                    ->badge()
                    ->searchable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()->modalWidth(MaxWidth::Large),
                Tables\Actions\DeleteAction::make()
                    ->before(function (DeleteAction $action, Severity $record) {
                        if ($record->tasks()->exists()) {
                            Notification::make()
                                ->danger()
                                ->title('Deletion Failed')
                                ->body('This severity cannot be deleted because it is currently assigned to tasks.')
                                ->persistent()
                                ->send();

                            $action->halt();
                        }
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageSeverities::route('/'),
        ];
    }
}
