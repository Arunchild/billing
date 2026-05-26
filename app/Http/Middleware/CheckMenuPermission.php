<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckMenuPermission
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        if (!$user) {
            return redirect()->route('login');
        }

        // Mapping route name patterns to staff permission keys
        $permissionMapping = [
            'dashboard' => 'dashboard',
            'invoices.' => 'invoice',
            'sale_returns.' => 'sale_return',
            'quotations.' => 'quotation',
            'purchases.' => 'purchase_bill',
            'purchase_returns.' => 'purchase_return',
            'purchase_orders.' => 'purchase_order',
            'debit_notes.' => 'debit_note',
            'credit_notes.' => 'credit_note',
            'suppliers.' => 'supplier',
            'inventory.' => 'inventory',
            'accounts.' => 'accounts',
            'expenses.' => 'expense',
            'customers.' => 'customer',
            'reports.' => 'reports',
            'staff.' => 'staff',
            'tools.' => 'tools',
            'master.' => 'master',
            'settings.' => 'settings',
        ];

        $routeName = $request->route()->getName();
        if (!$routeName) {
            return $next($request);
        }

        foreach ($permissionMapping as $pattern => $permission) {
            if ($routeName === $pattern || str_starts_with($routeName, $pattern)) {
                if (!$user->hasPermission($permission)) {
                    abort(403, 'Unauthorized access. You do not have permission to access this menu.');
                }
                break;
            }
        }

        return $next($request);
    }
}
