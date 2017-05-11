<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Client\Heidelpay;

use Spryker\Shared\Heidelpay\HeidelpayConstants;
use Spryker\Zed\Kernel\AbstractBundleConfig;

class HeidelpayConfig extends AbstractBundleConfig
{

    /**
     * @return string
     */
    public function getApplicationSecret()
    {
        return $this->get(HeidelpayConstants::CONFIG_HEIDELPAY_APPLICATION_SECRET);
    }

}
