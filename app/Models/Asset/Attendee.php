<?php

namespace App\Models\Asset;

use Illuminate\Database\Eloquent\Model;

/**
 * Class User.
 */
class Attendee extends Model
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
    protected $fillable = ['meeting_id', 'department_id'];

    public function meeting()
    {
        return $this->belongsTo(Meeting::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->table = "attendees";
    }
}
