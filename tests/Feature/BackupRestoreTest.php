<?php

namespace Tests\Feature;

use App\Models\ThematicArea;
use App\Services\BackupService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use InvalidArgumentException;
use Tests\TestCase;

class BackupRestoreTest extends TestCase
{
    use RefreshDatabase;

    public function test_json_restore_replaces_data_and_rejects_incomplete_backups(): void
    {
        $this->seed(\Database\Seeders\RbacSeeder::class);
        ThematicArea::create(['title' => 'Before backup']);
        $backup = BackupService::dumpJson();

        ThematicArea::create(['title' => 'After backup']);
        BackupService::restoreJson($backup);

        $this->assertDatabaseHas('thematic_areas', ['title' => 'Before backup']);
        $this->assertDatabaseMissing('thematic_areas', ['title' => 'After backup']);

        try {
            BackupService::restoreJson(json_encode([
                'version' => 1,
                'tables' => ['thematic_areas' => []],
            ]));
            $this->fail('An incomplete backup must not be restored.');
        } catch (InvalidArgumentException $exception) {
            $this->assertDatabaseHas('thematic_areas', ['title' => 'Before backup']);
        }
    }
}
