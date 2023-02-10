<?php

namespace App\Models\Asset;

use App\Models\Access\User\User;
use Illuminate\Database\Eloquent\Model;

/**
 * Class User.
 */
class Project extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'projects';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['user_id', 'uuid', 'order_id', 'estimated_cost', 'currency' ,'central_share', 'state_share', 'block', 'project_gist', 'remarks', 'src', 'date_of_completion','date_of_actual'];


    public function centralFundingAgencies()
    {
        return $this->belongsToMany(FundingAgency::class, 'project_central_funding_agency');
    }

    public function stateFundingAgencies()
    {
        return $this->belongsToMany(FundingAgency::class, 'project_state_funding_agency');
    }

    public function updateLogs()
    {
        return $this->belongsToMany(User::class, 'project_user_update');
    }

    public function financialProgresses()
    {
        return $this->hasMany(FinancialProgress::class, 'project_id', 'id');
    }

    public function order () {
        return $this->belongsTo(Order::class);
    }

    public function user () {
        return $this->belongsTo(User::class);
    }
}
