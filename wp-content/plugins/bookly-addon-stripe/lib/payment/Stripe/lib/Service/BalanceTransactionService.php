<?php

namespace BooklyStripe\Lib\Payment\Lib\Stripe\Service;

class BalanceTransactionService extends \BooklyStripe\Lib\Payment\Lib\Stripe\Service\AbstractService
{
    /**
     * Returns a list of transactions that have contributed to the Stripe account
     * balance (e.g., charges, transfers, and so forth). The transactions are returned
     * in sorted order, with the most recent transactions appearing first.
     *
     * Note that this endpoint was previously called “Balance history” and used the
     * path <code>/v1/balance/history</code>.
     *
     * @param null|array $params
     * @param null|array|\BooklyStripe\Lib\Payment\Lib\Stripe\Util\RequestOptions $opts
     *
     * @throws \BooklyStripe\Lib\Payment\Lib\Stripe\Exception\ApiErrorException if the request fails
     *
     * @return \BooklyStripe\Lib\Payment\Lib\Stripe\Collection
     */
    public function all($params = null, $opts = null)
    {
        return $this->requestCollection('get', '/v1/balance_transactions', $params, $opts);
    }

    /**
     * Retrieves the balance transaction with the given ID.
     *
     * Note that this endpoint previously used the path
     * <code>/v1/balance/history/:id</code>.
     *
     * @param string $id
     * @param null|array $params
     * @param null|array|\BooklyStripe\Lib\Payment\Lib\Stripe\Util\RequestOptions $opts
     *
     * @throws \BooklyStripe\Lib\Payment\Lib\Stripe\Exception\ApiErrorException if the request fails
     *
     * @return \BooklyStripe\Lib\Payment\Lib\Stripe\BalanceTransaction
     */
    public function retrieve($id, $params = null, $opts = null)
    {
        return $this->request('get', $this->buildPath('/v1/balance_transactions/%s', $id), $params, $opts);
    }
}
