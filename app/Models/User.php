<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'cpf',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // ---------- Relacionamentos ----------

    public function employee(): HasOne
    {
        return $this->hasOne(Employee::class);
    }

    public function empresas(): BelongsToMany
    {
        return $this->belongsToMany(Company::class, 'empresa_user', 'user_id', 'empresa_id')
            ->withPivot('role')
            ->withTimestamps();
    }

    // ---------- Empresa ativa (sessão) ----------

    public function activeCompany(): ?Company
    {
        $id = session('empresa_ativa_id');
        if (! $id) return null;
        if ($this->isSuperAdmin()) {
            return Company::find($id);
        }
        return $this->empresas()->find($id);
    }

    /**
     * Role do usuário no contexto da empresa ativa (ou da empresa informada).
     * Super admins sempre retornam 'super_admin'.
     */
    public function roleInCompany(?int $companyId = null): string
    {
        if ($this->role === 'super_admin') {
            return 'super_admin';
        }

        $companyId = $companyId ?? (int) session('empresa_ativa_id');

        if (! $companyId) {
            return $this->role; // fallback para o role global (retrocompatibilidade)
        }

        $pivot = $this->empresas()->where('empresa_id', $companyId)->first();

        return $pivot?->pivot->role ?? $this->role;
    }

    // ---------- Helpers de perfil ----------

    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    public function isAdmin(): bool
    {
        return $this->isSuperAdmin() || $this->roleInCompany() === 'admin';
    }

    public function isManager(): bool
    {
        if ($this->isSuperAdmin()) return false;
        return $this->roleInCompany() === 'manager';
    }

    public function isEmployee(): bool
    {
        if ($this->isSuperAdmin()) return false;
        return $this->roleInCompany() === 'employee';
    }

    public function isAdminOrManager(): bool
    {
        return $this->isAdmin() || $this->isManager();
    }
}

