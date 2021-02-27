<?php
namespace BooklyInvoices\Lib;

use Bookly\Lib as BooklyLib;

/**
 * Class Installer
 * @package BooklyInvoices\Lib
 */
class Installer extends Base\Installer
{
    /** @var array */
    protected $notifications = array();

    /**
     * Constructor.
     */
    public function __construct()
    {
        // Notifications email & sms.
        $default_settings = json_decode( '{"status":"any","option":2,"services":{"any":"any","ids":[]},"offset_hours":2,"perform":"before","at_hour":9,"before_at_hour":18,"offset_before_hours":-24,"offset_bidirectional_hours":0}', true );
        $default_settings['option'] = 1;
        $default_settings['status'] = 'pending';
        $settings = json_encode( $default_settings );
        $this->notifications[] = array(
            'gateway'     => 'email',
            'type'        => 'new_booking',
            'name'        => __( 'Invoice #{invoice_number} for your appointment', 'bookly' ),
            'subject'     => __( 'Invoice #{invoice_number} for your appointment', 'bookly' ),
            'message'     => __( "Dear {client_name}.\n\nAttached please find invoice #{invoice_number} for your appointment.\n\nThank you for choosing our company.\n\n{company_name}\n{company_phone}\n{company_website}", 'bookly' ),
            'to_customer' => 1,
            'settings'    => $settings,
            'attach_invoice' => 1,
        );
        $this->notifications[] = array(
            'gateway'     => 'sms',
            'type'        => 'new_booking',
            'name'        => __( 'New invoice', 'bookly' ),
            'subject'     => __( 'New invoice', 'bookly' ),
            'message'     => __( "Hello.\nYou have a new invoice #{invoice_number} for an appointment scheduled by {client_first_name} {client_last_name}.\nPlease download invoice here: {invoice_link}", 'bookly' ),
            'to_staff'    => 1,
            'settings'    => $settings,
        );

        $this->options = array(
            'bookly_invoices_due_days'          => 30,
            'bookly_invoices_footer_attachment_id' => '',
            'bookly_invoices_header_attachment_id' => '',
            'bookly_l10n_invoice_bill_to_label' => __( 'BILL TO', 'bookly' ) . ':',
            'bookly_l10n_invoice_bill_to_data'  => '{client_name}' . PHP_EOL . '{client_address}' . PHP_EOL . '{client_phone}',
            'bookly_l10n_invoice_company_label' => '{company_name}',
            'bookly_l10n_invoice_company_data'  => '{company_address}' . PHP_EOL . '{company_phone}' . PHP_EOL . '{company_website}',
            'bookly_l10n_invoice_company_logo'  => '{company_logo}',
            'bookly_l10n_invoice_info_data'     => __( 'Invoice#', 'bookly' ) . ' {invoice_number}' . PHP_EOL .  __( 'Date', 'bookly' ) . ': {invoice_date}' . PHP_EOL . __( 'Due date', 'bookly' ) . ': {invoice_due_date}',
            'bookly_l10n_invoice_label'         => __( 'INVOICE', 'bookly' ),
            'bookly_l10n_invoice_thank_you'     => __( 'Thank you for your business', 'bookly' ),
        );
    }

    public function loadData()
    {
        parent::loadData();

        // Insert notifications.
        foreach ( $this->notifications as $data ) {
            $notification = new BooklyLib\Entities\Notification();
            $notification->setFields( $data )->save();
        }
    }
}