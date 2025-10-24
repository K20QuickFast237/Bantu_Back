<?php

namespace App\Models;

use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\CanResetPassword as AuthCanResetPassword;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\Contracts\OAuthenticatable;
use Laravel\Passport\HasApiTokens;
use App\Http\Enums\RoleValues;

class User extends Authenticatable implements MustVerifyEmail, AuthCanResetPassword, OAuthenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable, CanResetPassword;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'nom',
        'prenom',
        'email',
        'photo_profil',
        'password',
        'role_actif',
        'is_active',
        'last_login',
        'email_verified_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function particulier()
    {
        return $this->hasOne(Particulier::class);
    }

    public function professionnel()
    {
        return $this->hasOne(Professionnel::class);
    }

    public function formations()
    {
        return $this->hasMany(Formation::class);
    }

    public function experiences()
    {
        return $this->hasMany(Experience::class);
    }

    /**
     * skills
     */
    public function skills(): BelongsToMany
    {
        return $this->belongsToMany(Skill::class, 'user_skill')->withPivot('niveau')->withTimestamps();
    }

    /**
     * roles
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'user_roles')
                    ->using(RoleUser::class) // ton pivot personnalisé
                    ->withPivot('isCurrent');
    }


    public function favoris()
    {
        return $this->belongsToMany(OffreEmploi::class, 'favoris', 'user_id', 'offre_emploi_id')
                    ->withTimestamps();
    }

    // Vérifie si l'utilisateur est un candidat
    public function isCandidat(): bool
    {
        return $this->particulier()->exists();
    }

    // Vérifie si l'utilisateur est un recruteur
    public function isRecruteur(): bool
    {
        return $this->professionnel()->exists();
    }

    public function isVendeur(): bool
    {
        return $this->roles()
                    ->wherePivot('isCurrent', 1)
                    ->where('name', RoleValues::VENDEUR->value)
                    ->exists();
    }

    public function isAdmin(): bool
    {
        return $this->roles()
                    ->wherePivot('isCurrent', 1)
                    ->where('name', RoleValues::ADMIN->value)
                    ->exists();
    }

}
