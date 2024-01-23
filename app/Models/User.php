<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements FilamentUser
{
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'profile_photo_path',
        'first_name',
        'last_name',
        'middle_name',
        'gender',
        'birth_date',
        'status',
        'city_id',
        'external_id',
        'external_source',
        'external_type',
        'details'
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
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'details' => 'array',
        'email_verified_at' => 'datetime',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'profile_photo_url',
    ];

    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function books()
    {
        return $this->belongsToMany(Book::class, 'book_user_states')->withPivot('status', 'date')->withTimestamps();
    }

    public function book_chapters()
    {
        return $this->belongsToMany(BookChapter::class, 'book_chapter_user_states')->withPivot('status', 'date')->withTimestamps();
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->isAdmin();
    }

    public function isStudent()
    {
        return $this->roles->contains(function ($value) {
            return in_array($value->name, ['student']);
        });
    }

    public function isAdmin()
    {
        return $this->roles->contains(function ($value) {
            return in_array($value->name, ['admin', 'manager', 'teacher']);
        });
    }

    public function profilePhotoDisk()
    {
        return isset($_ENV['VAPOR_ARTIFACT_NAME']) ? 's3' : config('jetstream.profile_photo_disk', 'public');
    }

    public function profilePhotoDirectory()
    {
        return config('jetstrem.profile_photo_directory', 'profile-photos');
    }

    public function getShortNameAttribute()
    {
        $short_name = $this->first_name . ' ' . $this->last_name;
        return trim($short_name) == '' ? $this->name : $short_name;
    }

    public function states(): BelongsToMany
    {
        return $this->belongsToMany(Book::class, 'book_user_logs')->withPivot('date', 'status')->withTimestamps();
    }

    public function attempts()
    {
        return $this->hasMany(Attempt::class);
    }

    public function groups()
    {
        return $this->belongsToMany(Group::class);
    }

    public function quizzes()
    {
        return $this->belongsToMany(Quiz::class);
    }

    public function bookUserStates()
    {
        return $this->hasMany(BookUserState::class);
    }

    public function questions()
    {
        return $this->belongsToMany(Question::class, 'author_question');
    }

    public function getFormattedPhoneAttribute()
    {
        return preg_replace('/^(\d{3})(\d{2})(\d{3})(\d{2})(\d{2})$/', '+$1 $2 $3-$4-$5', $this->phone);
    }

    public function setPhoneAttribute($value)
    {
        $this->attributes['phone'] = preg_replace('/[^0-9]/', '', $value);
    }
}
