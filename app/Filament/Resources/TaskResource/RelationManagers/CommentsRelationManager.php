<?php

namespace App\Filament\Resources\TaskResource\RelationManagers;

use App\Mail\NewCommentMail;
use App\Models\Comment;
use Filament\Forms;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Support\Enums\MaxWidth;
use Filament\Tables;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Mail;

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
            ->poll()
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
                    ->after(function (array $data, Comment $record) {
                        $task = $record->task;
                        $recipients = collect();

                        if ($task->developer && $task->developer->id !== $record->user_id) {
                            $recipients->push($task->developer);
                        }

                        if ($task->creator && $task->creator->id !== $record->user_id) {
                            $recipients->push($task->creator);
                        }

                        $recipients = $recipients->unique('id');

                        foreach ($recipients as $recipient) {
                            Mail::to($recipient)->send(new NewCommentMail($record));
                        }
                    })
                    ->slideOver()
                    ->modalWidth(MaxWidth::ExtraLarge)
                    ->disableCreateAnother(),
            ])
            ->actions([
                \Filament\Tables\Actions\ActionGroup::make([
                    Tables\Actions\Action::make('Reply')
                        ->icon('heroicon-o-arrow-uturn-left')
                        ->color('gray')
                        ->visible(fn (Comment $record) => $record->parent_id === null)
                        ->fillForm(fn (Comment $record): array => [
                            'parent_id' => $record->id,
                        ])
                        ->form([
                            Placeholder::make('')
                                ->content(fn ($record) => new \Illuminate\Support\HtmlString("
                                        <div class='prose dark:prose-invert max-w-none'>
                                            ".str($record->body)->markdown().'
                                        </div>
                                    ')),
                            MarkdownEditor::make('body')
                                ->required(),
                        ])
                        ->action(function (array $data, Comment $record): void {
                            $task = $this->getOwnerRecord();
                            $reply = $record->replies()->create([
                                'body' => $data['body'],
                                'task_id' => $task->id,
                                'user_id' => auth()->id(),
                            ]);

                            $recipients = collect();

                            if ($task->developer && $task->developer->id !== $reply->user_id) {
                                $recipients->push($task->developer);
                            }

                            if ($task->creator && $task->creator->id !== $reply->user_id) {
                                $recipients->push($task->creator);
                            }

                            if ($record->user_id !== $reply->user_id && ! $recipients->contains('id', $record->user_id)) {
                                $recipients->push($record->user);
                            }

                            $recipients = $recipients->unique('id');

                            foreach ($recipients as $recipient) {
                                Mail::to($recipient)->queue(new NewCommentMail($reply));
                            }
                        })
                        ->slideOver()
                        ->modalWidth(MaxWidth::ExtraLarge),

                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ]),

            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
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
