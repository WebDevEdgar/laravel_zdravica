<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AmoCrmLead extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $connection = 'sqlsrv1';
    protected $table = 'master.guest.amocrm_lead';
}
