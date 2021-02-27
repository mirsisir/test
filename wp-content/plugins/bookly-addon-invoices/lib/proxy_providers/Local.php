<?php
namespace BooklyInvoices\Lib\ProxyProviders;

use Bookly\Lib as BooklyLib;
use BooklyInvoices\Lib\Plugin;
use BooklyInvoices\Backend\Components;

/**
 * Class Local
 * Provide local methods to be used in Bookly and other add-ons.
 *
 * @package BooklyInvoices\Lib\ProxyProviders
 */
abstract class Local extends BooklyLib\Proxy\Invoices
{
    /**
     * @inheritdoc
     */
    public static function getInvoice( BooklyLib\Entities\Payment $payment )
    {
        $pdf  = self::_getInvoicePdf( $payment );
        $path = sys_get_temp_dir() . DIRECTORY_SEPARATOR . wp_unique_filename( sys_get_temp_dir(), 'Invoice_' . $payment->getId() . '.pdf' );

        $pdf->Output( $path, 'F' );

        return $path;
    }

    /**
     * @inheritdoc
     */
    public static function downloadInvoice( BooklyLib\Entities\Payment $payment )
    {
        $pdf = self::_getInvoicePdf( $payment );
        $pdf->Output( 'Invoice_' . $payment->getId() . '.pdf', 'D' );
        exit();
    }

    /**
     * @param BooklyLib\Entities\Payment $payment
     * @return \TCPDF
     */
    private static function _getInvoicePdf( BooklyLib\Entities\Payment $payment )
    {
        include_once Plugin::getDirectory() . '/lib/TCPDF/tcpdf.php';

        $pdf = new \TCPDF();
        $pdf->setImageScale( 2.3 );
        $pdf->setPrintHeader( false );
        $pdf->setPrintFooter( false );
        $pdf->AddPage();
        $pdf->SetFont( 'freeserif', '', 8 );
        $data = Components\Invoice\Invoice::render( $payment->getPaymentData() );
        $pdf->writeHTML( $data );

        return $pdf;
    }

}