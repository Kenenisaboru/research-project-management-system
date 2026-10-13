<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class ProjectResource
 *
 * Transforms Project model into standard API JSON format with relationships.
 */
class ProjectResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            'project_id'            => $this->project_id,
            'project_code'          => $this->project_code ?? ('UNI-PRJ-' . $this->project_id),
            'title'                 => $this->title,
            'abstract_text'         => $this->abstract_text,
            'status'                => $this->status,
            'current_stage'         => (int) $this->current_stage,
            'requested_budget'      => (float) $this->requested_budget,
            'approved_budget'       => $this->approved_budget !== null ? (float) $this->approved_budget : null,
            'ethical_cleared'       => (bool) $this->ethical_cleared,
            'feedback'              => $this->feedback,
            'proposal_document_url' => $this->proposal_document_url ? asset('storage/' . $this->proposal_document_url) : null,
            'contract_signed_at'    => $this->contract_signed_at?->toIso8601String(),
            'activated_at'          => $this->activated_at?->toIso8601String(),
            'completed_at'          => $this->completed_at?->toIso8601String(),
            'pi'                    => new UserResource($this->whenLoaded('pi')),
            'thematic_area'         => $this->whenLoaded('thematicArea', function () {
                return [
                    'id'          => $this->thematicArea?->id,
                    'title'       => $this->thematicArea?->title,
                    'description' => $this->thematicArea?->description,
                ];
            }),
            'department'            => $this->whenLoaded('department', function () {
                return [
                    'id'   => $this->department?->id,
                    'name' => $this->department?->name,
                    'code' => $this->department?->code,
                ];
            }),
            'created_at'            => $this->created_at?->toIso8601String(),
            'updated_at'            => $this->updated_at?->toIso8601String(),
        ];
    }
}
