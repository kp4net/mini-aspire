<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Loans extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'amount', 'term', 'user_id'
    ];

    public function repayments()
    {
        return $this->hasMany(LoanRepayments::class, 'loan_id');
    }
}
