<?php
namespace App\Models;

use Laravel\Sanctum\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Users extends Authenticatable
{
    use HasFactory, HasApiTokens;

    protected $primaryKey = 'user_id';
    protected $table = 'user';
    protected $fillable = ['company_name_id', 'type', 'name', 'contact_no1'];
}
