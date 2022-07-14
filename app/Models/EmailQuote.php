<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailQuote extends Model
{
    use HasFactory;
    protected $table = 'email_quote_send';
    protected $guarded = ['id'];
    public $timestamps = false;

}
