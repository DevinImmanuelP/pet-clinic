<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Filament\Panel;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Filament\Models\Contracts\HasAvatar;
use Illuminate\Notifications\Notifiable;
use Filament\Models\Contracts\HasTenants;
use Filament\Models\Contracts\FilamentUser;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class  User extends Authenticatable implements HasTenants, FilamentUser, HasAvatar
{
    use HasApiTokens;
    use HasFactory;
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'avatar_url',
        'name',
        'email',
        'password',
        'phone',
        'role_id'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(Schedule::class, 'owner_id');
    }

    public function clinics(): BelongsToMany
    {
        return $this->belongsToMany(Clinic::class);
    }

    public function pets(): HasMany
    {
        return $this->hasMany(Pet::class, 'owner_id');
    }

    public function getTenants(Panel $panel): array|Collection
    {
        return $this->clinics;
    }

    public function canAccessTenant(Model $tenant): bool
    {
        return $this->clinics->contains($tenant);
    }

    public function canAccessPanel(Panel $panel): bool
    {
        $role = Auth::user()->role->name;

        return match ($panel->getId()) {
            'admin' => $role === 'admin',
            'doctor' => $role === 'doctor',
            'owner' => $role === 'owner',
            default => false
        };
    }

    public function getFilamentAvatarUrl(): ?string
    {
        return "/storage/$this->avatar_url";
    }

    public function avatar(): Attribute
    {
        return Attribute::make(
            get: fn ($value, $attributes) => $attributes['avatar_url']
        );
    }
}
