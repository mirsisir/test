<?php

namespace BooklyStripe\Lib\Payment\Lib\Stripe;

/**
 * A Mandate is a record of the permission a customer has given you to debit their
 * payment method.
 *
 * @property string $id Unique identifier for the object.
 * @property string $object String representing the object's type. Objects of the same type share the same value.
 * @property \BooklyStripe\Lib\Payment\Lib\Stripe\StripeObject $customer_acceptance
 * @property bool $livemode Has the value <code>true</code> if the object exists in live mode or the value <code>false</code> if the object exists in test mode.
 * @property \BooklyStripe\Lib\Payment\Lib\Stripe\StripeObject $multi_use
 * @property string|\BooklyStripe\Lib\Payment\Lib\Stripe\PaymentMethod $payment_method ID of the payment method associated with this mandate.
 * @property \BooklyStripe\Lib\Payment\Lib\Stripe\StripeObject $payment_method_details
 * @property \BooklyStripe\Lib\Payment\Lib\Stripe\StripeObject $single_use
 * @property string $status The status of the mandate, which indicates whether it can be used to initiate a payment.
 * @property string $type The type of the mandate.
 */
class Mandate extends ApiResource
{
    const OBJECT_NAME = 'mandate';

    use ApiOperations\Retrieve;
}
