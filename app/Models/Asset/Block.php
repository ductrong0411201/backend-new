<?php

namespace App\Models\Asset;

use Illuminate\Database\Eloquent\Model;
/**
 * Class User.
 */
class Block extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table;

    /**
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->table = "blocks";
    }
}
