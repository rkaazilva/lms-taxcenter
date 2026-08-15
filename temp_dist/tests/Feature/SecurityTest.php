<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Http\Controllers\LmsController;
use Illuminate\Http\Request;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SecurityTest extends TestCase
{
    use RefreshDatabase;
    /**
     * Test that guest is redirected.
     */
    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get('/siswa/dashboard');
        $response->assertRedirect(route('login'));
    }

    /**
     * Test that guest accessing API returns 401.
     */
    public function test_guest_api_returns_unauthenticated(): void
    {
        $response = $this->getJson('/siswa/api/dashboard-data');
        $response->assertStatus(401);
    }

    /**
     * Test that student role cannot access teacher dashboard.
     */
    public function test_student_cannot_access_guru_dashboard(): void
    {
        $response = $this->withSession([
            'isLoggedIn' => true,
            'email' => 'siswa@test.local',
            'role' => 'SISWA',
            'nama' => 'Test Student'
        ])->get('/guru/dashboard');

        // Should return redirect or 403 depending on our middleware
        $response->assertRedirect(route('login'));
    }

    /**
     * Test that student role cannot submit grades.
     */
    public function test_student_cannot_submit_grades(): void
    {
        $response = $this->withSession([
            'isLoggedIn' => true,
            'email' => 'siswa@test.local',
            'role' => 'SISWA',
            'nama' => 'Test Student'
        ])->postJson(route('guru.submissions.grade'), [
            'email' => 'student@test.local',
            'id_tugas' => 'TUGAS01',
            'nilai' => 100
        ]);

        $response->assertStatus(403);
    }

    /**
     * Test migrate-database returns 403 when not in local environment.
     */
    public function test_migrate_database_blocks_in_production(): void
    {
        // Fake production environment
        $this->app->detectEnvironment(fn() => 'production');

        $response = $this->get('/migrate-database');
        $response->assertStatus(403);
    }
}
