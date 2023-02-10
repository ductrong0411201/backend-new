<?php

namespace App\Models\Asset;

use Illuminate\Database\Eloquent\Model;
/**
 * Class User.
 */
class FundingAgency extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'funding_agencies';

    protected $fillable = ['name', 'group_id', 'full_name','marker_color', 'src'];

    public function group () {
        return $this->belongsTo(FundingGroup::class, 'group_id', 'id');
    }

    public function constructions () {
        return $this->hasMany(Construction::class);
    }

    public function stateProjects () {
        return $this->belongsToMany(Project::class, 'project_state_funding_agency');
    }

    public function centralProjects () {
        return $this->belongsToMany(Project::class, 'project_state_funding_agency');
    }
}
