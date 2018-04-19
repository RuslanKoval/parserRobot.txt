<?php

namespace app\controllers;

use app\helpers\ExcelHelper;
use app\helpers\UrlHelper;
use app\services\parser\Parser;
use core\Controller;
use PHPExcel;
use PHPExcel_IOFactory;
use PHPExcel_RichText;
use PHPExcel_Style_Alignment;
use PHPExcel_Style_Border;
use PHPExcel_Style_Color;
use PHPExcel_Style_Fill;


class ApplicationController extends Controller
{

    public function indexAction()
    {

    }

    public function testAction()
    {
        $site = $this->getRequest()->getParam('site');
        $url = UrlHelper::parseUrl($site);

        $parser = new Parser($url);
        $this->view->site = $url;
        $this->view->parser = $parser;

        $_SESSION['url'] = $url;
    }

    public function excelAction()
    {
        $url = $_SESSION['url'];
        if(!$url)
            return false;

        $parser = new Parser($url);

        $objPHPExcel = new PHPExcel();

        $objPHPExcel->getProperties()->setCreator("Maarten Balliauw")
            ->setLastModifiedBy("Maarten Balliauw")
            ->setTitle("Office 2007 XLSX Test Document")
            ->setSubject("Office 2007 XLSX Test Document")
            ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
            ->setKeywords("office 2007 openxml php")
            ->setCategory("Test result file");


        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', '№')
            ->setCellValue('B1', 'Название проверки')
            ->setCellValue('C1', 'Статус')
            ->setCellValue('D1', '')
            ->setCellValue('E1', 'Текущее состояние');


        ExcelHelper::setAutoSize($objPHPExcel, [
            'D',
            'E'
        ]);

        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(60);

        ExcelHelper::setBackground($objPHPExcel, [
            'A1',
            'B1',
            'C1',
            'D1',
            'E1'
        ], 'a2c4c9');

        ExcelHelper::setHorizontalAlign($objPHPExcel, [
            'A1',
            'B1',
            'C1',
            'D1',
            'E1'
        ], PHPExcel_Style_Alignment::HORIZONTAL_CENTER);


        ExcelHelper::setRow($objPHPExcel, '2', $parser->getContent(), 'Проверка наличия файла robots.txt', $parser->fileError(), $parser->fileRecommendation());

        ExcelHelper::setRow($objPHPExcel, '5', $parser->getHostDirective(), 'Проверка указания директивы Host', $parser->hostError(), $parser->hostRecommendation());
        ExcelHelper::setRow($objPHPExcel, '8', $parser->getHostDirective(), 'Проверка количества директив Host, прописанных в файле', $parser->hostCountError(), $parser->hostCountRecommendation());

        ExcelHelper::setRow($objPHPExcel, '11', $parser->checkFileSize(), 'Проверка размера файла robots.txt', $parser->sizeError(), $parser->sizeRecommendation());
        ExcelHelper::setRow($objPHPExcel, '14', $parser->getSitemaps(), 'Проверка указания директивы Sitemap', $parser->siteMapError(), $parser->siteMapRecommendation());

        ExcelHelper::setRow($objPHPExcel, '17', $parser->getStatusCode(), 'Проверка кода ответа сервера для файла robots.txt', $parser->statusCodeError(), $parser->statusCodeRecommendation());



        $objPHPExcel->getActiveSheet()->setTitle('Simple');
        $objPHPExcel->setActiveSheetIndex(0);




        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'.$url.'.xlsx"');
        header('Cache-Control: max-age=0');

        header('Cache-Control: max-age=1');

        header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
        header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
        header ('Cache-Control: cache, must-revalidate');
        header ('Pragma: public');
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        exit;

    }

}