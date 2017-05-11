<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Heidelpay\Communication\Plugin\Checkout\Oms\Command;

use Generated\Shared\Transfer\OrderTransfer;
use Orm\Zed\Sales\Persistence\SpySalesOrder;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;
use Spryker\Zed\Oms\Business\Util\ReadOnlyArrayObject;
use Spryker\Zed\Oms\Communication\Plugin\Oms\Command\CommandByOrderInterface;

/**
 * @method \Spryker\Zed\Heidelpay\Business\HeidelpayFacade getFacade()
 * @method \Spryker\Zed\Heidelpay\Communication\HeidelpayCommunicationFactory getFactory()
 * @method \Spryker\Zed\Heidelpay\Persistence\HeidelpayQueryContainer getQueryContainer()
 */
class DebitPlugin extends AbstractPlugin implements CommandByOrderInterface
{

    /**
     * @param \Orm\Zed\Sales\Persistence\SpySalesOrderItem[] $orderItems
     * @param \Orm\Zed\Sales\Persistence\SpySalesOrder $orderEntity
     * @param \Spryker\Zed\Oms\Business\Util\ReadOnlyArrayObject $data
     *
     * @return array
     */
    public function run(array $orderItems, SpySalesOrder $orderEntity, ReadOnlyArrayObject $data)
    {
        $orderTransfer = $this->getOrderWithPaymentTransfer($orderEntity->getIdSalesOrder());
        $this->getFacade()->debitPayment($orderTransfer);

        return [];
    }

    /**
     * @param int $idSalesOrder
     *
     * @return \Generated\Shared\Transfer\OrderTransfer
     */
    protected function getOrderWithPaymentTransfer($idSalesOrder)
    {
        $orderTransfer = $this->getFactory()
            ->getSalesFacade()
            ->getOrderByIdSalesOrder($idSalesOrder);

        $orderTransfer = $this->hydrateHeidelpayPayment($orderTransfer);

        return $orderTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     *
     * @return \Generated\Shared\Transfer\OrderTransfer
     */
    protected function hydrateHeidelpayPayment(OrderTransfer $orderTransfer)
    {
        $paymentTransfer = $this->getFacade()->getPaymentByIdSalesOrder($orderTransfer->getIdSalesOrder());
        $orderTransfer->setHeidelpayPayment($paymentTransfer);

        return $orderTransfer;
    }

}
