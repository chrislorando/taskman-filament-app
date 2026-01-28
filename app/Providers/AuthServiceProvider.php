<?php

namespace App\Providers;

use App\Models\Comment;
use App\Models\Severity;
use App\Models\Status;
use App\Models\Task;
use App\Models\User;
use App\Policies\CommentPolicy;
use App\Policies\SeverityPolicy;
use App\Policies\StatusPolicy;
use App\Policies\TaskPolicy;
use App\Policies\UserPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        User::class => UserPolicy::class,
        Status::class => StatusPolicy::class,
        Severity::class => SeverityPolicy::class,
        Task::class => TaskPolicy::class,
        Comment::class => CommentPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        //
    }
}
