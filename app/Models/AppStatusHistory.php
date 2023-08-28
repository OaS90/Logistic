<?php

namespace App\Models;

use App\Domain\ApplicationStatuses;
use Illuminate\Database\Eloquent\Model;

class AppStatusHistory extends Model
{
    use ApplicationStatuses;

    /**
     * @var string
     */
    protected $table = 'application_status_history';
    /**
     * @var string[]
     */
    protected $guarded = ['id'];
    /**
     * @var bool
     */
    public $timestamps = false;

    protected $primaryKey = 'id';
}
