<?php
/**
 * lib_sign.php - Shared signing library functions
 * Common utility functions for PDF signing and payment file handling
 * 
 * Usage in other files:
 * require_once __DIR__ . '/lib_sign.php';
 * 
 * Functions:
 * - getFolderPdf($evid)
 * - getFileNameExcel($evid, $payment_type)
 * - getFileNamePdf($evid, $payment_type)
 * - getFileNameJson($evid, $payment_type)
 */

/**
 * Get the PDF folder path for an event
 * 
 * @param int $evid Event ID
 * @return string Folder path
 */
function getFolderPdf($evid) {
    return '/var/glx/weblog/pdf_event_bill/ev_id_' . $evid;
}

/**
 * Get the Excel file path with optional payment type suffix
 * 
 * @param int $evid Event ID
 * @param string $payment_type Optional payment type (trong_nuoc, nuoc_ngoai, etc.)
 * @return string Full file path to Excel file
 */
function getFileNameExcel($evid, $payment_type = '') {
    $pdfDir = getFolderPdf($evid);
    $suffix = $payment_type ? '_' . $payment_type : '';
    return $pdfDir . '/ThanhToan_Event_' . $evid . $suffix . '.xlsx';
}

/**
 * Get the PDF file path with optional payment type suffix
 * 
 * @param int $evid Event ID
 * @param string $payment_type Optional payment type (trong_nuoc, nuoc_ngoai, etc.)
 * @return string Full file path to PDF file
 */
function getFileNamePdf($evid, $payment_type = '', $haveSign = false) {
    $pdfDir = getFolderPdf($evid);
    $suffix = $payment_type ? '_' . $payment_type : '';
    if ($haveSign) {
        return $pdfDir . '/ThanhToan_Event_' . $evid . $suffix . '_signed.pdf';
    }
    return $pdfDir . '/ThanhToan_Event_' . $evid . $suffix . '.pdf';
}

/**
 * Get the JSON metadata file path with optional payment type suffix
 * 
 * @param int $evid Event ID
 * @param string $payment_type Optional payment type (trong_nuoc, nuoc_ngoai, etc.)
 * @return string Full file path to JSON metadata file
 */
function getFileNameJson($evid, $payment_type = '') {
    $pdfDir = getFolderPdf($evid);
    $suffix = $payment_type ? '_' . $payment_type : '';
    return $pdfDir . '/ThanhToan_Event_' . $evid . $suffix . '.json';
}
