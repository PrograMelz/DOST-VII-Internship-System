<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Intern extends Model
{
    use HasFactory;

    public $timestamps = false;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'username',
        'password_hash',
        'qr_code',
        'att_code',
        'first_name',
        'middle_name',
        'last_name',
        'school_id',
        'school_name',
        'email',
        'phone_number',
        'birthday',
        'sex',
        'ojt_hours_required',
        'created_at',
    ];

    /**
     * @return HasMany<AttendanceLog, $this>
     */
    public function attendanceLogs(): HasMany
    {
        return $this->hasMany(AttendanceLog::class);
    }

    public function getFullNameAttribute(): string
    {
        $middle = trim((string) $this->middle_name);

        return trim($this->first_name.' '.($middle !== '' ? $middle.' ' : '').$this->last_name);
    }
}

