<?php

namespace App\Models\Asset;

use Illuminate\Database\Eloquent\Model;

/**
 * Class FinancialProgress.
 */
class FinancialProgress extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'project_financial_progresses';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['project_id', 'shared_by', 'instalment', 'amount', 'sanction_date', 'release_date', 'uc_status', 'uc_documents', 'uc_date', 'cc_status', 'cc_documents', 'cc_date', 'handed_over', 'handed_over_date', 'taken_over', 'taken_over_date'];


    protected $casts = [
        'uc_documents' => 'array',
        'cc_documents' => 'array'
    ];

    public function setUcDocumentsAttribute($value)
    {
        if (gettype($value) === 'array') {
            $this->attributes['uc_documents'] = json_encode($value, false);
        } else {
            $this->attributes['uc_documents'] = $value;
        }
    }

    public function setCcDocumentsAttribute($value)
    {
        if (gettype($value) === 'array') {
            $this->attributes['cc_documents'] = json_encode($value, false);
        } else {
            $this->attributes['cc_documents'] = $value;
        }
    }
}
