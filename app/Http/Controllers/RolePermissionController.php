<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\Permission;
use App\Models\RolePermissionAudit;
use Illuminate\Http\Request;

class RolePermissionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            if (!auth()->user()->isSuperAdmin()) {
                abort(403, 'Unauthorized action.');
            }
            return $next($request);
        });
    }

    /**
     * Show role permissions list with permission counts
     * Only shows roles that have at least one permission assigned
     * Excludes Super Admin role (has access to all forms by default)
     */
    public function select()
    {
        $roles = Role::with('permissions')->orderBy('name')->get();
        
        // Calculate permission count for each role (permissions with at least one flag set)
        $roles->each(function($role) {
            $role->permission_count = $role->permissions->filter(function($permission) {
                return ($permission->pivot->read ?? false) || 
                       ($permission->pivot->write ?? false) || 
                       ($permission->pivot->delete ?? false);
            })->count();
        });
        
        // Filter to only show roles with permissions assigned, excluding Super Admin
        $roles = $roles->filter(function($role) {
            // Exclude Super Admin role (has access to all forms by default)
            if ($role->slug === 'super-admin') {
                return false;
            }
            return $role->permission_count > 0;
        });
        
        return view('masters.roles.select-role', compact('roles'));
    }

    /**
     * Show form to select role for permission assignment
     * Excludes Super Admin role and roles that already have permissions assigned
     */
    public function create()
    {
        // Get all roles with their permissions
        $allRoles = Role::with('permissions')->orderBy('name')->get();
        
        // Filter out Super Admin and roles that already have permissions assigned
        $roles = $allRoles->filter(function($role) {
            // Exclude Super Admin role (has access to all forms by default)
            if ($role->slug === 'super-admin') {
                return false;
            }
            
            // Exclude roles that already have permissions assigned
            $hasPermissions = $role->permissions->filter(function($permission) {
                return ($permission->pivot->read ?? false) || 
                       ($permission->pivot->write ?? false) || 
                       ($permission->pivot->delete ?? false);
            })->count() > 0;
            
            return !$hasPermissions; // Only include roles without permissions
        });
        
        $permissions = Permission::orderByRaw('COALESCE(form_name, name)')->get();
        
        return view('masters.roles.assign-permissions', compact('roles', 'permissions'));
    }

    /**
     * Handle role selection and redirect to edit form
     */
    public function store(Request $request)
    {
        $request->validate([
            'role_id' => 'required|exists:roles,id',
        ]);

        return redirect()->route('role-permissions.edit', $request->role_id);
    }

    /**
     * Show permission assignment form for selected role
     * Excludes Super Admin role from dropdown
     */
    public function edit(Role $role)
    {
        // Prevent editing Super Admin role permissions
        if ($role->slug === 'super-admin') {
            abort(403, 'Super Admin role has access to all forms by default and cannot be modified.');
        }
        
        // Use form_name if exists, otherwise fallback to name
        $permissions = Permission::orderByRaw('COALESCE(form_name, name)')->get();
        // Load existing permissions for the role
        $role->load('permissions');
        
        // Get all roles with their permissions for dropdown
        $allRoles = Role::with('permissions')->orderBy('name')->get();
        
        // Filter out Super Admin and roles that already have permissions assigned, but keep the current role
        $allRoles = $allRoles->filter(function($r) use ($role) {
            // Exclude Super Admin role (has access to all forms by default)
            if ($r->slug === 'super-admin') {
                return false;
            }
            
            // Always include the current role being edited
            if ($r->id == $role->id) {
                return true;
            }
            
            // Exclude other roles that have permissions assigned
            $hasPermissions = $r->permissions->filter(function($permission) {
                return ($permission->pivot->read ?? false) || 
                       ($permission->pivot->write ?? false) || 
                       ($permission->pivot->delete ?? false);
            })->count() > 0;
            
            return !$hasPermissions; // Only include roles without permissions (or current role)
        });
        
        return view('masters.roles.permissions', compact('role', 'permissions', 'allRoles'));
    }

    public function update(Request $request, Role $role)
    {
        // Prevent updating Super Admin role permissions
        if ($role->slug === 'super-admin') {
            abort(403, 'Super Admin role has access to all forms by default and cannot be modified.');
        }
        
        // Load existing permissions for comparison
        $role->load('permissions');
        $oldPermissions = $role->permissions->mapWithKeys(function($perm) {
            return [$perm->id => [
                'read' => $perm->pivot->read ?? false,
                'write' => $perm->pivot->write ?? false,
                'delete' => $perm->pivot->delete ?? false,
            ]];
        })->toArray();

        // Expecting input array: permissions[permission_id][read/write/delete]
        $data = $request->input('permissions', []);
        
        $syncData = [];
        foreach ($data as $permissionId => $flags) {
            $read = isset($flags['read']) ? true : false;
            $write = isset($flags['write']) ? true : false;
            $delete = isset($flags['delete']) ? true : false;
            
            if ($read || $write || $delete) {
                $syncData[$permissionId] = [
                    'read' => $read,
                    'write' => $write,
                    'delete' => $delete,
                ];
            }
        }
        
        // Log changes for each permission
        $allPermissionIds = array_unique(array_merge(array_keys($oldPermissions), array_keys($syncData)));
        foreach ($allPermissionIds as $permissionId) {
            $oldPerm = $oldPermissions[$permissionId] ?? ['read' => false, 'write' => false, 'delete' => false];
            $newPerm = $syncData[$permissionId] ?? null;
            
            if ($newPerm === null) {
                // Permission removed
                RolePermissionAudit::log('permission_removed', $role->id, $permissionId, null, json_encode($oldPerm), null, "Permission removed from role");
            } else {
                // Check each field for changes
                foreach (['read', 'write', 'delete'] as $field) {
                    if (($oldPerm[$field] ?? false) != ($newPerm[$field] ?? false)) {
                        $permission = Permission::find($permissionId);
                        $permissionName = $permission->form_name ?? $permission->name ?? 'Unknown';
                        RolePermissionAudit::log(
                            'permission_assigned',
                            $role->id,
                            $permissionId,
                            $field,
                            $oldPerm[$field] ? 'true' : 'false',
                            $newPerm[$field] ? 'true' : 'false',
                            "Permission '{$permissionName}' {$field} changed for role '{$role->name}'"
                        );
                    }
                }
            }
        }
        
        $role->permissions()->sync($syncData);

        return redirect()->route('role-permissions.select')->with('success', 'Permissions for role "' . $role->name . '" updated successfully.');
    }
}
