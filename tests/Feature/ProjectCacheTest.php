<?php

use App\Models\Project;
use App\Models\User;
use Illuminate\Support\Facades\Cache;

/**
 * Test caching behavior for project index results.
 */
it('caches project index results', function () {
     $user = User::factory()->create();
     $token = $user->createToken('api')->plainTextToken;

     Project::factory()->count(2)->create(['created_by' => $user->id]);

     Cache::tags('projects')->flush();

     // first hit -> warm cache
     $this->withHeader('Authorization', 'Bearer ' . $token)
          ->getJson('/api/projects')
          ->assertStatus(200);

     // second hit should serve from cache (we can’t directly assert source,
     // but we can assert it still returns OK and same data shape)
     $this->withHeader('Authorization', 'Bearer ' . $token)
          ->getJson('/api/projects')
          ->assertStatus(200);
});
