<?php

namespace App\Models\Asset;

use Illuminate\Database\Eloquent\Model;
/**
 * Class User.
 */
class Structure extends Model
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
    protected $fillable = ['order', 'name', 'meta'];

    /**
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->table = "structures";
    }
}
