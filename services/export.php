<?php
if(!defined('_VALID_SETTING')){define( '_VALID_SETTING', 0x0001 );}

//error_reporting(E_ALL ^ E_DEPRECATED);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);

require_once( 'module/applicationSettings.php' );
require_once 'excel/PHPExcel.php';
include_once( ABSPATH. CONNECTION. 'register.php' );

$conn = new Register();

global $config;

//query data base on criteria --------------------------------------------------------------------
$data = get_param( 'GET' );


$result = $conn->get_report( $data['startdate'] );

if( empty($result) ) return;

$default_border = array(
    'style' => PHPExcel_Style_Border::BORDER_THIN,
    'color' => array('rgb'=>'#003')
);
$headerArray = array(
    'borders' => array(
        'bottom' => $default_border,
        'left' => $default_border,
        'top' => $default_border,
        'right' => $default_border,
    ),
    'fill' => array(
        'type' => PHPExcel_Style_Fill::FILL_SOLID,
        'color' => array('rgb'=>'EEEEEE'),
    )
);
$headerArray2 = array(
    'fill' => array(
        'type' => PHPExcel_Style_Fill::FILL_SOLID,
        'color' => array('rgb'=>'6F6F6F'),
    )
);
$headerArray3 = array(
    'borders' => array(
        'bottom' => $default_border,
        'left' => $default_border,
        'top' => $default_border,
        'right' => $default_border,
    ),
    'fill' => array(
        'type' => PHPExcel_Style_Fill::FILL_SOLID,
        'color' => array('rgb'=>'E4EAF4'),
    )
);
$styleArray = array(
       'borders' => array(
             'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                    'color' => array('argb' => PHPExcel_Style_Color::COLOR_BLACK),
             ),
       )
);

//-------- Format Data ---------------------------------------------------------------------------


//--- create excel file -------------------------------------------------------------------------
$objPHPExcel = new PHPExcel();
//Set document properties
$objPHPExcel->getProperties()->setCreator("Huawei")
							 ->setLastModifiedBy("Huawei")
							 ->setTitle("Customer Register Daily Report");
						 
//Set active sheet	
$objPHPExcel->setActiveSheetIndex(0);
$active = $objPHPExcel->getActiveSheet();
$active->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$active->getDefaultStyle()->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$active->getStyle('1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$active->getRowDimension('1')->setRowHeight(25);

$startRow = 1;
$startCol = 1;

//header section -------------------------------------------------------------------------------------
//Report name
$active->setCellValue('A'.$startRow, 'ลำดับ' );
$active->setCellValue('B'.$startRow, 'IMEI' );
$active->setCellValue('C'.$startRow, 'คำนำหน้า' );
$active->setCellValue('D'.$startRow, 'ชื่อ-นามสกุล' );
$active->setCellValue('E'.$startRow, 'เลขบัตรประชาชน' );
$active->setCellValue('F'.$startRow, 'ประเภทที่อยู่' );
$active->setCellValue('G'.$startRow, 'ที่อยู่' );
$active->setCellValue('H'.$startRow, 'ถนน' );
$active->setCellValue('I'.$startRow, 'ซอย' );
$active->setCellValue('J'.$startRow, 'แขวง ตำบล' );
$active->setCellValue('K'.$startRow, 'เขต อำเภอ' );
$active->setCellValue('L'.$startRow, 'จังหวัด' );
$active->setCellValue('M'.$startRow, 'ประเทศ' );
$active->setCellValue('N'.$startRow, 'รหัสไปรษณีย์' );
$active->setCellValue('O'.$startRow, 'เบอร์มือถือ' );
$active->setCellValue('P'.$startRow, 'อีเมล์' );
$active->setCellValue('Q'.$startRow, 'รุ่น' );
$active->setCellValue('R'.$startRow, 'สี' );
$active->setCellValue('S'.$startRow, 'ร้านที่ซื้อ' );
$active->setCellValue('T'.$startRow, 'ชื่อร้านอื่นๆ' );
$active->setCellValue('U'.$startRow, 'หลักฐาน' );
$active->setCellValue('V'.$startRow, 'หลักฐาน Passport' );

$active->getColumnDimension('B')->setWidth(18);
$active->getColumnDimension('C')->setWidth(10);
$active->getColumnDimension('D')->setWidth(13);
$active->getColumnDimension('E')->setWidth(20);
$active->getColumnDimension('F')->setWidth(30);
$active->getColumnDimension('G')->setWidth(60);
$active->getColumnDimension('H')->setWidth(20);
$active->getColumnDimension('I')->setWidth(15);
$active->getColumnDimension('J')->setWidth(20);
$active->getColumnDimension('K')->setWidth(20);
$active->getColumnDimension('L')->setWidth(20);
$active->getColumnDimension('M')->setWidth(15);
$active->getColumnDimension('N')->setWidth(15);
$active->getColumnDimension('O')->setWidth(30);
$active->getColumnDimension('P')->setWidth(30);
$active->getColumnDimension('Q')->setWidth(30);
$active->getColumnDimension('R')->setWidth(10);
$active->getColumnDimension('S')->setWidth(15);
$active->getColumnDimension('T')->setWidth(30);
$active->getColumnDimension('U')->setWidth(30);
$active->getColumnDimension('V')->setWidth(30);
$active->getColumnDimension('W')->setWidth(30);
//$active->getStyle('B')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);


$startRow++;
$counter = 1;

foreach ( $result as $item ){
    $active->setCellValue('A'.$startRow, $counter++  );
    $active->setCellValue('B'.$startRow, $item['IMEI'] );
    $active->setCellValue('C'.$startRow, $item['sex'] );
    $active->setCellValue('D'.$startRow, $item['name'] );
    $active->setCellValue('E'.$startRow, $item['identity'] );
    $active->setCellValue('F'.$startRow, $item['type'] );
    $active->setCellValue('G'.$startRow, $item['address'] );
    $active->setCellValue('H'.$startRow, $item['road'] );
    $active->setCellValue('I'.$startRow, $item['alley'] );
    $active->setCellValue('J'.$startRow, $item['subdistrict'] );
    $active->setCellValue('K'.$startRow, $item['district'] );
    $active->setCellValue('L'.$startRow, $item['province'] );
    $active->setCellValue('M'.$startRow, $item['country'] );
    $active->setCellValue('N'.$startRow, $item['zipcode'] );
    $active->setCellValue('O'.$startRow, $item['mobile'] );
    $active->setCellValue('P'.$startRow, $item['email'] );
    $active->setCellValue('Q'.$startRow, $item['model'] );
    $active->setCellValue('R'.$startRow, $item['color'] );
    $active->setCellValue('S'.$startRow, $item['store'] );
    $active->setCellValue('T'.$startRow, $item['storename'] );
    $active->setCellValue('U'.$startRow, $item['slip'] );
    $active->setCellValue('V'.$startRow, $item['passportfile'] );

    $startRow++;
}


//border setting --------------------------------------------------------------------------------------------------
$active->getStyle("A1:V1")->getFont()->setBold(true);
$active->getStyle('A1:'.'V1')->applyFromArray( $headerArray );
$active->getStyle('A1:'. 'V' . ($startRow-1))->applyFromArray( $styleArray );

// Rename worksheet ---------------------------------------------------------------------------------------
$active->setTitle('Customer Register Daily Report');

$filename = 'registerlist_' . $data['startdate']. '.xlsx';

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="'. $filename .'"');
header('Cache-Control: max-age=0');
header('Cache-Control: max-age=1');
header ('Expires: Mon, 26 Jul 2020 05:00:00 GMT');
header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
header ('Cache-Control: cache, must-revalidate'); 
header ('Pragma: public');

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');
exit;
?>