<?php

namespace App\Exports;

use App\Models\Core;
use Maatwebsite\Excel\Concerns\FromArray;

class CoresExport implements FromArray
{
    protected $cores;

    public function __construct(array $cores)
    {
        $this->cores = $cores;
    }

    public function array(): array
    {
        return $this->cores;
    }
}
