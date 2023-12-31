<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class amocrmIDs extends Model
{
    use HasFactory;
    public $timestamps = false;
    public $connection = 'sqlsrv1';
    protected $table = 'master.guest.amocrm_ids';
    protected $fillable = ['amoContactID', 'amoBillID', 'amoLeadID', 'leadDBId'];
}
