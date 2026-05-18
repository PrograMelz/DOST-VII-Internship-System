<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemSetting extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'system_settings';

    /**
     * Indicates if the model should be timestamped.
     *
     * The table only has an updated_at column that is managed manually
     * in the migration, so we disable automatic timestamps here.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'system_short_name',
        'system_long_name',
        'primary_color',
        'secondary_color',
        'button_color',
        'heading_text_color',
        'body_text_color',
        'system_logo',
    ];
}

