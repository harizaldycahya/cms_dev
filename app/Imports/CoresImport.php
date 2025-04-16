<?php

namespace App\Imports;

use App\Models\Core;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;

class CoresImport implements ToModel, WithStartRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */

    public function __construct(array $params) 
    {
        $this->params = $params;
    }

    public function startRow(): int
    {
        return 10;
    }

    public function model(array $row)
    {
        return new Core([
            'project_id' => $this->params[0],
            'segment_id' => $this->params[1],
            'section_id' => $this->params[2],
            'tube' => $row[1],
            'core' => $row[2],
            'customers' => $row[3],
            'total_loss_db' => $row[4],
            'end_cable' => $row[5],
            'loss_db_km' => $row[6],
            'remarks' => $row[7],
        ]);
    }
}
