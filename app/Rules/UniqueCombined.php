<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\DB;

class UniqueCombined implements ValidationRule
{
    private $table_name;
    private $columns;
    private $getValue;
    private $ignore_id;
    private $attribute;

    public function __construct($table_name, $columns, $getValue, $ignore_id = null, $attribute = ":attribute")
    {
        $this->table_name = $table_name;
        $this->columns = $columns;
        $this->getValue = $getValue;
        $this->ignore_id = $ignore_id;
        $this->attribute = $attribute;
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $row = DB::table($this->table_name);

        if (!is_null($this->ignore_id)) {
            $row = $row->where('id', '<>', $this->ignore_id);
        }

        foreach ($this->columns as $column) {
            $row = $row->where($column, $this->getValue->__invoke($column));
        }

        $row = $row->count();

        if ($row > 0) {
            $fail("This $this->attribute must be unique");
        }
    }
}
