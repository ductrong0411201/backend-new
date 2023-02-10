<?php

namespace App\Models\Asset;

use App\Models\Access\User\User;
use Illuminate\Database\Eloquent\Model;

/**
 * Class User.
 */
class Department extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table;

    public function attendees()
    {
        return $this->hasMany(Attendee::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'meta'];

    /**
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->table = "departments";
    }
}
