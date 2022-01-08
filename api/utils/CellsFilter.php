<?php
class CellsFilter implements \PhpOffice\PhpSpreadsheet\Reader\IReadFilter
{
    private $availableCells = [];

    public function readCell($column, $row, $worksheetName = '') {
        if(!isset($this->availableCells[$column])) $this->availableCells[$column] = [];
        $this->availableCells[$column][] = $row;
        // if ($row >= 1 && $row <= 7) {
        //     if (in_array($column,range('A','E'))) {
        //         return true;
        //     }
        // }
        return true;
    }

    public function getAvailableCells(){
        return $this->availableCells;
    }

    public function getRowCount(){
        $columnCounts = [];
        foreach($this->availableCells as $rows){
            $columnCounts[] = count($rows);
        }
        sort($columnCounts);
        return count($columnCounts) ? $columnCounts[count($columnCounts) - 1] : 0;
    }
}