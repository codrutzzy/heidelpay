<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\Heidelpay\Business\Payment\Transaction;

interface TransactionLogReaderInterface
{
    /**
     * @param int $idSalesOrder
     *
     * @return \Generated\Shared\Transfer\HeidelpayTransactionLogTransfer|null
     */
    public function findOrderAuthorizeTransactionLogByIdSalesOrder($idSalesOrder);

    /**
     * @param string $orderReference
     *
     * @return \Generated\Shared\Transfer\HeidelpayTransactionLogTransfer|null
     */
    public function findOrderAuthorizeTransactionLogByOrderReference($orderReference);

    /**
     * @param int $orderReference
     *
     * @return \Generated\Shared\Transfer\HeidelpayTransactionLogTransfer|null
     */
    public function findOrderDebitTransactionLog($orderReference);

    /**
     * @param int $idSalesOrder
     *
     * @return \Generated\Shared\Transfer\HeidelpayTransactionLogTransfer|null
     */
    public function findOrderAuthorizeOnRegistrationTransactionLogByIdSalesOrder($idSalesOrder);

    /**
     * @param int $idSalesOrder
     *
     * @return \Generated\Shared\Transfer\HeidelpayTransactionLogTransfer|null
     */
    public function findQuoteInitializeTransactionLogByIdSalesOrder($idSalesOrder);

    /**
     * @param string $orderReference
     *
     * @return \Generated\Shared\Transfer\HeidelpayTransactionLogTransfer|null
     */
    public function findOrderAuthorizeOnRegistrationTransactionLogByOrderReference($orderReference);

    /**
     * @param string $orderReference
     *
     * @return \Generated\Shared\Transfer\HeidelpayTransactionLogTransfer|null
     */
    public function findQuoteInitializeTransactionLogByOrderReference($orderReference);
}
