<?php namespace Responsiv\Pay\Models\Invoice;

use Responsiv\Pay\Models\InvoiceItem;
use Responsiv\Shop\Models\TaxClass;

/**
 * HasCalculatedAttributes
 */
trait HasCalculatedAttributes
{
    /**
     * evalInvoiceTotals
     */
    public function evalInvoiceTotals(array $options = [])
    {
        extract(array_merge([
            'items' => null,
            'address' => null,
            'pricesIncludeTax' => null,
        ], $options));

        // Locate address
        if ($address === null) {
            $address = $this->getTaxableAddress();
        }

        if ($pricesIncludeTax === null) {
            $pricesIncludeTax = $this->prices_include_tax;
        }

        TaxClass::setLocationContext($address);
        TaxClass::setPricesIncludeTax($pricesIncludeTax);

        // Calculate totals for order items
        if ($items !== null) {
            $this->discount = 0;
            $this->discount_tax = 0;
            $this->total_cost = 0;
            $this->subtotal = 0;

            foreach ($items as $item) {
                if ($item instanceof InvoiceItem) {
                    $this->discount += $item->quantity * $item->discount;
                    $this->discount_tax += $item->quantity * ($item->discount_with_tax - $item->discount);
                    $this->total_cost += $item->quantity * $item->cost;
                    $this->subtotal += $item->quantity * $item->price;
                }
            }

            // Calculate item taxes
            $cartTaxes = TaxClass::calculateInvoiceTaxes($items);
            $this->sales_taxes = $cartTaxes['taxes'];
            $this->sales_tax = $cartTaxes['taxTotal'];
        }

        // Reset total
        $this->total = $this->final_subtotal;
        $this->total_tax = $this->sales_tax - $this->discount_tax;
    }

    /**
     * fillFromOptions
     */
    public function fillFromOptions(array $options = [])
    {
        extract(array_merge([
            'billingAddress' => null,
            'paymentMethod' => null,
            'isFinal' => true,
        ], $options));


        if ($billingAddress) {
            $billingAddress->saveToInvoice($this);
        }

        if ($paymentMethod) {
            $this->payment_method = $paymentMethod;
        }

        if (!$isFinal) {
            $this->is_throwaway = true;
        }
    }
}
