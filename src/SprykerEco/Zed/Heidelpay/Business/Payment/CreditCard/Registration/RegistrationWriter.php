<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Heidelpay\Business\Payment\CreditCard\Registration;

use Generated\Shared\Transfer\QuoteTransfer;
use Orm\Zed\Heidelpay\Persistence\SpyPaymentHeidelpayCreditCardRegistration;
use SprykerEco\Zed\Heidelpay\Persistence\HeidelpayQueryContainerInterface;

class RegistrationWriter implements RegistrationWriterInterface
{
    /**
     * @var \SprykerEco\Zed\Heidelpay\Persistence\HeidelpayQueryContainerInterface
     */
    private $heidelpayQueryContainer;

    /**
     * @param \SprykerEco\Zed\Heidelpay\Persistence\HeidelpayQueryContainerInterface $heidelpayQueryContainer
     */
    public function __construct(HeidelpayQueryContainerInterface $heidelpayQueryContainer)
    {
        $this->heidelpayQueryContainer = $heidelpayQueryContainer;
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return void
     */
    public function updateRegistrationWithAddressIdFromQuote(QuoteTransfer $quoteTransfer): void
    {
        $registrationEntity = $this->findRegistrationFromQuote($quoteTransfer);

        if ($registrationEntity !== null) {
            $registrationEntity
                ->setFkCustomerAddress(
                    $quoteTransfer->getShippingAddress()->getIdCustomerAddress()
                )
                ->save();
        }
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Orm\Zed\Heidelpay\Persistence\SpyPaymentHeidelpayCreditCardRegistration
     */
    protected function fetchRegistrationFromQuote(QuoteTransfer $quoteTransfer): SpyPaymentHeidelpayCreditCardRegistration
    {
        $registrationEntity = new SpyPaymentHeidelpayCreditCardRegistration();
        $this->fillRegistrationEntityFromQuoteTransfer($quoteTransfer, $registrationEntity);

        return $registrationEntity;
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Orm\Zed\Heidelpay\Persistence\SpyPaymentHeidelpayCreditCardRegistration
     */
    protected function findRegistrationFromQuote(QuoteTransfer $quoteTransfer): SpyPaymentHeidelpayCreditCardRegistration
    {
        $registrationHash = $quoteTransfer
            ->getPayment()
            ->getHeidelpayCreditCardSecure()
            ->getSelectedRegistration()
            ->getRegistrationNumber();

        $registrationEntity = $this->heidelpayQueryContainer
            ->queryCreditCardRegistrationByRegistrationNumber(
                $registrationHash
            )
            ->findOne();

        return $registrationEntity;
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param \Orm\Zed\Heidelpay\Persistence\SpyPaymentHeidelpayCreditCardRegistration $registrationEntity
     *
     * @return void
     */
    protected function fillRegistrationEntityFromQuoteTransfer(
        QuoteTransfer $quoteTransfer,
        SpyPaymentHeidelpayCreditCardRegistration $registrationEntity
    ): void {
        $creditCardPayment = $quoteTransfer
            ->getPayment()
            ->getHeidelpayCreditCardSecure()
            ->getSelectedRegistration();

        $creditCardInfo = $creditCardPayment->getCreditCardInfo();

        $registrationEntity->setFkCustomerAddress($quoteTransfer->getShippingAddress()->getIdCustomerAddress())
            ->setAccountVerification($creditCardInfo->getAccountVerification())
            ->setAccountNumber($creditCardInfo->getAccountNumber())
            ->setAccountHolder($creditCardInfo->getAccountHolder())
            ->setAccountExpiryYear((int)$creditCardInfo->getAccountExpiryYear())
            ->setAccountExpiryMonth((int)$creditCardInfo->getAccountExpiryMonth())
            ->setAccountBrand($creditCardInfo->getAccountBrand())
            ->setRegistrationNumber($creditCardPayment->getRegistrationNumber());
    }

    /**
     * @param \Orm\Zed\Heidelpay\Persistence\SpyPaymentHeidelpayCreditCardRegistration $registrationEntity
     *
     * @return bool
     */
    protected function isRegistrationExists(SpyPaymentHeidelpayCreditCardRegistration $registrationEntity): bool
    {
        return $this->heidelpayQueryContainer
            ->queryCreditCardRegistrationByRegistrationNumber(
                $registrationEntity->getRegistrationNumber()
            )
            ->exists();
    }
}
