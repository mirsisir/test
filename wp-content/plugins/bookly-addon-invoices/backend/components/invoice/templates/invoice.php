<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
/** @var $helper BooklyInvoices\Backend\Modules\Settings\Lib\Helper */
?>
<div>
    <table cellpadding="0" cellspacing="0" width="100%">
        <tbody>
        <tr>
            <td colspan="3"><?php $helper::renderImage( 'bookly_invoices_header_attachment_id', 'w-100' ) ?></td>
        </tr>
        <tr>
            <td style="width: 66.66%; padding: 8px" colspan="2"><?php $helper::renderString( 'bookly_l10n_invoice_company_logo', $codes ) ?></td>
            <td style="width: 33.33%; padding: 8px; font-size: large"><?php $helper::renderString( 'bookly_l10n_invoice_label', $codes ) ?></td>
        </tr>
        <tr>
            <td colspan="3">&nbsp;</td>
        </tr>
        <tr>
            <td colspan="3" style="padding: 8px; font-size: large; font-weight: bold"><?php $helper::renderString( 'bookly_l10n_invoice_company_label', $codes ) ?></td>
        </tr>
        <tr>
            <td colspan="3">&nbsp;</td>
        </tr>
        <tr>
            <td style="width: 33.33%; padding: 0 8px"><?php $helper::renderString( 'bookly_l10n_invoice_company_data', $codes ) ?></td>
            <td style="width: 33.33%; padding: 0 8px"></td>
            <td style="width: 33.33%; padding: 0 8px"><?php $helper::renderString( 'bookly_l10n_invoice_info_data', $codes ) ?></td>
        </tr>
        <tr>
            <td colspan="3">&nbsp;</td>
        </tr>
        <tr>
            <td style="padding: 0 8px;font-size: large; font-weight: bold"><?php $helper::renderString( 'bookly_l10n_invoice_bill_to_label', $codes ) ?></td>
        </tr>
        <tr>
            <td style="padding: 0 8px"><?php $helper::renderString( 'bookly_l10n_invoice_bill_to_data', $codes ) ?></td>
        </tr>
        <tr>
            <td colspan="3">&nbsp;</td>
        </tr>
        <tr>
            <?php if ( isset( $payment ) ) : ?>
                <td colspan="3" style="padding: 8px;"><?php $self::renderTemplate( 'order', array( 'translate' => true, 'payment' => $payment, 'adjustments' => $adjustments, 'show' => $show, 'time_zone_offset' => $time_zone_offset, 'time_zone' => $time_zone ) ) ?></td>
            <?php else: ?>
                <td colspan="3" style="padding: 8px;"><?php $self::renderTemplate( 'order_demo', array( 'translate' => false ) ) ?></td>
            <?php endif ?>
        </tr>
        <tr>
            <td colspan="3">&nbsp;</td>
        </tr>
        <tr>
            <td colspan="3" style="text-align: center"><?php $helper::renderString( 'bookly_l10n_invoice_thank_you', $codes ) ?></td>
        </tr>
        <tr>
            <td colspan="3">&nbsp;</td>
        </tr>
        <tr>
            <td colspan="3"><?php $helper::renderImage( 'bookly_invoices_footer_attachment_id', 'w-100' ) ?></td>
        </tr>
        </tbody>
    </table>
</div>