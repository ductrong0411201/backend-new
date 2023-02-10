<?php

namespace App\Models\Asset;

use Illuminate\Database\Eloquent\Model;

class PublicProject extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'public_projects';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'construction_id', 'no_of_beds', 'plinth_area', 'name_of_the_firm', 'emails', 'name_of_ce_pwd', 'name_of_circle', 'name_of_division', 'head_of_account', 'physical_progress_status', 'remark', 'phase'
    ];

    protected $casts = [
        'emails' => 'array',
        'plinth_area' => 'float'
    ];

    public function construction() {
        return $this->belongsTo(Construction::class);
    }
}
