<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\Heidelpay\Business\Payment\Request;

use Generated\Shared\Transfer\HeidelpayRequestTransfer;
use Generated\Shared\Transfer\OrderTransfer;
use SprykerEco\Zed\Heidelpay\Business\Mapper\OrderToHeidelpayRequestInterface;
use SprykerEco\Zed\Heidelpay\Business\Payment\PaymentReaderInterface;
use SprykerEco\Zed\Heidelpay\Dependency\Facade\HeidelpayToCurrencyInterface;
use SprykerEco\Zed\Heidelpay\HeidelpayConfig;

class AdapterRequestFromOrderBuilder extends BaseAdapterRequestBuilder implements AdapterRequestFromOrderBuilderInterface
{
    /**
     * @var \SprykerEco\Zed\Heidelpay\Business\Mapper\OrderToHeidelpayRequestInterface
     */
    protected $orderToHeidelpayMapper;

    /**
     * @var \SprykerEco\Zed\Heidelpay\Business\Payment\PaymentReaderInterface
     */
    protected $paymentReader;

    /**
     * @param \SprykerEco\Zed\Heidelpay\Business\Mapper\OrderToHeidelpayRequestInterface $orderToHeidelpayMapper
     * @param \SprykerEco\Zed\Heidelpay\Dependency\Facade\HeidelpayToCurrencyInterface $currencyFacade
     * @param \SprykerEco\Zed\Heidelpay\HeidelpayConfig $config
     * @param \SprykerEco\Zed\Heidelpay\Business\Payment\PaymentReaderInterface $paymentReader
     */
    public function __construct(
        OrderToHeidelpayRequestInterface $orderToHeidelpayMapper,
        HeidelpayToCurrencyInterface $currencyFacade,
        HeidelpayConfig $config,
        PaymentReaderInterface $paymentReader
    ) {
        parent::__construct($currencyFacade, $config);
        $this->orderToHeidelpayMapper = $orderToHeidelpayMapper;
        $this->paymentReader = $paymentReader;
    }

    /**
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     *
     * @return \Generated\Shared\Transfer\HeidelpayRequestTransfer
     */
    public function buildAuthorizeRequestFromOrder(OrderTransfer $orderTransfer): HeidelpayRequestTransfer
    {
        return $this->buildBaseOrderHeidelpayRequest($orderTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     *
     * @return \Generated\Shared\Transfer\HeidelpayRequestTransfer
     */
    public function buildDebitRequestFromOrder(OrderTransfer $orderTransfer): HeidelpayRequestTransfer
    {
        $requestTransfer = $this->buildBaseOrderHeidelpayRequest($orderTransfer);
        $basketId = $this->getBasketId($orderTransfer);
        $requestTransfer->getCustomerPurchase()->setBasketId($basketId);
        return $requestTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     *
     * @return \Generated\Shared\Transfer\HeidelpayRequestTransfer
     */
    public function buildCaptureRequestFromOrder(OrderTransfer $orderTransfer): HeidelpayRequestTransfer
    {
        $requestTransfer = $this->buildBaseOrderHeidelpayRequest($orderTransfer);
        $basketId = $this->getBasketId($orderTransfer);
        $requestTransfer->getCustomerPurchase()->setBasketId($basketId);
        $requestTransfer->setIdPaymentReference($orderTransfer->getHeidelpayPayment()->getIdPaymentReference());

        return $requestTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     *
     * @return \Generated\Shared\Transfer\HeidelpayRequestTransfer
     */
    protected function buildBaseOrderHeidelpayRequest(OrderTransfer $orderTransfer): HeidelpayRequestTransfer
    {
        $requestTransfer = new HeidelpayRequestTransfer();

        $requestTransfer = $this->hydrateOrder($requestTransfer, $orderTransfer);
        $requestTransfer = $this->hydrateRequestData($requestTransfer);

        $paymentMethod = $orderTransfer->getHeidelpayPayment()->getPaymentMethod();

        $requestTransfer = $this->hydrateTransactionChannel($requestTransfer, $paymentMethod);

        return $requestTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\HeidelpayRequestTransfer $heidelpayRequestTransfer
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     *
     * @return \Generated\Shared\Transfer\HeidelpayRequestTransfer
     */
    protected function hydrateOrder(HeidelpayRequestTransfer $heidelpayRequestTransfer, OrderTransfer $orderTransfer): HeidelpayRequestTransfer
    {
        $this->orderToHeidelpayMapper->map($orderTransfer, $heidelpayRequestTransfer);

        return $heidelpayRequestTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     *
     * @return string
     */
    protected function getBasketId(OrderTransfer $orderTransfer)
    {
        return $this->paymentReader->getBasketIdByIdSalesOrder($orderTransfer->getIdSalesOrder());
    }
}
