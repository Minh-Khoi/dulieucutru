<?php

require_once dirname(__FILE__, 2) . "/models/action.php";
require_once dirname(__FILE__, 2) . "/models/hogiadinh.php";
require_once dirname(__FILE__, 2) . "/models/input.php";
require_once dirname(__FILE__, 2) . "/models/nhankhau.php";
include dirname(__FILE__, 1) . "/global.php";


print_r("<pre>");

$action = new input();


$list_of_hogiadinh = ($action->list_hogiadinh);
$hodautien = $list_of_hogiadinh[0];


require dirname(__FILE__, 2) . '/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

$sheet->setCellValue("A1", "Số HSHK");
$sheet->setCellValue("B1", "Số sổ hộ khẩu");
$sheet->setCellValue("C1", "tên chủ hộ");
$sheet->setCellValue("D1", "tên người ký");
$sheet->setCellValue("E1", "số nhân khẩu trong hộ");
$sheet->setCellValue("F1", "TDP cũ");
$sheet->setCellValue("G1", "TDP mới");
$sheet->setCellValue("H1", "Thông tin phúc tra");

foreach ($list_of_hogiadinh as $k => $ho) {
  $sheet->setCellValue("A" . ($k + 2), $ho->so_hshk);
  $sheet->setCellValue("B" . ($k + 2), $ho->so_seri);
  $sheet->setCellValue("C" . ($k + 2), $ho->ten_chuho);
  $sheet->setCellValue("D" . ($k + 2), $ho->nguoi_ky_ten);
  $sheet->setCellValue("E" . ($k + 2), $ho->so_nhankhau);
  $sheet->setCellValue("F" . ($k + 2), $ho->tdp_cu);
  $sheet->setCellValue("G" . ($k + 2), $ho->tdp_moi);
  $sheet->setCellValue("H" . ($k + 2), $ho->thongtin_phuctra);
}

$writer = new Xlsx($spreadsheet);
$writer->save(dirname(__FILE__, 2) . "/output/file.xlsx");
// header("Location: ../output/file.xlsx");
print_r("</pre>");