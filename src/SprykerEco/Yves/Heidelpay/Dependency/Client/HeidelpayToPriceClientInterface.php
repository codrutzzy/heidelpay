<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Yves\Heidelpay\Dependency\Client;

interface HeidelpayToPriceClientInterface
{
    /**
     * @return string
     */
    public function getNetPriceModeIdentifier();
}
