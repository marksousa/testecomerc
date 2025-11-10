<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

// #[UseFactory(CustomerFactory::class)]
class Customer extends Model
{
    use hasFactory;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'birthdate',
        'address',
        'address_line_two',
        'neighborhood',
        'zip_code',
    ];
}
