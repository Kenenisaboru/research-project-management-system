<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\ProjectDocument;
use App\Models\ThematicArea;
use App\Models\User;
use App\Services\NotificationService;
use App\Services\ProjectSearchService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdvancedFeaturesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_analytics_dashboard_with_filters()
    {
        $vp = User::where('role', 'vparttcs')->first();

        $response = $this->actingAs($vp)->get(route('analytics', [
            'date_from' => now()->subMonth()->format('Y-m-d'),
            'date_to'   => now()->format('Y-m-d'),
        ]));

        $response->assertStatus(200)
            ->assertSee('Executive Analytics')
            ->assertSee('Thematic Area');
    }

    public function test_notification_centre_and_mark_read()
    {
        $user = User::where('role', 'pi')->first();

        $notification = NotificationService::notifyUser(
            $user->id,
            'TEST_ALERT',
            'Your progress report has been received.',
            'workflow',
            'high',
            '/progress'
        );

        $response = $this->actingAs($user)->get(route('notifications.index'));
        $response->assertStatus(200)
            ->assertSee('Notification Centre')
            ->assertSee('Your progress report has been received.');

        // Mark read
        $markResponse = $this->actingAs($user)->post(route('notifications.read', $notification->id));
        $markResponse->assertRedirect('/progress');

        $notification->refresh();
        $this->assertNotNull($notification->read_at);
    }

    public function test_notification_poll_uses_authenticated_web_session()
    {
        $user = User::where('role', 'admin')->first();
        NotificationService::notifyUser($user->id, 'WEB_POLL_TEST', 'Web session notification poll.');

        $response = $this->actingAs($user)->getJson(route('notifications.poll'));

        $response->assertOk()
            ->assertJsonStructure([
                'success',
                'data' => [
                    'unread_count',
                    'recent',
                ],
            ])
            ->assertJsonFragment(['message' => 'Web session notification poll.']);
    }

    public function test_api_notification_poll_endpoint()
    {
        $user = User::where('role', 'admin')->first();
        $token = $user->createToken('test')->plainTextToken;

        NotificationService::notifyUser($user->id, 'SYSTEM_PING', 'System maintenance tonight.');

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/v1/notifications/poll');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'unread_count',
                    'recent',
                ],
            ]);

        $this->assertGreaterThanOrEqual(1, $response->json('data.unread_count'));
    }

    public function test_project_search_service_and_csv_export()
    {
        $pi = User::where('role', 'pi')->first();

        $request = new \Illuminate\Http\Request([
            'search' => 'Solar',
            'export' => 'csv',
        ]);

        $query = ProjectSearchService::buildQuery($request, $pi);
        $this->assertNotNull($query);

        $streamResponse = ProjectSearchService::exportCsv($query->get());
        $this->assertEquals(200, $streamResponse->getStatusCode());
        $this->assertTrue(str_contains($streamResponse->headers->get('content-type', ''), 'text/csv'));
    }

    public function test_versioned_project_document_upload_and_download()
    {
        Storage::fake('public');

        $pi = User::where('role', 'pi')->first();
        $project = Project::where('pi_id', $pi->id)->first();

        // 1. Upload Version 1
        $file1 = UploadedFile::fake()->create('protocol_v1.pdf', 500, 'application/pdf');
        $upload1 = $this->actingAs($pi)->post(route('projects.documents.store', $project->project_id), [
            'title'         => 'Ethics Protocol',
            'document_type' => 'Ethics_Clearance',
            'file'          => $file1,
            'notes'         => 'First draft submitted',
        ]);
        $upload1->assertSessionHas('success');

        $doc1 = ProjectDocument::where('project_id', $project->project_id)
            ->where('title', 'Ethics Protocol')
            ->first();

        $this->assertNotNull($doc1);
        $this->assertEquals(1, $doc1->version_number);
        $this->assertTrue($doc1->is_current);

        // 2. Upload Version 2 with same title -> must increment version and mark v1 not current
        $file2 = UploadedFile::fake()->create('protocol_v2.pdf', 600, 'application/pdf');
        $upload2 = $this->actingAs($pi)->post(route('projects.documents.store', $project->project_id), [
            'title'         => 'Ethics Protocol',
            'document_type' => 'Ethics_Clearance',
            'file'          => $file2,
            'notes'         => 'Revised per committee request',
        ]);
        $upload2->assertSessionHas('success');

        $doc1->refresh();
        $this->assertFalse($doc1->is_current);

        $doc2 = ProjectDocument::where('project_id', $project->project_id)
            ->where('title', 'Ethics Protocol')
            ->where('version_number', 2)
            ->first();

        $this->assertNotNull($doc2);
        $this->assertTrue($doc2->is_current);

        // 3. Download document
        $downloadResponse = $this->actingAs($pi)->get(route('documents.download', $doc2->id));
        $this->assertEquals(200, $downloadResponse->getStatusCode());
    }

    public function test_institutional_reports_generation()
    {
        $admin = User::where('role', 'admin')->first();

        // 1. Reports Index
        $indexResponse = $this->actingAs($admin)->get(route('reports.index'));
        $indexResponse->assertStatus(200)->assertSee('Institutional Reports Generator');

        // 2. Annual Output PDF
        $annualResponse = $this->actingAs($admin)->get(route('reports.annual-output', ['year' => date('Y')]));
        $annualResponse->assertStatus(200);

        // 3. Financial Audit PDF
        $finResponse = $this->actingAs($admin)->get(route('reports.financial-audit', ['year' => date('Y')]));
        $finResponse->assertStatus(200);

        // 4. Thematic Impact PDF
        $impactResponse = $this->actingAs($admin)->get(route('reports.thematic-impact'));
        $impactResponse->assertStatus(200);

        // 5. Researcher Performance PDF
        $perfResponse = $this->actingAs($admin)->get(route('reports.researcher-performance'));
        $perfResponse->assertStatus(200);
    }
}
