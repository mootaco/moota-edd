<?php namespace Moota\EDD;

use Moota\SDK\Contracts\MatchPayments;

class OrderMatcher implements MatchPayments
{
    public function match(array $mootaInflows, array $orders)
    {
        $matchedPayments = [];

        $guardedPayments = $mootaInflows;

        if ( ! empty($orders) && count($orders) > 0 ) {
            // match whmcs invoice with moota transactions
            // TODO: apply unique code transformation over here
            foreach ($orders as $order) {
                $transAmount = (float) $order->total;
                $tmpPayment = null;

                foreach ($guardedPayments as $i => $mootaInflow) {
                    if (empty($guardedPayments[ $i ])) continue;

                    if ( ( (float) $mootaInflow['amount'] ) === $transAmount ) {
                        $tmpPayment = $mootaInflow;

                        $guardedPayments[ $i ] = null;

                        break;
                    }
                }

                if (!empty($tmpPayment)) {
                    $matchedPayments[]  = array(
                        'transactionId' => implode('-', array(
                            $order->ID,
                            $tmpPayment['id'],
                            $tmpPayment['account_number']
                        )),

                        'orderId' => $order->ID,
                        'mootaId' => $tmpPayment['id'],
                        'mootaAccNo' => $tmpPayment['account_number'],
                        'amount' => $tmpPayment['amount'],
                        'mootaAmount' => $tmpPayment['amount'],
                        'invoiceAmount' => $order->total,
                        'orderModel' => $order,
                    );
                }
            }
        }

        return $matchedPayments;
    }
}
