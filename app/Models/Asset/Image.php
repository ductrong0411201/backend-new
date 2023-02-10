<?php

namespace App\Models\Asset;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    use HasFactory;

    protected $fillable = [
        'report_id',
        'location',
        'image1',
        'image2',
        'image3',
        'image4',
    ];

    public function report()
    {
        return $this->belongsTo(Report::class);
    }
}
