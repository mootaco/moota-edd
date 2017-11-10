<?php namespace Moota\EDD;

use Moota\SDK\Contracts\Push\FullfilsOrder;

class OrderFullfiler implements FullfilsOrder
{
    public function fullfil($order)
    {
        $orderId = $order['orderId'];
        $orderModel = $order['orderModel'];
        $orderModel->status = 'publish';

        if ($modelSaved = $orderModel->save()) {
            $orderModel->update_meta(
                '_edd_completed_date',
                date('Y-m-d H:i:s')
            );

            $orderModel->update_meta(
                '_edd_payment_transaction_id',
                $order['transactionId']
            );

            $note = "Payment applied from Moota, MootaID: {$order['mootaId']}"
                . ", amount: {$order['mootaAmount']}";

            wp_insert_comment( wp_filter_comment( array(
                'comment_post_ID'      => $orderId,
                'comment_content'      => $note,
                'user_id'              => 0,
                'comment_date'         => current_time( 'mysql' ),
                'comment_date_gmt'     => current_time( 'mysql', 1 ),
                'comment_approved'     => 1,
                'comment_parent'       => 0,
                'comment_author'       => '',
                'comment_author_IP'    => '',
                'comment_author_url'   => '',
                'comment_author_email' => '',
                'comment_type'         => 'edd_payment_note'
            ) ) );
        }

        return $modelSaved;
    }
}
