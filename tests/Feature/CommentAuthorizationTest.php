<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Task;
use App\Models\Comment;
use App\Enums\UserRole;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommentAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function developer_can_edit_and_delete_their_own_comment()
    {
        $developer = User::factory()->create(['role' => UserRole::Developer]);
        $task = Task::factory()->create(['developer_id' => $developer->id]);
        $comment = Comment::factory()->create([
            'user_id' => $developer->id,
            'task_id' => $task->id
        ]);

        $this->actingAs($developer);

        $this->assertTrue($developer->can('update', $comment));
        $this->assertTrue($developer->can('delete', $comment));
    }

    /** @test */
    public function developer_cannot_edit_or_delete_admin_comment()
    {
        $developer = User::factory()->create(['role' => UserRole::Developer]);
        $admin = User::factory()->create(['role' => UserRole::Admin]);
        $task = Task::factory()->create(['developer_id' => $developer->id]);

        $adminComment = Comment::factory()->create([
            'user_id' => $admin->id,
            'task_id' => $task->id
        ]);

        $this->actingAs($developer);

        $this->assertFalse($developer->can('update', $adminComment));
        $this->assertFalse($developer->can('delete', $adminComment));
    }
}