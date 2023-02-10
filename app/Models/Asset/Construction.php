<?php

namespace App\Models\Asset;

use App\Helpers\StringHelper;
use App\Models\Access\User\User;
use Illuminate\Database\Eloquent\Model;

/**
 * Class User.
 */
class Construction extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'constructions';
    protected $appends = ["display_name"];

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function order()
    {
        return $this->hasOne(Order::class);
    }

    public function area()
    {
        return $this->belongsTo(Area::class, 'area_id', 'gid');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function structure()
    {
        return $this->belongsTo(Structure::class);
    }
    public function funding_agency()
    {
        return $this->belongsTo(FundingAgency::class);
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['user_id', 'name', 'latitude', 'longitude', 'structure_id', 'department_id', 'area_id', 'funding_agency_id', 'meta', 'geom'];

    public function getDisplayNameAttribute()
    {
        return StringHelper::convertFromSmallCaps($this->name);
    }
}
