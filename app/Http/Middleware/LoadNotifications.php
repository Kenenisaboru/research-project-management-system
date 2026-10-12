<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;

class LoadNotifications
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $user = Auth::user();
            $notifCount = 0;
            $notifItems = [];
            $role = $user->role;

            // Generated notifications (single source of truth for in-app alerts)
            \App\Models\Notification::forUser($user->id)->unread()->latest()->limit(10)->get()
                ->each(function (\App\Models\Notification $n) use (&$notifCount, &$notifItems) {
                    $notifCount++;
                    $notifItems[] = [
                        'title' => $n->type ? ucwords(strtolower(str_replace('_', ' ', $n->type))) : 'Notice',
                        'desc'  => $n->message,
                        'time'  => $n->created_at ? $n->created_at->diffForHumans() : 'Now',
                        'icon'  => 'bi-bell text-primary',
                    ];
                });

            // Legacy role-scoped roll-up counts (kept for backwards compatibility)
            if ($role === 'reviewer') {
                $n = \App\Models\Evaluation::where('examiner_id', $user->id)
                    ->where('decision', 'Pending')->count();
                if ($n > 0) {
                    $notifCount += $n;
                    $notifItems[] = ['title' => 'Pending Evaluations', 'desc' => "$n project evaluation(s) awaiting your scoring.", 'time' => 'Now', 'icon' => 'bi-clipboard-check text-warning'];
                }
            } elseif (in_array($role, ['coordinator', 'dh'])) {
                $n = \App\Models\Project::where('status', 'Submitted')->count();
                if ($n > 0) {
                    $notifCount += $n;
                    $notifItems[] = ['title' => 'Submitted Proposals', 'desc' => "$n new proposal(s) awaiting screening.", 'time' => 'Now', 'icon' => 'bi-inbox text-primary'];
                }
            } elseif (in_array($role, ['dean', 'rcsc', 'vparttcs'])) {
                $n = \App\Models\BudgetRequest::where('status', 'Pending')->count();
                if ($n > 0) {
                    $notifCount += $n;
                    $notifItems[] = ['title' => 'Budget Approvals', 'desc' => "$n budget request(s) pending your decision.", 'time' => 'Now', 'icon' => 'bi-cash-stack text-success'];
                }
            } elseif ($role === 'pi') {
                $n = \App\Models\Project::where('pi_id', $user->id)
                    ->whereNotIn('status', ['Completed', 'Terminated'])->count();
                if ($n > 0) {
                    $notifItems[] = ['title' => 'Active Projects', 'desc' => "You have $n project(s) in progress.", 'time' => 'Now', 'icon' => 'bi-folder2-open text-info'];
                }
            } elseif ($role === 'irerc') {
                $n = \App\Models\IRERCClearance::where('status', 'Pending')->count();
                if ($n > 0) {
                    $notifCount += $n;
                    $notifItems[] = ['title' => 'Ethics Clearances', 'desc' => "$n proposal(s) awaiting ethical review.", 'time' => 'Now', 'icon' => 'bi-shield-exclamation text-danger'];
                }
            } elseif ($role === 'finance') {
                $n = \App\Models\BudgetRequest::where('status', 'Approved')->whereNull('disbursed_at')->count();
                if ($n > 0) {
                    $notifCount += $n;
                    $notifItems[] = ['title' => 'Disbursements', 'desc' => "$n approved tranche(s) ready for release.", 'time' => 'Now', 'icon' => 'bi-cash-coin text-success'];
                }
            } elseif ($role === 'admin') {
                $b = \App\Services\AlertsService::backupRiskCount();
                if ($b > 0) {
                    $notifCount += $b;
                    $notifItems[] = ['title' => 'Risk Alerts', 'desc' => "$b overdue or over-budget project(s) need attention.", 'time' => 'Now', 'icon' => 'bi-exclamation-triangle text-danger'];
                }
                $notifItems[] = [
                    'title' => 'System Overview',
                    'desc'  => \App\Models\User::where('status', 'active')->count() . ' active users',
                    'time'  => 'Now',
                    'icon'  => 'bi-gear text-primary',
                ];
            } elseif ($role === 'tm') {
                $n = \App\Models\ProjectMember::where('user_id', $user->id)->count();
                if ($n > 0) {
                    $notifItems[] = ['title' => 'Memberships', 'desc' => "You belong to $n project team(s).", 'time' => 'Now', 'icon' => 'bi-people text-info'];
                }
            }

            View::share('notifCount', $notifCount);
            View::share('notifItems', $notifItems);
        }

        return $next($request);
    }
}