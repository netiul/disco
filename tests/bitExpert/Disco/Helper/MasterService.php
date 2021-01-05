<?php

/*
 * This file is part of the Disco package.
 *
 * (c) bitExpert AG
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace bitExpert\Disco\Helper;

class MasterService
{
    public SampleService $service;

    public function __construct(SampleService $service)
    {
        $this->service = $service;
    }
}
