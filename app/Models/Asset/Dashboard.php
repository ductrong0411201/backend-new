<?php

namespace App\Models\Asset;

use Illuminate\Database\Eloquent\Model;

class Dashboard extends Model
{
    protected $table = 'dashboard';

    public function centralFundingAgencies()
    {
        return $this->belongsToMany(FundingAgency::class, 'project_central_funding_agency', 'project_id', 'funding_agency_id');
    }

    public function stateFundingAgencies()
    {
        return $this->belongsToMany(FundingAgency::class, 'project_state_funding_agency', 'project_id', 'funding_agency_id');
    }
}
