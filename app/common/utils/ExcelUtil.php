<?php

namespace app\common\utils;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ExcelUtil
{
    public static function export(string $title, array $headers, array $rows): string
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle($title);

        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . '1', $header);
            $col++;
        }

        $rowNum = 2;
        foreach ($rows as $row) {
            $col = 'A';
            foreach ($row as $value) {
                $sheet->setCellValue($col . $rowNum, $value);
                $col++;
            }
            $rowNum++;
        }

        $writer = new Xlsx($spreadsheet);
        $filePath = sys_get_temp_dir() . '/' . uniqid($title . '_') . '.xlsx';
        $writer->save($filePath);

        return $filePath;
    }

    public static function import(string $filePath): array
    {
        $spreadsheet = IOFactory::load($filePath);
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray();

        $headers = array_shift($rows);
        $result = [];
        foreach ($rows as $row) {
            $item = [];
            foreach ($headers as $i => $header) {
                $item[$header] = $row[$i] ?? null;
            }
            $result[] = $item;
        }

        return $result;
    }
}
