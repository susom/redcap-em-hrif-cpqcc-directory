<?php

/**
This is a script to generate a pdf contact list for the hrif cpqcc directory
**/
namespace Cpqcc\MemberDirectory;
/** @var \Cpqcc\MemberDirectory\MemberDirectory $module */

error_reporting(E_ALL);

use \FPDF;
use REDCap;

class PDF extends \FPDF
{
    function Header() {

    $this->SetFont('Arial','B',14);
        $this->SetTextColor(255,255,255);
        $this->setFillColor(76,59,115);
        $this->SetLeftMargin(5);
    $this->Cell(0,10,'MEMBER DIRECTORY',1,1,'C',true);
        $this->Ln(8);

    }

    function Footer() {
    global $module;
   // Position at 1.0 cm from the bottom
    //$this->SetY(-13);
        $this->Image($module->getModulePath() . '/directory_footer_2019.png',7,284,196,8);

    //$this->setFillColor(76,59,115);
    //$this->Cell(50,10,'',0,0,'L',true);
    //$this->Cell(96,10,'',0,0,'C',true);
    //$this->Cell(50,10,'',0,0,'R',true);
    }
}


$data = getData();
$module->emDebug("Results: ".print_r($data,true));
$module->emDebug('url: ' . $module->getUrl("index.php", true, true));

$pdf = new PDF();

foreach ($data as $row) {
	$pdf->AddPage();

 //       $pdf->SetLeftMargin(5);
        $pdf->SetMargins(7,7,7);
        $pdf->SetAutoPageBreak(true, 10);

        // Facility name
    $pdf->SetTextColor(76,59,115);
        $pdf->SetFont('Arial','B',14);
        $pdf->Cell(0,5,$row['facility_name'],0,1,'C');

        // Facility info
        $pdf->SetTextColor(0);
        $pdf->setFont('Arial','',9);

        $pdf->MultiCell(0,4,
            $row['facility_address_1']."\n".
            $row['facility_address_2']."\n".
            'County: '.$row['facility_county']."\n".
            'Phone: '.$row['facility_phone']."\n"
            ,0,'C',false);

        $pdf->Ln();
        $pdf->Cell(0,4,'OSHPD Facility Code: '.$row['facility_oshpd'],0,1,'C');
        $pdf->Cell(0,4,'CCS NICU Level: '.$row['facility_nicu_level'],0,1,'C');

        switch ($row['facility_region']) {
          case "1": $pdf->Cell(0,4,'Region: (1) North Coast East Bay',0,1,'C');
                    break;
          case "2": $pdf->Cell(0,4,'Region: (2) Northeastern',0,1,'C');
                    break;
          case "3": $pdf->Cell(0,4,'Region: (3) San Joaquin-Central Valley-Sierra Nevada',0,1,'C');
                    break;
          case "4": $pdf->Cell(0,4,'Region: (4) Mid-Coastal',0,1,'C');
                    break;
          case "5": $pdf->Cell(0,4,'Region: (5) Southern Inland Counties',0,1,'C');
                    break;
          case "6": $pdf->Cell(0,4,'Region: (6) Central-North LA-Coastal Valley ',0,1,'C');
                    break;
          case "7": $pdf->Cell(0,4,'Region: (7) LA-San Gabriel-Inland Orange',0,1,'C');
                    break;
          case "8": $pdf->Cell(0,4,'Region: (8) South Coastal LA-Orange',0,1,'C');
                    break;
          case "9": $pdf->Cell(0,4,'Region: (9) San Diego and Imperial',0,1,'C');
                    break;
          case "10": $pdf->Cell(0,4,'Region: (10) Kaiser North',0,1,'C');
                    break;
          case "11": $pdf->Cell(0,4,'Region: (11) Kaiser South',0,1,'C');
                    break;

          default:  $pdf->Cell(0,4,'Region: NA',0,1,'C');
        }

        $pdf->Ln();
        $pdf->Cell(110,4,'HRIF Program Onsite: ',0,0,'R');
        $pdf->SetFont('Arial','B',9);
        $pdf->Cell(7,4,$row['facility_has_hrif'],0,1,'L');
        $pdf->SetFont('Arial','',9);
        $pdf->Cell(0,4,'Hospital Providing HRIF Program Services:',0,1,'C');
        $pdf->Cell(0,6,$row['facility_followup_care'],0,1,'C');

        $pdf->Ln(6);

        // Contact info

        // Contact Header
        $pdf->setFont('Arial','B',9);
    $pdf->setTextColor(0,137,144);
    $pdf->setFillColor(217,217,217);
        $pdf->Cell(93,8,'NICU CONTACTS',0,0,'C',true);
        $pdf->setFillColor(255);
        $pdf->Cell(10,8,'',0,0,'C',true);  // space between
    $pdf->setFillColor(217,217,217);
        $pdf->Cell(93,8,'HRIF CONTACTS',0,1,'C',true);



        // CPQCC Report Contact
        $pdf->SetXY(7,100);
    $pdf->SetFillColor(0,137,144);
        $pdf->SetTextColor(255);
    $pdf->setFont('Arial','',8);
        $pdf->Cell(44,6,'Report Contact',1,1,'C',true);
    $pdf->SetTextColor(0);
    $pdf->SetFontSize(7);
    $pdf->MultiCell(44,3.5,
           "\n"."  ".$row['cpqcc_report_name']."\n".
           "  ".$row['cpqcc_report_title']."\n".
           "  ".$row['cpqcc_report_dept']."\n".
           "  ".$row['cpqcc_report_street']."\n".
           "  ".$row['cpqcc_report_city'].", ".$row['cpqcc_report_state']." ".$row['cpqcc_report_zip']."\n".
           "  ".$row['cpqcc_report_phone']."\n".
           "  ".$row['cpqcc_report_email']."\n\n"
           ,1,'L',false);
        $pdf->Ln(6);

        // CPQCC Data Contact #1
        $pdf->SetXY(7,143);
        $pdf->SetTextColor(255);
        $pdf->SetFontSize(8);
        $pdf->Cell(44,6,'Data Contact 1',1,1,'C',true);
        $pdf->SetTextColor(0);
        $pdf->SetFontSize(7);
        $pdf->MultiCell(44,3.5,
           "\n"."  ".$row['cpqcc_data1_name']."\n".
           "  ".$row['cpqcc_data1_title']."\n".
           "  ".$row['cpqcc_data1_phone']."\n".
           "  ".$row['cpqcc_data1_email']."\n\n"
           ,1,'L',false);
        $pdf->Ln(6);

        // CPQCC Transport Contact #1
        $pdf->SetXY(7,176);
        $pdf->SetTextColor(255);
        $pdf->SetFontSize(8);
        $pdf->Cell(44,6,'Transport Contact 1',1,1,'C',true);
        $pdf->SetTextColor(0);
        $pdf->SetFontSize(7);
        $pdf->MultiCell(44,3.5,
           "\n"."  ".$row['cpqcc_transp1_name']."\n".
           "  ".$row['cpqcc_transp1_title']."\n".
           "  ".$row['cpqcc_transp1_phone']."\n".
           "  ".$row['cpqcc_transp1_email']."\n\n"
           ,1,'L',false);
        $pdf->Ln(6);

        // CPQCC Quality Improvement #1
        $pdf->SetXY(7,209);
        $pdf->SetTextColor(255);
        $pdf->SetFontSize(8);
        $pdf->Cell(44,6,'Quality Improvement 1',1,1,'C',true);
        $pdf->SetTextColor(0);
        $pdf->SetFontSize(7);
        $pdf->MultiCell(44,3.5,
           "\n"."  ".$row['cpqcc_qi1_name']."\n".
           "  ".$row['cpqcc_qi1_title']."\n".
           "  ".$row['cpqcc_qi1_phone']."\n".
           "  ".$row['cpqcc_qi1_email']."\n\n"
           ,1,'L',false);

        // CPQCC Admin
        $pdf->SetXY(7,242);
        $pdf->SetTextColor(255);
        $pdf->SetFontSize(8);
        $pdf->Cell(44,6,'Admin',1,1,'C',true);
        $pdf->SetTextColor(0);
        $pdf->SetFontSize(7);
        $pdf->MultiCell(44,3.5,
           "\n"."  ".$row['cpqcc_admin_name']."\n".
           "  ".$row['cpqcc_admin_title']."\n".
           "  ".$row['cpqcc_admin_phone']."\n".
           "  ".$row['cpqcc_admin_email']."\n\n"
           ,1,'L',false);
        $pdf->Ln(6);


        // CPQCC Neonatologist
        $pdf->SetXY(56,100);
        $pdf->SetTextColor(255);
        $pdf->SetFontSize(8);
        $pdf->Cell(44,6,'Neonatologist',1,1,'C',true);
        $pdf->SetXY(56,106);
        $pdf->SetTextColor(0);
        $pdf->SetFontSize(7);
        $pdf->MultiCell(44,3.5,
           "\n"."  ".$row['cpqcc_neo_name']."\n".
           "  ".$row['cpqcc_neo_title']."\n".
           "  ".$row['cpqcc_neo_phone']."\n".
           "  ".$row['cpqcc_neo_email']."\n\n\n\n\n"
           ,1,'L',false);

        // CPQCC Data Contact #2
        $pdf->SetXY(56,143);
        $pdf->SetTextColor(255);
        $pdf->SetFontSize(8);
        $pdf->Cell(44,6,'Data Contact 2',1,1,'C',true);
        $pdf->SetXY(56,149);
        $pdf->SetTextColor(0);
        $pdf->SetFontSize(7);
        $pdf->MultiCell(44,3.5,
           "\n"."  ".$row['cpqcc_data2_name']."\n".
           "  ".$row['cpqcc_data2_title']."\n".
           "  ".$row['cpqcc_data2_phone']."\n".
           "  ".$row['cpqcc_data2_email']."\n\n"
           ,1,'L',false);

        // CPQCC Transport #2
        $pdf->SetXY(56,176);
        $pdf->SetTextColor(255);
        $pdf->SetFontSize(8);
        $pdf->Cell(44,6,'Transport Contact 2',1,1,'C',true);
        $pdf->SetXY(56,182);
        $pdf->SetTextColor(0);
        $pdf->SetFontSize(7);
        $pdf->MultiCell(44,3.5,
           "\n"."  ".$row['cpqcc_transp2_name']."\n".
           "  ".$row['cpqcc_transp2_title']."\n".
           "  ".$row['cpqcc_transp2_phone']."\n".
           "  ".$row['cpqcc_transp2_email']."\n\n"
           ,1,'L',false);

        // CPQCC Quality Improvement #2
        $pdf->SetXY(56,209);
        $pdf->SetTextColor(255);
        $pdf->SetFontSize(8);
        $pdf->Cell(44,6,'Quality Improvement 2',1,1,'C',true);
        $pdf->SetXY(56,215);
        $pdf->SetTextColor(0);
        $pdf->SetFontSize(7);
        $pdf->MultiCell(44,3.5,
           "\n"."  ".$row['cpqcc_qi2_name']."\n".
           "  ".$row['cpqcc_qi2_title']."\n".
           "  ".$row['cpqcc_qi2_phone']."\n".
           "  ".$row['cpqcc_qi2_email']."\n\n"
           ,1,'L',false);
        $pdf->Ln(6);

        // CPQCC Contract Signer
        $pdf->SetXY(56,242);
        $pdf->SetTextColor(255);
        $pdf->SetFontSize(8);
        $pdf->Cell(44,6,'Contract Signer',1,1,'C',true);
        $pdf->SetXY(56,248);
        $pdf->SetTextColor(0);
        $pdf->SetFontSize(7);
        $pdf->MultiCell(44,3.5,
           "\n"."  ".$row['cpqcc_contract_signed']."\n".
           "  ".$row['cpqcc_contract_title']."\n".
           "  ".$row['cpqcc_contract_phone']."\n".
           "  ".$row['cpqcc_contract_email']."\n\n"
           ,1,'L',false);

        $pdf->Ln(6);

// HRIF Contacts
        // HRIF Coordinator
        $pdf->SetXY(110,100);
        $pdf->SetTextColor(255);
        $pdf->SetFontSize(8);
        $pdf->Cell(44,6,'Coordinator',1,1,'C',true);
        $pdf->SetXY(110,106);
        $pdf->SetTextColor(0);
        $pdf->SetFontSize(7);
        $hrifcoordstr = "\n"."  ".$row['hrif_coord_name']."\n";
	if (strlen($row['hrif_coord_fax']) == 0) {
            $hrifcoordstr = $hrifcoordstr ."  ".$row['hrif_coord_title']."\n";
	}
        $hrifcoordstr = $hrifcoordstr ."  ".$row['hrif_coord_address1']."\n";
        if (strlen($row['hrif_coord_address2']) > 0) {
            $hrifcoordstr = $hrifcoordstr .
                "  ".$row['hrif_coord_address2']."\n";
        }
        if (strlen($row['hrif_coord_city']) > 0 or
            strlen($row['hrif_coord_state']) > 0) {
            $hrifcoordstr = $hrifcoordstr .
                "  ".$row['hrif_coord_city'].", "
                .$row['hrif_coord_state']." ".$row['hrif_coord_zip']."\n";
        } else {
            $hrifcoordstr = $hrifcoordstr ."\n";
        }
        if (strlen($row['hrif_coord_phone']) > 0) {
            $hrifcoordstr = $hrifcoordstr .
                 "  Ph: ".$row['hrif_coord_phone']."\n";
        } else {
             $hrifcoordstr = $hrifcoordstr ."\n";
        }
        if (strlen($row['hrif_coord_fax']) > 0) {
             $hrifcoordstr = $hrifcoordstr .
                  "  Fax (secure): ".$row['hrif_coord_fax']."\n";
        }
        if (strlen($row['hrif_coord_address2']) == 0){
	   if (strlen($row['hrif_coord_fax2']) > 0) {
             $hrifcoordstr = $hrifcoordstr .
                      "  Fax: ".$row['hrif_coord_fax2']."\n";
           } else {
              $hrifcoordstr = $hrifcoordstr ."\n";
           }
        }
        $hrifcoordstr = $hrifcoordstr .
             "  ".$row['hrif_coord_email']."\n\n";
        $pdf->MultiCell(44,3.5,
           $hrifcoordstr
           ,1,'L',false);


        // HRIF Contact #1
        $pdf->SetXY(110,143);
        $pdf->SetTextColor(255);
        $pdf->SetFontSize(8);
        $pdf->Cell(44,6,'Clinic Contact 1',1,1,'C',true);
        $pdf->SetXY(110,149);
        $pdf->SetTextColor(0);
        $pdf->SetFontSize(7);
        $pdf->MultiCell(44,3.5,
           "\n"."  ".$row['hrif_contact1_name']."\n".
           "  ".$row['hrif_contact1_title']."\n".
           "  ".$row['hrif_contact1_phone']."\n".
           "  ".$row['hrif_contact1_email']."\n\n"
           ,1,'L',false);

	// HRIF Contact #3
        $pdf->SetXY(110,176);
        $pdf->SetTextColor(255);
        $pdf->SetFontSize(8);
        $pdf->Cell(44,6,'Clinic Contact 3',1,1,'C',true);
        $pdf->SetXY(110,182);
        $pdf->SetTextColor(0);
        $pdf->SetFontSize(7);
        $pdf->MultiCell(44,3.5,
           "\n"."  ".$row['hrif_contact3_name']."\n".
           "  ".$row['hrif_contact3_title']."\n".
           "  ".$row['hrif_contact3_phone']."\n".
           "  ".$row['hrif_contact3_email']."\n\n"
           ,1,'L',false);

	// HRIF NICU Discharge Contact
        $pdf->SetXY(110,209);
        $pdf->SetTextColor(255);
        $pdf->SetFontSize(8);
        $pdf->Cell(44,6,'NICU Discharge Contact',1,1,'C',true);
        $pdf->SetXY(110,215);
        $pdf->SetTextColor(0);
        $pdf->SetFontSize(7);
        $pdf->MultiCell(44,3.5,
           "\n"."  ".$row['hrif_nicu_dc_name']."\n".
           "  ".$row['hrif_nicu_dc_title']."\n".
           "  ".$row['hrif_nicu_dc_phone']."\n".
           "  ".$row['hrif_nicu_dc_email']."\n\n"
           ,1,'L',false);

        // HRIF NICU Contact #2
        $pdf->SetXY(110,242);
        $pdf->SetTextColor(255);
        $pdf->SetFontSize(8);
        $pdf->Cell(44,6,'NICU Contact 2',1,1,'C',true);
        $pdf->SetXY(110,248);
        $pdf->SetTextColor(0);
        $pdf->SetFontSize(7);
        $pdf->MultiCell(44,3.5,
           "\n"."  ".$row['hrif_nicu2_name']."\n".
           "  ".$row['hrif_nicu2_title']."\n".
           "  ".$row['hrif_nicu2_phone']."\n".
           "  ".$row['hrif_nicu2_email']."\n\n"
           ,1,'L',false);

        // Medical Director
        $pdf->SetXY(159,100);
        $pdf->SetTextColor(255);
        $pdf->SetFontSize(8);
        $pdf->Cell(44,6,'Medical Director',1,1,'C',true);
        $pdf->SetXY(159,106);
        $pdf->SetTextColor(0);
        $pdf->SetFontSize(7);
        $pdf->MultiCell(44,3.5,
           "\n"."  ".$row['hrif_md_name']."\n".
           "  ".$row['hrif_md_title']."\n".
           "  ".$row['hrif_md_phone']."\n".
           "  ".$row['hrif_md_email']."\n\n\n\n\n"
           ,1,'L',false);

	// HRIF Contact #2
        $pdf->SetXY(159,143);
        $pdf->SetTextColor(255);
        $pdf->SetFontSize(8);
        $pdf->Cell(44,6,'Clinic Contact 2',1,1,'C',true);
        $pdf->SetXY(159,149);
        $pdf->SetTextColor(0);
        $pdf->SetFontSize(7);
        $pdf->MultiCell(44,3.5,
           "\n"."  ".$row['hrif_contact2_name']."\n".
           "  ".$row['hrif_contact2_title']."\n".
           "  ".$row['hrif_contact2_phone']."\n".
           "  ".$row['hrif_contact2_email']."\n\n"
           ,1,'L',false);

        // HRIF Contact #4
        $pdf->SetXY(159,176);
        $pdf->SetTextColor(255);
        $pdf->SetFontSize(8);
        $pdf->Cell(44,6,'Clinic Contact 4',1,1,'C',true);
        $pdf->SetXY(159,182);
        $pdf->SetTextColor(0);
        $pdf->SetFontSize(7);
        $pdf->MultiCell(44,3.5,
           "\n"."  ".$row['hrif_contact4_name']."\n".
           "  ".$row['hrif_contact4_title']."\n".
           "  ".$row['hrif_contact4_phone']."\n".
           "  ".$row['hrif_contact4_email']."\n\n"
           ,1,'L',false);

	// NICU Contact #1
        $pdf->SetXY(159,209);
        $pdf->SetTextColor(255);
        $pdf->SetFontSize(8);
        $pdf->Cell(44,6,'NICU Contact 1',1,1,'C',true);
        $pdf->SetXY(159,215);
        $pdf->SetTextColor(0);
        $pdf->SetFontSize(7);
        $pdf->MultiCell(44,3.5,
           "\n"."  ".$row['hrif_nicu1_name']."\n".
           "  ".$row['hrif_nicu1_title']."\n".
           "  ".$row['hrif_nicu1_phone']."\n".
           "  ".$row['hrif_nicu1_email']."\n\n"
           ,1,'L',false);

        // Last Modified Date
        $pdf->SetXY(7,272);
        $pdf->Ln(2);
        $pdf->SetFont('Arial','I',8);
        $pdf->SetTextColor(128);  // gray
        $pdf->Cell(93,3,'NICU Contacts Last Updated By: '.$row['cpqcc_last_update_by'],0,0,'L');
        $pdf->Cell(10,3,'',0,0,'C');  // space between
	$pdf->Cell(93,3,'HRIF Contacts Last Updated By: '.$row['hrif_last_update_by'],0,0,'R');
        $pdf->SetXY(7,278);
        $pdf->Cell(93,3,'Last Modified Date: '.$row['last_modified_date'],0,0,'L');
        $pdf->Cell(10,3,'',0,0,'C');  // space between
        $pdf->Cell(93,3,'Download Date: '.date('F j, Y h:ia'),0,0,'R');

}

$pdf->Output("Cpqcc_Hrif-Qci_Directory.pdf","I");

// Retrieve all data from the project
function getData() {
    global $module;
    $r_result = REDCap::getData(PROJECT_ID,'json');
    $results = json_decode($r_result,true);
    //$module->emDebug(print_r($results,true));

    usort($results, function ($a, $b) {
        return strcmp($a["facility_name"], $b["facility_name"]);
    });

    return $results;
}


?>
