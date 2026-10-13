<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\ThematicArea;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectWorkflowAndSearchTest extends TestCase
{
    use RefreshDatabase;

    public function test_project_search_combines_title_status_and_thematic_area_filters(): void
    {
        $pi = User::factory()->create(['role' => 'pi']);
        $area = ThematicArea::create(['title' => 'Water Research']);
        $otherArea = ThematicArea::create(['title' => 'Health Research']);

        Project::create([
            'title' => 'Needle project',
            'abstract_text' => 'Abstract',
            'thematic_id' => $area->id,
            'pi_id' => $pi->id,
            'requested_budget' => 1000,
            'status' => 'Draft',
        ]);
        Project::create([
            'title' => 'Needle project in another area',
            'abstract_text' => 'Abstract',
            'thematic_id' => $otherArea->id,
            'pi_id' => $pi->id,
            'requested_budget' => 1000,
            'status' => 'Draft',
        ]);
        Project::create([
            'title' => 'Needle submitted project',
            'abstract_text' => 'Abstract',
            'thematic_id' => $area->id,
            'pi_id' => $pi->id,
            'requested_budget' => 1000,
            'status' => 'Submitted',
        ]);

        $response = $this->actingAs($pi)->get(route('projects.index', [
            'search' => 'Needle',
            'status' => 'Draft',
            'thematic_area' => $area->id,
        ]));

        $response->assertOk()
            ->assertSee('Needle project')
            ->assertDontSee('Needle project in another area')
            ->assertDontSee('Needle submitted project');
    }

    public function test_project_detail_explains_the_next_actor_for_a_submitted_proposal(): void
    {
        $pi = User::factory()->create(['role' => 'pi']);
        $area = ThematicArea::create(['title' => 'Water Research']);
        $project = Project::create([
            'title' => 'Workflow guidance project',
            'abstract_text' => 'Abstract',
            'thematic_id' => $area->id,
            'pi_id' => $pi->id,
            'requested_budget' => 1000,
            'status' => 'Submitted',
        ]);

        $response = $this->actingAs($pi)->get(route('projects.show', $project->project_id));

        $response->assertOk()
            ->assertSee('Department Head')
            ->assertSee('should act next')
            ->assertSee('Screen the submitted proposal');
    }
}
