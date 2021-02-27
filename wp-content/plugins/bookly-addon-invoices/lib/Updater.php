<?php
namespace BooklyInvoices\Lib;

use Bookly\Lib;

/**
 * Class Updates
 * @package BooklyInvoices\Lib
 */
class Updater extends Lib\Base\Updater
{
    function update_1_2()
    {
        delete_option( 'bookly_invoices_enabled' );
    }
}