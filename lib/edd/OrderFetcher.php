<?php namespace Moota\EDD;

use Moota\SDK\Contracts\FetchesTransactions;

class OrderFetcher implements FetchesTransactions
{
    public function fetch(array $inflowAmounts)
    {
        $query = new EDD_Payments_Query(array(
            'status' => 'pending',
        ));

        $payments = $query->get_payments();
    }
}
