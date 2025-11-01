<?php

namespace App\Models;

use App\Enums\LoanStatus;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Loan extends Model
{
    use HasFactory;

    protected $appends = ['day'];

    protected $fillable = [
        'loan_no',
        'txn_id',
        'loan_plan_id',
        'user_id',
        'amount',
        'submitted_data',
        'status',
    ];

    protected $casts = [
        'status' => LoanStatus::class,
        'amount' => 'int',
    ];

    public function scopeSearch($query, $search)
    {
        if ($search) {
            return $query->where(function ($query) use ($search) {
                $query->whereHas('user', function ($query) use ($search) {
                    $query->where('username', 'like', '%'.$search.'%');
                })->orWhereHas('plan', function ($query) use ($search) {
                    $query->where('name', 'like', '%'.$search.'%');
                });
            });
        }

        return $query;
    }

    public function getDayAttribute(): string
    {
        return Carbon::parse($this->attributes['created_at'])->format('d M');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function plan()
    {
        return $this->hasOne(LoanPlan::class, 'id', 'loan_plan_id');
    }

    public function scopeDue($query)
    {
        return $query->where('status', LoanStatus::Due->value);
    }

    public function scopeRunning($query)
    {
        return $query->where('status', LoanStatus::Running->value);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', LoanStatus::Completed->value);
    }

    public function scopeReviewing($query)
    {
        return $query->where('status', LoanStatus::Reviewing->value);
    }

    public function scopeRejected($query)
    {
        return $query->where('status', LoanStatus::Rejected->value);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(LoanTransaction::class);
    }

    public function getTotalLoanAmountAttribute()
    {
        return $this->transactions->sum('given_amount');
    }

    public function getLastDateAttribute()
    {
        return $this->transactions->last()->created_at;
    }

    public function givenInstallemnt()
    {
        return $this->transactions->whereNotNull('given_date')->count();
    }

    public function totalPayableAmount()
    {
        $interestAmount = $this->amount / 100 * $this->plan->per_installment;

        $fullAmountWithInterest = $interestAmount + $this->amount;

        return round($fullAmountWithInterest, 2);
    }

    public function perInstallment()
    {
        $totalAmount = $this->totalPayableAmount();

        $perInstallment = ($totalAmount / $this->plan->total_installment);

        return round($perInstallment, 2);
    }
}
