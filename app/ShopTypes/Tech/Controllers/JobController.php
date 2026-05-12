<?php

namespace App\ShopTypes\Tech\Controllers;

use App\Models\Job;

class JobController extends \App\Http\Controllers\JobController
{
    protected function showRoute(Job $job): string
    {
        return route('tech.jobs.show', $job);
    }

    protected function indexRoute(): string
    {
        return 'tech.jobs.index';
    }

    protected function listRoute(): string
    {
        return 'tech.jobs.list';
    }
}
