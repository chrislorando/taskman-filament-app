<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Filament\Resources\TaskResource;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskVisibilityTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Seed initial data like statuses before each test.
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed', ['--class' => 'StatusSeeder']);
        $this->artisan('db:seed', ['--class' => 'SeveritySeeder']);
    }

    /** @test */
    public function admin_can_see_all_tasks_in_the_list(): void
    {
        $admin = User::factory()->create(['role' => UserRole::Admin]);

        $tasks = Task::factory()->count(2)->create();

        $response = $this->actingAs($admin)->get(TaskResource::getUrl());

        $response->assertStatus(200);

        foreach ($tasks as $task) {
            $response->assertSee($task->title);
        }
    }

    /** @test */
    public function developer_can_only_see_their_assigned_tasks(): void
    {
        // 1. Setup: Create two developers
        $developer = User::factory()->create(['role' => 'developer']);
        $otherDeveloper = User::factory()->create(['role' => 'developer']);

        // 2. Setup: Create tasks for each
        $assignedTask = Task::factory()->create([
            'title' => 'My Secret Task',
            'developer_id' => $developer->id,
        ]);

        $otherTask = Task::factory()->create([
            'title' => 'Someone Elses Task',
            'developer_id' => $otherDeveloper->id,
        ]);

        // 3. Action: Login as first developer
        $response = $this->actingAs($developer)->get('/tasks');

        // 4. Assertion: Only see own task
        $response->assertStatus(200);
        $response->assertSee($assignedTask->title);
        $response->assertDontSee($otherTask->title);
    }

    /** @test */
    public function developer_cannot_view_details_of_unassigned_task(): void
    {
        $developer = User::factory()->create(['role' => 'developer']);
        $otherDeveloper = User::factory()->create(['role' => 'developer']);

        $unauthorizedTask = Task::factory()->create([
            'developer_id' => $otherDeveloper->id,
        ]);

        // Trying to access the detail page directly via URL
        $response = $this->actingAs($developer)
            ->get("/tasks/{$unauthorizedTask->id}");

        // Assertion: Should be Forbidden or Redirected
        $response->assertStatus(403);
    }
}
