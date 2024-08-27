<?php

namespace App\Models;

use \DateTimeInterface;
use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExpensesCategory extends Model
{
    use SoftDeletes;
    use Auditable;
    use HasFactory;

    public $table = 'expenses_categories';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'name',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function expenses() :HasMany
    {
        return $this->hasMany(Expense::class,'expenses_category_id');
    }

    public function expensesCount($expensesCategoryId, $branchId = null, $date = null)
    {
        $query = $this->expenses()
            ->where('expenses_category_id', $expensesCategoryId);

        if ($branchId) {
            $query->whereHas('account.branch', function ($q) use ($branchId) {
                $q->where('id', $branchId);
            });
        }

        if ($date) {
            $query->whereYear('date', substr($date, 0, 4))
                ->whereMonth('date', substr($date, 5, 2));
        }

        return $query->sum('amount');
    }

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}
