<?php
namespace BooklyInvoices\Backend\Modules\Notifications\ProxyProviders;

use Bookly\Backend\Modules\Notifications\Proxy;

/**
 * Class Shared
 * @package BooklyInvoices\Backend\Modules\Notifications\ProxyProviders
 */
class Shared extends Proxy\Shared
{
    /**
     * @inheritdoc
     */
    public static function prepareNotificationCodes( array $codes, $type )
    {
        $codes['invoice']['invoice_date']     = __( 'invoice creation date', 'bookly' );
        $codes['invoice']['invoice_due_date'] = __( 'due date of invoice', 'bookly' );
        $codes['invoice']['invoice_due_days'] = __( 'number of days to submit payment', 'bookly' );
        $codes['invoice']['invoice_link']     = __( 'invoice link', 'bookly' );
        $codes['invoice']['invoice_number']   = __( 'invoice number', 'bookly' );

        return $codes;
    }
}