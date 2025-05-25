<?php

namespace App\Providers;

use App\Models\banner;
use App\Models\Candidate;
use App\Models\Evaluation;
use App\Models\Halaka;
use App\Models\Post;
use App\Models\RecitationSession;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use App\Policies\BannerPolicy;
use App\Policies\CalenderPolicy;
use App\Policies\CandidatePolicy;
use App\Policies\EvaluationPolicy;
use App\Policies\GeneralSettingsPolicy;
use App\Policies\HalakaPolicy;
use App\Policies\PostPolicy;
use App\Policies\RecitationSessionPolicy;
use App\Policies\RolePolicy;
use App\Policies\SessionPolicy;
use App\Policies\StudentPolicy;
use App\Policies\TeacherPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Spatie\Permission\Contracts\Role;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        Role::class => RolePolicy::class,
        Teacher::class => TeacherPolicy::class,
        Student::class => StudentPolicy::class,
        User::class => TeacherPolicy::class,
        Halaka::class => SessionPolicy::class,
        RecitationSession::class => RecitationSessionPolicy::class,
        Evaluation::class => EvaluationPolicy::class,
        Halaka::class => HalakaPolicy::class,
        Post::class => PostPolicy::class,
        banner::class => BannerPolicy::class,
        Candidate::class => CandidatePolicy::class,
        'App\Models\GeneralSettings' => GeneralSettingsPolicy::class,



        //Banner::class => BannerPolicy::class,


    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();
        Gate::before(function ($user, $ability) {
            return $user->hasRole('super_admin') ? true : null;
        });
    }
}
