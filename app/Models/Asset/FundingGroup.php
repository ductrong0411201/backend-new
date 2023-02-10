<?php

namespace App\Models\Asset;

use Illuminate\Database\Eloquent\Model;
/**
 * Class User.
 */
class FundingGroup extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'funding_groups';

    public function FundingAgencies () {
        return $this->hasMany(FundingAgency::class, 'group_id', 'id');
    }
}
