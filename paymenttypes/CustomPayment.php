<?php namespace Responsiv\Pay\PaymentTypes;

use Responsiv\Pay\Classes\GatewayBase;

/**
 * CustomPayment type
 */
class CustomPayment extends GatewayBase
{
    /**
     * {@inheritDoc}
     */
    public function driverDetails()
    {
        return [
            'name' => 'Custom Payment Method',
            'description' => 'For creating payment forms with custom payment processing, such as offline payments.',
            'offline' => true
        ];
    }

    /**
     * processPaymentForm
     */
    public function processPaymentForm($data, $order)
    {
    }

    /**
     * payOfflineMessage returns the payment instructions for custom payment
     * @return string
     */
    public function payOfflineMessage()
    {
        $host = $this->getHostObject();

        return $host->payment_instructions;
    }

    /**
     * allowNewOrderNotifications should return false to suppress the new order notification
     * when this payment method is assigned
     */
    public function allowNewOrderNotifications($host, $order)
    {
        return !$host->suppress_new_notification;
    }
}
