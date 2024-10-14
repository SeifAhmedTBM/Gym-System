<?php

namespace App\Http\Helpers;

use App\Models\Account;
use Illuminate\Support\Str;

class ModelScope
{

    // Get the filtered request data
    public static function filter($data, $model_name)
    {
        $query = $model_name::query();
        foreach ($data as $field => $value) {
            if ($value != NULL) {
                $field = str_replace('amp;', '', $field);
                if ($field == 'relations') {
                    foreach ($value as $relation => $fields) {
                        if (Str::contains($relation, '.')) {
                            $relations = explode('.', $relation);
                            foreach ($fields as $field_name => $field_value) {
                                if ($field_name == 'member_code') {
                                    if (!is_null($field_value)) {
                                        $query->whereHas($relations[0], function ($q) use ($relations, $field_name, $field_value) {
                                            $q = $q->whereHas($relations[1], function ($rq) use ($field_name, $field_value) {
                                                $rq = $rq->where($field_name, $field_value);
                                            });
                                        });
                                    }
                                } elseif ($field_name == 'gender') {
                                    $query->whereHas($relations[0], function ($q) use ($relations, $field_name, $field_value) {
                                        $q = $q->whereHas($relations[1], function ($rq) use ($field_name, $field_value) {
                                            $rq = $rq->where($field_name, $field_value);
                                        });
                                    });
                                } else {
                                    $query->whereHas($relations[0], function ($q) use ($relations, $field_name, $field_value) {
                                        $q = $q->whereHas($relations[1], function ($rq) use ($field_name, $field_value) {
                                            if (!is_array($field_value)) {
                                                $rq = $rq->where($field_name, 'LIKE', '%' . $field_value . '%');
                                            }else {
                                                if (!is_null($field_value)) {
                                                    $rq = $rq->whereIn($field_name, $field_value);
                                                }
                                            }
                                        });
                                    });
                                }
                            }
                        } else {
                            foreach ($fields as $field_name => $field_value) {
                                // dd($field_name,$field_value);
                                if ($field_name == 'member_code') {
                                    if (!is_null($field_value)) {
                                        $query->whereHas($relation, function ($q) use ($field_name, $field_value) {
                                            $q = $q->where($field_name, $field_value);
                                        });
                                    }
                                } elseif ($field_name == 'gender') {
                                    $query->whereHas($relation, function ($q) use ($field_name, $field_value) {
                                        $q = $q->where($field_name, $field_value);
                                    });
                                } elseif ($field_name == 'account_id') {
                                    $accounts = ['', 'null', 'instapay', 'cash', 'visa', 'vodafone', 'valu', 'premium', 'sympl'];
                                    if (in_array($field_value[0], array_values($accounts))) {
                                        if ($field_value[0] == '' || $field_value[0] == 'null') {
                                            $field_value = Account::pluck('id');
                                        } else {
                                            $field_value = Account::where('name', 'like', '%' . $field_value[0] . '%')->pluck('id');
                                        }
                                    }
                                    $query->whereHas($relation, function ($q) use ($field_name, $field_value) {
                                        $q = $q->whereIn($field_name, $field_value);
                                    });
                                } else {
                                    if (gettype($field_value) == 'array' && isset($field_value['from']) && isset($field_value['to'])) {
                                        $query->whereHas($relation, function ($q) use ($field_name, $field_value) {
                                            $q = $q->whereDate($field_name, '>=', $field_value['from'])
                                                ->whereDate($field_name, '<=', $field_value['to']);
                                        });
                                    } elseif (gettype($field_value) == 'array' && !isset($field_value['from']) && !isset($field_value['to'])) {
                                        $query->whereHas($relation, function ($q) use ($field_name, $field_value) {
                                            $q = $q->WhereIn($field_name, $field_value);
                                        });
                                    } else {
                                        if (!is_null($field_value)) {
                                            $query->whereHas($relation, function ($q) use ($field_name, $field_value) {
                                                $q = $q->Where($field_name, $field_value);
                                            });
                                        }
                                    }
                                }

                            }
                        }
                    }
                }

                if ($field == 'filter_by') {
                    $query->whereMonth('created_at', date('m'));
                }

                if (intval($value) > 0 && $field != 'relations' && $field != 'is_reviewed' && gettype($value) != 'array') {
                    $query->where($field, $value);
                }

                if (gettype($value) != 'array' && gettype($value) != 'integer' && $value != 'is_reviewed' && $value != 'not_reviewed') {
                    $query->where($field, 'LIKE', "%" . $value . "%");
                }

                if (gettype($value) == 'array' && $field != 'relations' && gettype($value) != 'integer') {
                    if ($field != 'created_at' && $field != 'start_date' && $field != 'end_date' && $field != 'due_date' && $field != 'date') {
                        $query->whereIn($field, $value);
                    }
                }

                if ($value == 'is_reviewed' || $value == 'not_reviewed') {
                    $value == 'is_reviewed' ? $query->where('is_reviewed', true) : $query->where('is_reviewed', false);
                }

                if (isset($value['from']) && !isset($value['to'])) {
                    $query->whereDate($field, '>=', $value['from']);
                }

                if (isset($value['to']) && !isset($value['from'])) {
                    $query->whereDate($field, '<=', $value['to']);
                }

                if (isset($value['from']) && isset($value['to'])) {
                    $query->whereDate($field, '>=', $value['from'])->whereDate($field, '<=', $value['to']);
                }

            }
        }


        return $query;
    }


}