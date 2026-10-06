<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    use HasRoles {
        hasPermissionTo as traitHasPermissionTo;
        getPermissionsViaRoles as traitGetPermissionsViaRoles;
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Get simulated role from session if available.
     * // ponytail: Cek simulated_role di session untuk simulasi testing semua role (seperti pada aplikasi logsheet).
     *
     * @return string|null
     */
    public function getSimulatedRole()
    {
        try {
            if (function_exists('session') && session()->has('simulated_role')) {
                $simulated = session('simulated_role');
                return !empty($simulated) ? (string) $simulated : null;
            }
        } catch (\Throwable $e) {
            // Abaikan jika session tidak tersedia
        }

        return null;
    }

    /**
     * Override roles collection accessor to return simulated role when active.
     */
    public function getRolesAttribute()
    {
        $simulated = $this->getSimulatedRole();
        if ($simulated) {
            $role = \Spatie\Permission\Models\Role::with('permissions')->where('name', $simulated)->first();
            return collect($role ? [$role] : []);
        }

        if (! $this->relationLoaded('roles')) {
            $this->load('roles');
        }

        return $this->getRelationValue('roles');
    }

    /**
     * Pluck role names taking simulated role into account.
     */
    public function getRoleNames(): \Illuminate\Support\Collection
    {
        return $this->roles->pluck('name');
    }

    /**
     * Accessor for single role string (lowercase), compatible with auth()->user()->role checks.
     */
    public function getRoleAttribute()
    {
        $firstRole = $this->roles->first();
        return $firstRole ? strtolower($firstRole->name) : null;
    }

    /**
     * Return permissions via roles taking simulated role into account.
     */
    public function getPermissionsViaRoles(): \Illuminate\Support\Collection
    {
        $simulated = $this->getSimulatedRole();
        if ($simulated) {
            $role = \Spatie\Permission\Models\Role::with('permissions')->where('name', $simulated)->first();
            return $role ? $role->permissions->sort()->values() : collect();
        }

        return $this->traitGetPermissionsViaRoles();
    }

    /**
     * Check permission taking simulated role into account.
     */
    public function hasPermissionTo($permission, $guard = null): bool
    {
        $simulated = $this->getSimulatedRole();
        if ($simulated) {
            $role = \Spatie\Permission\Models\Role::with('permissions')->where('name', $simulated)->first();
            if ($role) {
                if (is_string($permission)) {
                    return $role->permissions->contains('name', $permission);
                }
                return $role->hasPermissionTo($permission);
            }
            return false;
        }

        return $this->traitHasPermissionTo($permission, $guard);
    }
}

