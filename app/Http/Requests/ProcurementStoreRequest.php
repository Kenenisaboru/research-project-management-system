<?php

namespace App\Http\Requests;

use App\Models\Project;
use App\Services\RbacService;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Class ProcurementStoreRequest
 *
 * Validates procurement requests for equipment, reagents, and consumables.
 */
class ProcurementStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        $user = $this->user();
        if (!$user) {
            return false;
        }

        $projectId = $this->input('project_id');
        if ($projectId) {
            $project = Project::where('project_id', $projectId)->first();
            if ($project && (int) $project->pi_id !== (int) $user->id && $user->role !== 'admin') {
                return false;
            }
        }

        return $user->role === 'pi' || $user->role === 'admin'
            || RbacService::userHasPermission($user, 'submit_procurement');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'item_name'      => ['required', 'string', 'max:255'],
            'description'    => ['nullable', 'string', 'max:2000'],
            'category'       => ['required', 'in:Equipment,Reagents,Consumables,Other'],
            'estimated_cost' => ['required', 'numeric', 'min:0'],
            'project_id'     => ['required', 'exists:projects,project_id'],
        ];
    }

    /**
     * Custom validation messages.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'item_name.required'      => __('Item title or specification is required.'),
            'category.required'       => __('Please select a procurement category (Equipment, Reagents, Consumables, or Other).'),
            'category.in'             => __('Selected procurement category is invalid.'),
            'estimated_cost.required' => __('Estimated procurement cost is required.'),
            'estimated_cost.numeric'  => __('Estimated cost must be a numeric amount.'),
            'estimated_cost.min'      => __('Estimated cost cannot be negative.'),
            'project_id.required'     => __('Associated research project must be specified.'),
            'project_id.exists'       => __('Selected project does not exist.'),
        ];
    }

    /**
     * Custom attribute names for validation.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'item_name'      => __('Item Name'),
            'description'    => __('Item Description'),
            'category'       => __('Procurement Category'),
            'estimated_cost' => __('Estimated Cost'),
            'project_id'     => __('Project ID'),
        ];
    }
}
