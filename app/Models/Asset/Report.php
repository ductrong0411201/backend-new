<?php

namespace App\Models\Asset;

use Illuminate\Database\Eloquent\Model;
use App\Helpers\StringHelper;


/**
 * Class User.
 */
class Report extends Model
{

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table;
    protected $appends = ["display_description"];
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['local_time', 'user_id','construction_id', 'order_id', 'image1', 'image2', 'image3', 'image4','description', 'report_url'];


    public function physicalProgress () {
        return $this->hasOne(PhysicalProgress::class);
    }

    public function order () {
        return $this->belongsTo(Order::class);
    }
    /**
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->table = "reports";
    }

    public function getDisplayDescriptionAttribute() {
        return StringHelper::convertFromSmallCaps($this->description);
    }

    public function images()
    {
        return $this->hasMany(Images::class);
    }

}
