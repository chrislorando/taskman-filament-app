<?php

namespace App\Filament\Resources\TaskResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Table;

class CommentsRelationManager extends RelationManager
{
    protected static string $relationship = 'comments';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\MarkdownEditor::make('body')
                    ->required()
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            // ->recordTitleAttribute('body')
            ->columns([
                Stack::make([
                    Tables\Columns\TextColumn::make('user.name')
                        ->description(fn ($record) => $record->created_at, 'below'),
                    Tables\Columns\TextColumn::make('body')
                        ->label('')
                        ->wrap()
                        ->formatStateUsing(fn (string $state): string => str($state)->markdown())
                        ->html()
                        ->extraAttributes([
                            'class' => 'prose dark:prose-invert ',
                        ])
                        ->columnSpanFull(),
                ]),

            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->disableCreateAnother()
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                // Tables\Actions\BulkActionGroup::make([
                //     Tables\Actions\DeleteBulkAction::make(),
                // ]),
            ]);
    }

    public function isReadOnly(): bool
    {
        $user = auth()->user();
        $task = $this->getOwnerRecord();
        $policy = app(\App\Policies\CommentPolicy::class);

        return ! $policy->createOnTask($user, $task);
    }
}
