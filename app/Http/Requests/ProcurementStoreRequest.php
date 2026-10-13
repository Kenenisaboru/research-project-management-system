<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use App\Services\RbacService;

/**
 * Form request for submitting a procurement (purchase) request for a project.
 *
 * @author  RPMS Pro Upgrade
 */
class ProcurementStoreRequest extends FormRequest
{
    /**
     * PIs with the submit_procurement permission may raise procurement requests.
     */
    public function authorize(): bool
    {
        $user = Auth::user();

        return $user
            && $user->role === 'pi'
            && RbacService::userHasPermission($user, 'submit_procurement');
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'item_name'      => ['required', 'string', 'max:255'],
            'description'    => ['nullable', 'string', 'max:2000'],
            'category'       => ['required', 'string', 'in:Equipment,Reagents,Consumables,Other'],
            'estimated_cost' => ['required', 'numeric', 'min:0'],
            'project_id'     => ['required', 'integer', 'exists:projects,project_id'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'item_name'      => __('item name'),
            'description'    => __('item description'),
            'category'       => __('procurement category'),
            'estimated_cost' => __('estimated cost'),
            'project_id'     => __('project'),
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'category.in'          => __('Category must be one of: Equipment, Reagents, Consumables, or Other.'),
            'estimated_cost.min'   => __('Estimated cost cannot be negative.'),
            'project_id.exists'    => __('The selected project does not exist.'),
        ];
    }
}
