<?php

namespace App\Models\Asset;

use Illuminate\Database\Eloquent\Model;

/**
 * Class User.
 */
class Meeting extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['title', 'description', 'created_by', 'start_date', 'note', 'files','readed'];


    public function attendees () {
        return $this->hasMany(Attendee::class);
    }
    

    protected $casts = [
        'files' => 'array'
    ];

    /**
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->table = "meetings";
    }

    public function setFilesAttribute($value)
    {
        if (gettype($value) === 'array') {
            $this->attributes['files'] = json_encode($value, false);
        } else {
            $this->attributes['files'] = $value;
        }

    }
}
