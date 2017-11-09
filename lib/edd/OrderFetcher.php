<?php namespace Moota\EDD;

use Moota\SDK\Contracts\FetchesTransactions;

class OrderFetcher implements FetchesTransactions
{
    public function fetch(array $inflowAmounts)
    {
        $query = new \EDD_Payments_Query(array(
            'status' => 'pending',
        ));

        $tmpPayments = $query->get_payments();

        $payments = array();

        if ( count($inflowAmounts) < 1 ) {
            return $tmpPayments;
        }

        foreach ($tmpPayments as $payment) {
            $fPayment = (float) $payment->total;

            if ( in_array( $fPayment, $inflowAmounts ) ) {
                $payments[] = $payment;
            }
        }

        return $payments;
    }
}
