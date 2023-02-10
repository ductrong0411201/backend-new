<?php

namespace App\Models\Asset;

use App\Helpers\StringHelper;
use Illuminate\Database\Eloquent\Model;

/**
 * Class User.
 */
class Order extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'orders';
    protected $appends = ["display_name"];
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'construction_id', 'meta'];

    public function construction () {
        return $this->belongsTo(Construction::class);
    }

    public function reports () {
        return $this->hasMany(Report::class)->orderBy('local_time');;
    }

    public function report () {
        return $this->hasOne(Report::class)->latest('local_time');
    }

    public function getDisplayNameAttribute() {
        return StringHelper::convertFromSmallCaps($this->name);
    }
}
