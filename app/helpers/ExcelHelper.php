<?php

namespace app\helpers;


use PHPExcel;
use PHPExcel_Style_Alignment;
use PHPExcel_Style_Fill;

class ExcelHelper
{

    public static function setBackground(PHPExcel $objPHPExcel, $coordinates, $color)
    {

        if (is_array($coordinates)) {
            foreach ($coordinates as $item) {
                $objPHPExcel->getActiveSheet()->getStyle($item)
                    ->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
                $objPHPExcel->getActiveSheet()->getStyle($item)
                    ->getFill()->getStartColor()->setARGB($color);
            }

        } else {
            $objPHPExcel->getActiveSheet()->getStyle($coordinates)
                ->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
            $objPHPExcel->getActiveSheet()->getStyle($coordinates)
                ->getFill()->getStartColor()->setARGB($color);
        }

    }

    public static function setAutoSize(PHPExcel $objPHPExcel, $coordinates)
    {
        if (is_array($coordinates)) {
            foreach ($coordinates as $item) {
                $objPHPExcel->getActiveSheet()->getColumnDimension($item)->setAutoSize(true);
            }

        } else {
            $objPHPExcel->getActiveSheet()->getColumnDimension($coordinates)->setAutoSize(true);
        }

    }

    public static function setHorizontalAlign(PHPExcel $objPHPExcel, $coordinates, $align)
    {
        if (is_array($coordinates)) {
            foreach ($coordinates as $item) {
                $objPHPExcel->getActiveSheet()->getStyle($item)->getAlignment()->setHorizontal($align);
            }

        } else {
            $objPHPExcel->getActiveSheet()->getStyle($coordinates)->getAlignment()->setHorizontal($align);
        }

    }

    public static function setVerticalAlign(PHPExcel $objPHPExcel, $coordinates, $align)
    {
        if (is_array($coordinates)) {
            foreach ($coordinates as $item) {
                $objPHPExcel->getActiveSheet()->getStyle($item)->getAlignment()->setVertical($align);
            }

        } else {
            $objPHPExcel->getActiveSheet()->getStyle($coordinates)->getAlignment()->setVertical($align);
        }

    }

    public static function setRow(PHPExcel $objPHPExcel, $num, $status, $rowName, $option1, $option2)
    {
        $objPHPExcel->getActiveSheet()->mergeCells("A{$num}:E{$num}");

        ExcelHelper::setBackground($objPHPExcel, [
            "A{$num}",
        ], 'eeeeee');


        $num1 = $num + 1;
        $num2 = $num + 2;


        $objPHPExcel->getActiveSheet()->mergeCells("A{$num1}:A{$num2}");
        $objPHPExcel->getActiveSheet()->mergeCells("B{$num1}:B{$num2}");
        $objPHPExcel->getActiveSheet()->mergeCells("C{$num1}:C{$num2}");

        $objPHPExcel->getActiveSheet()->setCellValue("A{$num1}", '1');
        $objPHPExcel->getActiveSheet()->setCellValue("B{$num1}", $rowName);


        if($status)
        {
            $objPHPExcel->getActiveSheet()->setCellValue("C{$num1}", 'Ок');
            self::setBackground($objPHPExcel, "C{$num1}", '93c47d');
        } else {
            $objPHPExcel->getActiveSheet()->setCellValue("C{$num1}", 'Ошибка');
            self::setBackground($objPHPExcel, "C{$num1}", 'e06666');
        }


        $objPHPExcel->getActiveSheet()->setCellValue("D{$num1}", 'Состояние');
        $objPHPExcel->getActiveSheet()->setCellValue("D{$num2}", 'Рекомендации');

        $objPHPExcel->getActiveSheet()->setCellValue("E{$num1}", $option1);
        $objPHPExcel->getActiveSheet()->setCellValue("E{$num2}", $option2);


        self::setHorizontalAlign($objPHPExcel, [
            "A{$num1}",
            "C{$num1}",
        ], PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        self::setVerticalAlign($objPHPExcel, [
            "A{$num1}",
            "B{$num1}",
            "C{$num1}",
        ], PHPExcel_Style_Alignment::VERTICAL_CENTER);

    }

}