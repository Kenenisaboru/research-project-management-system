<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\Role;
use App\Models\ThematicArea;
use App\Models\User;
use App\Services\BackupService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;

class SqliteBackupRestoreTest extends TestCase
{
    private string $databasePath;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('local');
        $this->databasePath = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'rpms-restore-' . Str::uuid() . '.sqlite';
        touch($this->databasePath);
        config(['database.connections.sqlite.database' => $this->databasePath]);
        DB::purge('sqlite');
        $this->artisan('migrate:fresh', ['--force' => true])->assertExitCode(0);
        $this->seed(\Database\Seeders\RbacSeeder::class);
    }

    protected function tearDown(): void
    {
        DB::purge('sqlite');
        foreach ([$this->databasePath, $this->databasePath . '-wal', $this->databasePath . '-shm'] as $path) {
            if (is_file($path)) {
                unlink($path);
            }
        }

        Storage::disk('local')->delete(Storage::disk('local')->files(BackupService::DIR));
        parent::tearDown();
    }

    public function test_sqlite_restore_validates_and_atomically_replaces_the_database(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'role_id' => Role::where('name', 'admin')->value('id'),
        ]);
        $area = ThematicArea::create(['title' => 'Restore test']);
        $before = Project::create([
            'title' => 'Project in backup',
            'abstract_text' => 'Abstract',
            'thematic_id' => $area->id,
            'pi_id' => $admin->id,
            'requested_budget' => 1000,
        ]);

        $backupName = BackupService::create();
        $backup = Storage::disk('local')->get(BackupService::DIR . '/' . $backupName);
        Project::create([
            'title' => 'Project after backup',
            'abstract_text' => 'Abstract',
            'thematic_id' => $area->id,
            'pi_id' => $admin->id,
            'requested_budget' => 1000,
        ]);

        $response = $this->actingAs($admin)->post(route('admin.backups.restore'), [
            'backup_file' => UploadedFile::fake()->createWithContent('restore.sqlite', $backup),
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('projects', ['project_id' => $before->project_id]);
        $this->assertDatabaseMissing('projects', ['title' => 'Project after backup']);
    }
}
