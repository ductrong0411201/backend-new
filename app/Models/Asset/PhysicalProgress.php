<?php

namespace App\Models\Asset;

use Illuminate\Database\Eloquent\Model;

/**
 * Class PhysicalProgress.
 */
class PhysicalProgress extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'project_physical_progresses';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['status', 'physical_percent', 'financial_percent',  'cc_status', 'cc_documents', 'cc_date' ,'report_id'];
    protected $casts = [
        'cc_documents' => 'array'
    ];

    public function setCcDocumentsAttribute($value)
    {
        if (gettype($value) === 'array') {
            $this->attributes['cc_documents'] = json_encode($value, false);
        } else {
            $this->attributes['cc_documents'] = $value;
        }
    }
}
