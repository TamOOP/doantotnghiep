<?php

namespace App\Models;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Model implements Authenticatable
{
    use HasFactory;
    public $timestamps = false;

    public function courseTeaching(): HasMany
    {
        return $this->hasMany(Course::class)->where('status', '1');
    }

    public function courses(): BelongsToMany
    {
        return $this->belongsToMany(Course::class, 'enrolments', 'user_id', 'course_id')
            ->withPivot('enrol_date', 'last_access')
            ->where('status', '1');
    }

    public function attempts() : HasMany {
        return $this->hasMany(Attempt::class);
    }

    public function exams() : BelongsToMany {
        return $this->belongsToMany(Examination::class, 'attempts', 'user_id', 'exam_id');
    }

    public function activitie() : BelongsToMany {
        return $this->belongsToMany(Activity::class, 'processes', 'user_id', 'activity_id')
            ->withPivot('marked');
    }

    public function banks() : HasMany {
        return $this->hasMany(Bank::class);
    }

    public function transfers() : HasMany {
        return $this->hasMany(Transfer::class);
    }

    public function getAuthIdentifierName()
    {
        return $this->primaryKey;
    }

    /**
     * Get the unique identifier for the user.
     *
     * @return mixed
     */
    public function getAuthIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Get the password for the user.
     *
     * @return string
     */
    public function getAuthPassword()
    {
        return $this->password;
    }

    /**
     * Get the remember token for the user.
     *
     * @return string|null
     */
    public function getRememberToken()
    {
        return $this->remember_token;
    }

    /**
     * Set the remember token for the user.
     *
     * @param  string  $value
     * @return void
     */
    public function setRememberToken($value)
    {
        $this->remember_token = $value;
    }

    /**
     * Get the column name for the "remember me" token.
     *
     * @return string
     */
    public function getRememberTokenName()
    {
        return 'remember_token';
    }
}
