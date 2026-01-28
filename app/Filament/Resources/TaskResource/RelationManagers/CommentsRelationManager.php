<?php

namespace App\Filament\Resources\TaskResource\RelationManagers;

use App\Models\Comment;
use App\Models\Task;
use Filament\Actions\ActionGroup;
use Filament\Forms;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Support\Enums\MaxWidth;
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
        $table->modifyQueryUsing(function ($query) {
            $query->orderByRaw('COALESCE(parent_id, id), parent_id IS NOT NULL, id');
        });
        return $table
            // ->recordTitleAttribute('body')
            ->striped()
            ->columns([
                Stack::make([
                    Tables\Columns\Layout\View::make('filament.tables.comment-row'),
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
                \Filament\Tables\Actions\ActionGroup::make([
                    Tables\Actions\Action::make('Reply')
                        ->icon('heroicon-o-arrow-uturn-left')
                        ->color('gray')
                        ->fillForm(fn (Comment $record): array => [
                            'parent_id' => $record->id,
                        ])
                         ->form([
                                Placeholder::make('')
                                    ->content(fn ($record) => new \Illuminate\Support\HtmlString("
                                        <div class='prose dark:prose-invert max-w-none'>
                                            " . str($record->body)->markdown() . "
                                        </div>
                                    ")),
                                MarkdownEditor::make('body')
                                    ->required(),
                            ])
                            ->action(function (array $data, Comment $record): void {
                                $task = $this->getOwnerRecord();
                                $record->replies()->create([
                                    'body' => $data['body'],
                                    'task_id' => $task->id, 
                                    'user_id' => auth()->id(),
                                ]);
                            })
                            ->slideOver()
                            ->modalWidth(MaxWidth::ExtraLarge),
                            
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ])

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
