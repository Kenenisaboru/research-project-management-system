<?php

namespace Database\Seeders;

use App\Models\Award;
use App\Models\Paper;
use App\Models\PaperEvaluation;
use App\Models\Project;
use App\Models\ResearchCall;
use App\Models\User;
use Illuminate\Database\Seeder;

class SampleDataSeeder extends Seeder
{
    public function run()
    {
        $pi          = User::where('role', 'pi')->first();
        $reviewer    = User::where('role', 'reviewer')->first();
        $coordinator = User::where('role', 'coordinator')->first();
        $admin       = User::where('role', 'admin')->first();
        $projects    = Project::orderBy('project_id')->get();

        if (! $pi || ! $admin) {
            return;
        }

        // -----------------------------------------------------------------
        // Calls for proposals / abstracts
        // -----------------------------------------------------------------
        if (ResearchCall::count() === 0 && $coordinator) {
            ResearchCall::create([
                'title'                => '2026 Thematic Research Grant Call — Climate Resilience',
                'description'          => 'Full research projects addressing climate adaptation, flood resilience and sustainable agriculture in the Baro-Akobo basin.',
                'call_type'            => 'project',
                'thematic_id'          => optional($projects->first())->thematic_id,
                'funding_organization' => 'Gambella University Research Office',
                'budget_ceiling'       => 800000.00,
                'submission_deadline'  => now()->addDays(45),
                'status'               => 'Open',
                'posted_by'            => $coordinator->id,
            ]);

            ResearchCall::create([
                'title'                => 'Rapid Community Service Abstract Call — Public Health',
                'description'          => 'Short community-service abstracts for vector-borne disease and public health interventions.',
                'call_type'            => 'abstract',
                'thematic_id'          => optional($projects->skip(2)->first())->thematic_id,
                'funding_organization' => 'GMU Community Service Directorate',
                'budget_ceiling'       => 250000.00,
                'submission_deadline'  => now()->addDays(20),
                'status'               => 'Open',
                'posted_by'            => $coordinator->id,
            ]);

            ResearchCall::create([
                'title'                => 'Closed Call — Digital Infrastructure Seed Grants (2025)',
                'description'          => 'Seed grants for smart governance and digital infrastructure pilot studies. Submission window has closed.',
                'call_type'            => 'project',
                'thematic_id'          => optional($projects->skip(1)->first())->thematic_id,
                'funding_organization' => 'GMU ICT Directorate',
                'budget_ceiling'       => 500000.00,
                'submission_deadline'  => now()->subDays(10),
                'status'               => 'Closed',
                'posted_by'            => $coordinator->id,
            ]);
        }

        // -----------------------------------------------------------------
        // Publication & Award
        // -----------------------------------------------------------------
        if (Paper::count() === 0 && $projects->count() > 0) {
            $p1 = $projects->get(0);
            $p2 = $projects->get(1) ?? $p1;

            $paper1 = Paper::create([
                'project_id'      => $p1->project_id,
                'submitter_id'    => $pi->id,
                'title'           => 'IoT Telemetry for Community Flood Early Warning in the Baro-Akobo Lowlands',
                'abstract_text'   => 'This paper presents a solar-powered sensor network delivering real-time river-stage alerts to vulnerable lowland communities.',
                'authors'         => 'Abebe Bikila, Kebede Tassew, Sara Mohammed',
                'journal_name'    => 'Journal of Hydroinformatics',
                'journal_index'   => 'SCIE',
                'status'          => 'Recorded',
                'publication_date'=> now()->subMonths(3),
                'award_status'    => 'Awarded',
            ]);

            $paper2 = Paper::create([
                'project_id'      => $p2->project_id,
                'submitter_id'    => $pi->id,
                'title'           => 'Soil Carbon Dynamics under Indigenous Agro-Forestry in National Park Buffer Zones',
                'abstract_text'   => 'An empirical assessment of soil organic carbon sequestration in community-led reforestation corridors.',
                'authors'         => 'Abebe Bikila, Mesfin Haile',
                'journal_name'    => 'Agroforestry Systems',
                'journal_index'   => 'Scopus',
                'status'          => 'UnderEvaluation',
                'award_status'    => 'None',
            ]);

            $paper3 = Paper::create([
                'project_id'      => $p1->project_id,
                'submitter_id'    => $pi->id,
                'title'           => 'GIS-Based Seasonal Malaria Transmission Mapping in Itang Special Woreda',
                'abstract_text'   => 'Spatial-temporal modelling of vector-borne disease risk in riparian ecosystems.',
                'authors'         => 'Abebe Bikila, Tigist Assefa',
                'journal_name'    => 'Parasites & Vectors',
                'journal_index'   => 'SCIE',
                'status'          => 'Submitted',
                'award_status'    => 'None',
            ]);

            if ($reviewer) {
                PaperEvaluation::create([
                    'paper_id'     => $paper1->paper_id,
                    'evaluator_id' => $reviewer->id,
                    'decision'     => 'RecommendAccept',
                    'score'        => 88.50,
                    'comments'     => 'Rigorous methodology and strong community impact. Recommended for recording.',
                ]);

                PaperEvaluation::create([
                    'paper_id'     => $paper2->paper_id,
                    'evaluator_id' => $reviewer->id,
                    'decision'     => 'RecommendAcceptWithRevisions',
                    'score'        => 76.00,
                    'comments'     => 'Solid field data; clarify sampling design and add statistical power analysis.',
                ]);
            }

            Award::create([
                'paper_id'     => $paper1->paper_id,
                'recipient_id' => $pi->id,
                'awarded_by'   => $admin->id,
                'award_name'   => 'GMU Best Applied Research Publication Award 2026',
                'award_amount' => 50000.00,
                'citation'     => 'For outstanding high-impact publication on community flood resilience.',
                'awarded_at'   => now()->subMonth(),
            ]);
        }
    }
}
