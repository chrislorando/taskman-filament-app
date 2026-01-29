<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Filament\Resources\TaskResource;
use App\Filament\Resources\TaskResource\Pages\EditTask;
use App\Models\Status;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class TaskStatusTransitionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed', ['--class' => 'StatusSeeder']);
        $this->artisan('db:seed', ['--class' => 'SeveritySeeder']);
    }

    /** @test */
    public function developer_cannot_see_closed_status_option_in_the_form(): void
    {
        $developer = User::factory()->create(['role' => UserRole::Developer]);
        $task = Task::factory()->create(['developer_id' => $developer->id]);
        $closedStatus = Status::where('name', 'Closed')->first();

        $response = $this->actingAs($developer)
            ->get(TaskResource::getUrl('edit', ['record' => $task]));

        $response->assertStatus(200);

        $response->assertDontSee('value="' . $closedStatus->id . '"', false);
    }

    /** @test */
    public function developer_cannot_save_task_with_closed_status()
    {
        $developer = User::factory()->create(['role' => UserRole::Developer]);
        $task = Task::factory()->create(['developer_id' => $developer->id]);
        $closedStatus = Status::where('name', 'Closed')->first();
        Livewire::actingAs($developer)
            ->test(EditTask::class, [
                'record' => $task->getRouteKey(),
            ])
            ->fillForm([
                'status_id' => $closedStatus->id,
            ])
            ->call('save')
            ->assertHasFormErrors(['status_id']);
    }

    /** @test */
    public function finish_date_persists_for_completed_and_closed_statuses(): void
    {
        $admin = User::factory()->create(['role' => UserRole::Admin]);
        $task = Task::factory()->create(['finish_date' => null]);

        $closedStatus = Status::firstOrCreate(['name' => 'Closed']);

        Livewire::actingAs($admin)
            ->test(EditTask::class, [
                'record' => $task->getRouteKey(),
            ])
            ->fillForm([
                'status_id' => $closedStatus->id,
            ])
            ->call('save')
            ->assertHasNoErrors();

        $task->refresh();

        $this->assertNotNull($task->finish_date);
        $this->assertEquals(now()->toDateString(), $task->finish_date->toDateString());
    }
}
