<?php

/**
 * This is a script to generate a pdf contact list for the hrif cpqcc directory
 **/
namespace Cpqcc\MemberDirectory;
/** @var \Cpqcc\MemberDirectory\MemberDirectory $module */

error_reporting(E_ALL);

use \FPDF;
use REDCap;

class PDF extends \FPDF
{

    function Header()
    {
        $this->SetFont('Arial', 'B', 14);
        $this->SetTextColor(255, 255, 255);
        $this->setFillColor(76, 59, 115);
        $this->SetLeftMargin(5);
        $this->Cell(0, 10, 'MEMBER DIRECTORY', 1, 1, 'C', true);
        $this->Ln(8);

    }

    function Footer()
    {
        global $module;
        // Position at 1.0 cm from the bottom
        //$this->SetY(-13);
        $this->Image($module->getModulePath() . '/directory_footer_2019.png', 7, 284, 196, 8);

        //$this->setFillColor(76,59,115);
        //$this->Cell(50,10,'',0,0,'L',true);
        //$this->Cell(96,10,'',0,0,'C',true);
        //$this->Cell(50,10,'',0,0,'R',true);
    }

    // copied from PDF_MC_Table hosted on fpdf.org
    function NbLines($w, $txt)
    {
        // Compute the number of lines a MultiCell of width w will take
        if(!isset($this->CurrentFont))
            $this->Error('No font has been set');
        $cw = $this->CurrentFont['cw'];
        if($w==0)
            $w = $this->w-$this->rMargin-$this->x;
        $wmax = ($w-2*$this->cMargin)*1000/$this->FontSize;
        $s = str_replace("\r",'',(string)$txt);
        $nb = strlen($s);
        if($nb>0 && $s[$nb-1]=="\n")
            $nb--;
        $sep = -1;
        $i = 0;
        $j = 0;
        $l = 0;
        $nl = 1;
        while($i<$nb)
        {
            $c = $s[$i];
            if($c=="\n")
            {
                $i++;
                $sep = -1;
                $j = $i;
                $l = 0;
                $nl++;
                continue;
            }
            if($c==' ')
                $sep = $i;
            $l += $cw[$c];
            if($l>$wmax)
            {
                if($sep==-1)
                {
                    if($i==$j)
                        $i++;
                }
                else
                    $i = $sep+1;
                $sep = -1;
                $j = $i;
                $l = 0;
                $nl++;
            }
            else
                $i++;
        }
        return $nl;
    }
}

const INDENT_NL = "\n   ";
const INDENT = "   ";
const TEAL_R = 0;
const TEAL_G = 137;
const TEAL_B = 144;
const BG_R = 225;
const BG_G = 225;
const BG_B = 225;

$half_width = 98; // used for the facilities info
$header_width = 94; // used for contact columns
$header_height = 8;
$center_width = 8; // gap size between contact columns
$cell_width = $header_width /2;
$line_height = 4; // this is the height of each line of text
$label_height = 6;
// $min_row_height is 4x, 7x provides some padding
$min_row_height = $label_height + 7 * $line_height;
$contact_font_size = 8;

// Retrieve all data from the project
function getData()
{
    global $module;
    $r_result = REDCap::getData(PROJECT_ID, 'json');
    $results = json_decode($r_result, true);
    //$module->emDebug(print_r($results,true));

    usort($results, function ($a, $b) {
        return strcmp($a["facility_name"], $b["facility_name"]);
    });

    return $results;
}

function getRegion($region_code) {
    $region = "NA";
    switch ($region_code) {
        case "1":
            $region =  '(1) North Coast East Bay';
            break;
        case "2":
            $region =  '(2) Northeastern';
            break;
        case "3":
            $region =  '(3) San Joaquin-Central Valley-Sierra Nevada';
            break;
        case "4":
            $region =  '(4) Mid-Coastal';
            break;
        case "5":
            $region =  '(5) Southern Inland Counties';
            break;
        case "6":
            $region =  '(6) Central-North LA-Coastal Valley ';
            break;
        case "7":
            $region =  '(7) LA-San Gabriel-Inland Orange';
            break;
        case "8":
            $region =  '(8) South Coastal LA-Orange';
            break;
        case "9":
            $region = '(9) San Diego and Imperial';
            break;
        case "10":
            $region =  '(10) Kaiser North';
            break;
        case "11":
            $region = '(11) Kaiser South';
            break;
    }
    return $region;
}

function getAddress($addr1, $addr2, $city, $state, $zip) {
    if (empty($addr1)) return "Address: Not Available";

    $addr = $addr1;
    if (!empty($addr2)) {
        if (empty($city) && empty($state) && empty($zip)) $addr .=  INDENT_NL. $addr2;
        else $addr .=  ", ". $addr2;
    }
    if (!empty($city) && !empty($state)) $addr .= INDENT_NL. $city . ', ' . $state;
    else if (!empty($city)) $addr .= INDENT_NL. $city;
    else if (!empty($state)) $addr .= INDENT_NL. $state;

    if (!empty($zip)) {
        if (!empty($city) && !empty($state)) $addr .= ' ';
        $addr .= $zip;
    }
    return $addr;
}

function satelliteLocations($isAffliated, $city1, $phone1, $city2,
                            $phone2, $city3, $phone3, $city4, $phone4,
                            $city5, $phone5) {
    $locations = "Satellite Locations:  ";
    if (empty($isAffliated)) return "";
    if (!empty($city1)) {
        $locations .= $city1;
        if (!empty($phone1)) $locations .= " $phone1";
    }
    if (!empty($city2)) {
        $locations .= ", " . $city2;
        if (!empty($phone2)) $locations .= " $phone2";
    }
    if (!empty($city3)) {
        $locations .= ", " . $city3;
        if (!empty($phone3)) $locations .= " $phone3";
    }
    if (!empty($city4)) {
        $locations .= ", " . $city4;
        if (!empty($phone4)) $locations .= " $phone4";
    }
    if (!empty($city5)) {
        $locations .= ", " . $city5;
        if (!empty($phone5)) $locations .= " $phone5";
    }
    return $locations;
}

function yesNo($code) {
    if ($code == '1') return 'Yes';
    return 'No';
}


function valueOrNa($value) {
    if (empty($value)) return 'Not Available';
    return $value;
}

function addContactCell($x, $y, $row_height, $label, $text) {
    global $pdf, $cell_width, $line_height, $label_height, $contact_font_size;
    $pdf->SetXY($x, $y);
    $pdf->SetFillColor(BG_R, BG_G, BG_B);
    $pdf->Rect($x, $y, $cell_width, $row_height, "DF");
    $pdf->SetTextColor(TEAL_R, TEAL_G, TEAL_B);
    $pdf->setFont('Arial', 'B', 9);
    $pdf->Cell($cell_width, $label_height, '   ' . $label, 0, 1, 'L', false);

    $pdf->SetXY($x, $y + $label_height);
    $pdf->SetTextColor(0);
    $pdf->setFont('Arial', '', $contact_font_size);
    $pdf->MultiCell($cell_width, $line_height,
        $text, 0, 'L', false);
}

function addContactRow($y,
                       $nicu1_label, $nicu1_text,
                       $nicu2_label, $nicu2_text,
                       $hrif1_label, $hrif1_text,
                       $hrif2_label, $hrif2_text)
{
    global $pdf, $row_start, $cell_width, $line_height, $center_width,
           $label_height, $min_row_height, $contact_font_size;
    $pdf->setFont('Arial', '', $contact_font_size);
    $row_height = $label_height + $line_height *
        max($pdf->NbLines($cell_width, $nicu1_text),
            $pdf->NbLines($cell_width, $nicu2_text),
            $pdf->NbLines($cell_width, $hrif1_text),
            $pdf->NbLines($cell_width, $hrif2_text));
    $row_height = max($row_height, $min_row_height);
    addContactCell($row_start, $y, $row_height, $nicu1_label, $nicu1_text);

    $x = $row_start + $cell_width;
    addContactCell($x, $y, $row_height, $nicu2_label, $nicu2_text);

    $x = $x + $cell_width + $center_width;
    addContactCell($x, $y, $row_height, $hrif1_label, $hrif1_text);

    $x = $x + $cell_width;
    addContactCell($x, $y, $row_height, $hrif2_label, $hrif2_text);
    return $row_height;
}

$data = getData();
$module->emDebug("Results: " . print_r($data, true));
$module->emDebug('url: ' . $module->getUrl("index.php", true, true));

$pdf = new PDF();

foreach ($data as $row) {
    if (strtoupper($row['facility_name']) != 'DEMO') {
        $pdf->AddPage();

        $pdf->SetMargins(7, 7, 7);
        $pdf->SetAutoPageBreak(true, 10);
        $row_start = 8;

        // Facility name
        $pdf->SetTextColor(76, 59, 115);
        $pdf->SetFont('Arial', 'B', 14);
        $pdf->Cell(0, 5, $row['facility_name'], 0, 1, 'C');
        $pdf->Ln();

        // Facility info
        $text1 = INDENT_NL .
            getAddress($row['facility_address_1'],
                $row['facility_address_2'], null, null, null)
            . INDENT_NL .
            'County: ' . $row['facility_county'] . INDENT_NL .
            'Phone: ' . $row['facility_phone'] . "\n" . INDENT_NL .
            'HCAI Facility ID: ' . $row['facility_oshpd'] . INDENT_NL .
            'CCS NICU Level: ' . $row['facility_nicu_level'] . INDENT_NL .
            "Region: " . getRegion($row['facility_region']) . INDENT_NL;
        $text2 = INDENT_NL .
            'HRIF Program Onsite: ' . $row['facility_has_hrif'] . INDENT_NL .
            'Hospital Providing HRIF Services: ' . $row['facility_followup_care'] . INDENT_NL .
            INDENT_NL .
            getAddress($row['hrif_coord_address1'],
                $row['hrif_coord_address2'],
                $row['hrif_coord_city'],
                $row['hrif_coord_state'],
                $row['hrif_coord_zip']) . INDENT_NL .
            'Phone: ' . valueOrNa($row['[hrif_coord_phone]']) . INDENT_NL .
            'Secure Fax #: ' . valueOrNa($row['[hrif_coord_fax]']) . INDENT_NL .
            'Satellite Clinics: ' . yesNo($row['is_your_hrif_program_affil'])
            . INDENT_NL
            . satelliteLocations($row['is_your_hrif_program_affil'],
                $row['sc_city'], $row['sc_phone_number'],
                $row['sc_city_2'], $row['sc_phone_number_2'],
                $row['sc_city_3'], $row['sc_phone_number_3'],
                $row['sc_city_4'], $row['sc_phone_number_4'],
                $row['sc_city_5'], $row['sc_phone_number_5']) . INDENT_NL;
        $pdf->SetTextColor(0);
        $pdf->setFont('Arial', '', 9);
        $y = $pdf->GetY();
        $row_height = $line_height * max($pdf->NbLines($half_width, $text1), $pdf->NbLines($half_width, $text2));


        $pdf->SetXY($row_start, $y);
        // draw the rectangle separately from multicells to ensure the rectangles match

        $pdf->Rect($row_start, $y, $half_width, $row_height);
        $pdf->MultiCell($half_width, $line_height,
            $text1, 0, 'L', false);

        /* To bold content inside a rectangle
         * $pdf->Rect($x + $half_width,  $y, $half_width, 11* $line_height);
        $pdf->SetXY($x + $half_width, $y + $line_height);
        $pdf->SetFont('Arial', '', 9);
        $pdf->Write(5,'HRIF Program Onsite: ');
        $pdf->SetFont('Arial', 'B', 9);
        $pdf->Write(5,$row['facility_has_hrif']);
        $pdf->Ln();
        $pdf->SetXY($x + $half_width, $y + 2 * $line_height);
        $pdf->SetFont('Arial', '', 9);
        $pdf->Write(5,'Hospital Providing HRIF Services: ');
        $pdf->Write(5,$row['facility_followup_care']);
        $pdf->Ln();*/
        $x = $row_start + $half_width;
        $pdf->SetXY($x, $y);
        $pdf->Rect($x, $y, $half_width, $row_height);
        $pdf->MultiCell($half_width, $line_height,
            $text2,
            0, 'L', false);
        $pdf->SetY($row_height + $y);
        $pdf->Ln(5);

        // Contact info


        $y = $pdf->GetY();
        $pdf->SetX($row_start);
        // Contact Header
        $pdf->setFont('Arial', 'B', 9);
        $pdf->setFillColor(0, 137, 144);
        $pdf->setTextColor(255, 255, 255);
        $pdf->Cell($header_width, $header_height, 'NICU CONTACTS', 1, 0, 'C', true);
        $pdf->SetXY($row_start + $header_width + $center_width, $y);
        $pdf->Cell($header_width, $header_height, 'HRIF CONTACTS', 1, 1, 'C', true);

        /**************** ROW 1 ***********************************/
        $y = $pdf->GetY();

        $nicu1 = INDENT . $row['cpqcc_report_name'] .
            INDENT_NL . $row['cpqcc_report_title'] .
            INDENT_NL . $row['cpqcc_report_phone'] .
            INDENT_NL . $row['cpqcc_report_email'];
        $nicu2 = INDENT . $row['cpqcc_neo_name'] .
            INDENT_NL . $row['cpqcc_neo_title'] .
            INDENT_NL . $row['cpqcc_neo_phone'] .
            INDENT_NL . $row['cpqcc_neo_email'];
        $hrif1 = INDENT . $row['hrif_coord_name'] .
            INDENT_NL . $row['hrif_coord_title'] .
            INDENT_NL . $row['hrif_coord_phone'] .
            INDENT_NL . $row['hrif_coord_email'];
        $hrif2 = INDENT . $row['hrif_md_name'] .
            INDENT_NL . $row['hrif_md_title'] .
            INDENT_NL . $row['hrif_md_phone'] .
            INDENT_NL . $row['hrif_md_email'];
        $row_height = addContactRow($y,
            "Report Contact", $nicu1,
            "Neonatologist", $nicu2,
            "Coordinator", $hrif1,
            "Medical Director", $hrif2);

        /***** END ROW 1 *****/

        $y = $y + $row_height;

        $nicu1 = INDENT . $row['cpqcc_data1_name'] .
            INDENT_NL . $row['cpqcc_data1_title'] .
            INDENT_NL . $row['cpqcc_data1_phone'] .
            INDENT_NL . $row['cpqcc_data1_email'];
        $nicu2 = INDENT . $row['cpqcc_data2_name'] .
            INDENT_NL . $row['cpqcc_data2_title'] .
            INDENT_NL . $row['cpqcc_data2_phone'] .
            INDENT_NL . $row['cpqcc_data2_email'];
        $hrif1 = INDENT . $row['hrif_contact1_name'] .
            INDENT_NL . $row['hrif_contact1_title'] .
            INDENT_NL . $row['hrif_contact1_phone'] .
            INDENT_NL . $row['hrif_contact1_email'];
        $hrif2 = INDENT . $row['hrif_contact2_name'] .
            INDENT_NL . $row['hrif_contact2_title'] .
            INDENT_NL . $row['hrif_contact2_phone'] .
            INDENT_NL . $row['hrif_contact2_email'];
        $row_height = addContactRow($y,
            "Data 1", $nicu1,
            "Data 2", $nicu2,
            "Clinic Data 1", $hrif1,
            "Clinic Data 2", $hrif2);

        /***** END ROW 2 *****/
        $y = $y + $row_height;

        $nicu1 = INDENT . $row['cpqcc_transp1_name'] .
            INDENT_NL . $row['cpqcc_transp1_title'] .
            INDENT_NL . $row['cpqcc_transp1_phone'] .
            INDENT_NL . $row['cpqcc_transp1_email'];
        $nicu2 = INDENT . $row['cpqcc_transp2_name'] .
            INDENT_NL . $row['cpqcc_transp2_title'] .
            INDENT_NL . $row['cpqcc_transp2_phone'] .
            INDENT_NL . $row['cpqcc_transp2_email'];
        $hrif1 = INDENT . $row['hrif_contact3_name'] .
            INDENT_NL . $row['hrif_contact3_title'] .
            INDENT_NL . $row['hrif_contact3_phone'] .
            INDENT_NL . $row['hrif_contact3_email'];
        $hrif2 = INDENT . $row['hrif_contact4_name'] .
            INDENT_NL . $row['hrif_contact4_title'] .
            INDENT_NL . $row['hrif_contact4_phone'] .
            INDENT_NL . $row['hrif_contact4_email'];

        $row_height = addContactRow($y,
            "Transport 1", $nicu1,
            "Transport 2", $nicu2,
            "Clinic Data 3", $hrif1,
            "Clinic Data 4", $hrif2);

        /********** END ROW ******************************/
        $y = $y + $row_height;

        $nicu1 = INDENT . $row['cpqcc_qi1_name'] .
            INDENT_NL . $row['cpqcc_qi1_title'] .
            INDENT_NL . $row['cpqcc_qi1_phone'] .
            INDENT_NL . $row['cpqcc_qi1_email'];
        $nicu2 = INDENT . $row['cpqcc_qi2_name'] .
            INDENT_NL . $row['cpqcc_qi2_title'] .
            INDENT_NL . $row['cpqcc_qi2_phone'] .
            INDENT_NL . $row['cpqcc_qi2_email'];
        $hrif1 = INDENT . $row['hrif_nicu_dc_name'] .
            INDENT_NL . $row['hrif_nicu_dc_title'] .
            INDENT_NL . $row['hrif_nicu_dc_phone'] .
            INDENT_NL . $row['hrif_nicu_dc_email'];
        $hrif2 = INDENT . $row['hrif_nicu1_name'] .
            INDENT_NL . $row['hrif_nicu1_title'] .
            INDENT_NL . $row['hrif_nicu1_phone'] .
            INDENT_NL . $row['hrif_nicu1_email'];

        $row_height = addContactRow($y,
            "Quality Improvement 1", $nicu1,
            "Quality Improvement 2", $nicu2,
            "NICU Discharge", $hrif1,
            "NICU 1", $hrif2);

        /***** ROW END ***********/

        $y += $row_height;
        // CPQCC Admin
        $nicu1 = INDENT . $row['cpqcc_admin_name'] .
            INDENT_NL . $row['cpqcc_admin_title'] .
            INDENT_NL . $row['cpqcc_admin_phone'] .
            INDENT_NL . $row['cpqcc_admin_email'];
        // CPQCC Contract Signer
        $nicu2 = INDENT . $row['cpqcc_contract_signed'] .
            INDENT_NL . $row['cpqcc_contract_title'] .
            INDENT_NL . $row['cpqcc_contract_phone'] .
            INDENT_NL . $row['cpqcc_contract_email'];
        // HRIF NICU Contact #2
        $hrif1 = INDENT . $row['hrif_nicu2_dc_name'] .
            INDENT_NL . $row['hrif_nicu2_dc_title'] .
            INDENT_NL . $row['hrif_nicu2_dc_phone'] .
            INDENT_NL . $row['hrif_nicu2_dc_email'];
        $hrif2 = INDENT . $row['hrif_nicu3_name'] .
            INDENT_NL . $row['hrif_nicu3_title'] .
            INDENT_NL . $row['hrif_nicu3_phone'] .
            INDENT_NL . $row['hrif_nicu3_email'];

        $row_height = addContactRow($y,
            "Admin", $nicu1,
            "Contact Signee", $nicu2,
            "NICU 2", $hrif1,
            "NICU 3", $hrif2);


        // Last Modified Date
        $y += $row_height;
        $pdf->SetXY($row_start, $y);
        $pdf->Ln(2);
        $pdf->SetFont('Arial', 'I', $contact_font_size);
        $pdf->SetTextColor(128);  // gray
        $pdf->Cell($header_width, $line_height, 'NICU Contacts Last Updated By: ' . $row['cpqcc_last_update_by'], 0, 0, 'L');
        $pdf->Cell($center_width, $line_height, '', 0, 0, 'C');  // space between
        $pdf->Cell($header_width, $line_height, 'HRIF Contacts Last Updated By: ' . $row['hrif_last_update_by'], 0, 0, 'R');
        $pdf->Ln(4);
        $pdf->Cell($header_width, $line_height, 'Last Modified Date: ' . $row['last_modified_date'], 0, 0, 'L');
        $pdf->Cell($center_width, $line_height, '', 0, 0, 'C');  // space between
        $pdf->Cell($header_width, $line_height, 'Download Date: ' . date('F j, Y h:ia'), 0, 0, 'R');

    }
}

$pdf->Output("Cpqcc_Hrif-Qci_Directory.pdf", "I");




?>
