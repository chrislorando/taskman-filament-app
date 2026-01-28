<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Comment</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
        <div style="background-color: #f8f9fa; padding: 20px; border-radius: 5px; border-left: 4px solid #3b82f6;">
            <h2 style="color: #1e40af; margin-top: 0;">New Comment on Task</h2>
            <p style="margin: 0;"><strong>{{ $task->title }}</strong></p>
        </div>

        <div style="margin-top: 20px;">
            <p style="margin: 0; color: #666;">From: <strong>{{ $user->name }}</strong> ({{ $user->email }})</p>
            <p style="margin: 5px 0; color: #666;">Status: <strong>{{ $task->status->name }}</strong></p>
            <p style="margin: 5px 0; color: #666;">Severity: <strong>{{ $task->severity->name }}</strong></p>
        </div>

        <div style="margin-top: 20px; padding: 20px; background-color: #f3f4f6; border-radius: 5px;">
            <p style="margin-top: 0; font-weight: bold; color: #374151;">Comment:</p>
            <div>{!! str($comment->body)->markdown() !!}</div>
        </div>

        @if($comment->parent_id)
        <div style="margin-top: 20px;">
            <p style="margin: 0; color: #666; font-style: italic;">This is a reply to another comment.</p>
        </div>
        @endif

        <div style="margin-top: 30px; text-align: center;">
            <a href="{{ url('/tasks/' . $task->id) }}" style="display: inline-block; padding: 10px 20px; background-color: #3b82f6; color: white; text-decoration: none; border-radius: 5px;">View Task</a>
        </div>

        <div style="margin-top: 40px; padding-top: 20px; border-top: 1px solid #e5e7eb; text-align: center; color: #9ca3af; font-size: 12px;">
            <p style="margin: 0;">This email was sent from Task Management App.</p>
        </div>
    </div>
</body>
</html>
