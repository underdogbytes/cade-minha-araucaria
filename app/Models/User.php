<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Jetstream\HasTeams;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens;

    /** @use HasFactory<UserFactory> */
    use HasFactory;

    use HasProfilePhoto;
    use HasTeams;
    use Notifiable;
    use TwoFactorAuthenticatable;

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
        'pinhao_balance',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'profile_photo_url',
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

    public function araucariaObservations(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(AraucariaObservation::class);
    }

    public function actionHistories(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(ActionHistory::class);
    }

    public function virtualTrees(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(VirtualTree::class);
    }

    /**
     * Sobrescreve o upload de foto do Jetstream para salvar como Base64 no banco,
     * igual ao comportamento das fotos de observação de araucárias.
     */
    public function updateProfilePhoto(UploadedFile $photo, string $storagePath = 'profile-photos'): void
    {
        $manager = new ImageManager(new Driver());

        $image = $manager->read($photo);
        $image->scaleDown(width: 800, height: 800);
        $encoded = $image->toJpeg(80);
        $base64 = base64_encode((string) $encoded);

        $this->forceFill([
            'profile_photo_path' => 'data:image/jpeg;base64,' . $base64,
        ])->save();
    }

    /**
     * Sobrescreve o accessor do Jetstream: se o path já for base64, retorna direto.
     */
    protected function profilePhotoUrl(): Attribute
    {
        return Attribute::get(function (): string {
            if ($this->profile_photo_path && str_starts_with($this->profile_photo_path, 'data:')) {
                return $this->profile_photo_path;
            }

            // Fallback: gera avatar com as iniciais do nome
            return 'https://ui-avatars.com/api/?name='
                . urlencode($this->name)
                . '&color=7F9CF5&background=EBF4FF';
        });
    }

    /**
     * Remove a foto de perfil (limpa o campo no banco).
     */
    public function deleteProfilePhoto(): void
    {
        $this->forceFill(['profile_photo_path' => null])->save();
    }

    protected static function booted(): void
    {
        static::creating(function ($user) {
            if (empty($user->username)) {
                do {
                    $slug = Str::lower(Str::random(8));
                } while (self::where('username', $slug)->exists());
                $user->username = $slug;
            }
        });
    }
}
