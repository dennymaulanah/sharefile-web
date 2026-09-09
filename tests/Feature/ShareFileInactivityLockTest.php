<?php

namespace Tests\Feature;

use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShareFileInactivityLockTest extends TestCase
{
    use RefreshDatabase;
    protected function setUp(): void
    {
        parent::setUp();
        Setting::set('sharefile_password_enabled', '1');
        Setting::set('sharefile_password', 'budidaya123');
    }

    public function test_sharefile_is_locked_initially(): void
    {
        $response = $this->get('/data-File');
        $response->assertStatus(200);
        $response->assertViewHas('isLocked', true);
    }

    public function test_unlock_with_wrong_password(): void
    {
        $response = $this->post('/data-File/unlock', [
            'password' => 'wrongpassword'
        ]);

        $response->assertSessionHas('lock_error');
        $response->assertSessionMissing('sharefile_unlocked');
    }

    public function test_unlock_with_correct_password_sets_session_and_timestamp(): void
    {
        $response = $this->post('/data-File/unlock', [
            'password' => 'budidaya123'
        ]);

        $response->assertSessionHas('sharefile_unlocked', true);
        $this->assertNotNull(session('sharefile_last_activity'));
    }

    public function test_active_session_within_5_minutes_stays_unlocked(): void
    {
        $response = $this->withSession([
            'sharefile_unlocked' => true,
            'sharefile_last_activity' => time() - 100 // 100 seconds ago (< 300)
        ])->get('/data-File');

        $response->assertStatus(200);
        $response->assertViewHas('isLocked', false);
        $response->assertSessionHas('sharefile_unlocked', true);
    }

    public function test_session_expires_after_5_minutes_of_inactivity(): void
    {
        $response = $this->withSession([
            'sharefile_unlocked' => true,
            'sharefile_last_activity' => time() - 305 // 305 seconds ago (> 300)
        ])->get('/data-File');

        $response->assertStatus(200);
        $response->assertViewHas('isLocked', true);
        $response->assertSessionMissing('sharefile_unlocked');
        $response->assertSessionHas('lock_error');
    }

    public function test_middleware_blocks_action_after_5_minutes_inactivity(): void
    {
        $response = $this->withSession([
            'sharefile_unlocked' => true,
            'sharefile_last_activity' => time() - 305
        ])->postJson('/data-File/folder', [
            'folder_name' => 'Test Folder'
        ]);

        $response->assertStatus(403);
        $response->assertJson([
            'success' => false,
            'locked' => true,
        ]);
    }

    public function test_ping_activity_updates_timestamp_when_active(): void
    {
        $initialTime = time() - 60;
        $response = $this->withSession([
            'sharefile_unlocked' => true,
            'sharefile_last_activity' => $initialTime
        ])->postJson('/data-File/ping-activity');

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        $this->assertGreaterThanOrEqual($initialTime, session('sharefile_last_activity'));
    }

    public function test_ping_activity_fails_and_locks_when_timeout_exceeded(): void
    {
        $response = $this->withSession([
            'sharefile_unlocked' => true,
            'sharefile_last_activity' => time() - 350
        ])->postJson('/data-File/ping-activity');

        $response->assertStatus(403);
        $response->assertJson([
            'success' => false,
            'locked' => true
        ]);
        $response->assertSessionMissing('sharefile_unlocked');
    }

    public function test_lock_route_with_idle_reason(): void
    {
        $response = $this->withSession([
            'sharefile_unlocked' => true,
            'sharefile_last_activity' => time()
        ])->post('/data-File/lock', ['reason' => 'idle']);

        $response->assertRedirect('/data-File');
        $response->assertSessionMissing('sharefile_unlocked');
        $response->assertSessionHas('lock_error');
    }
}
