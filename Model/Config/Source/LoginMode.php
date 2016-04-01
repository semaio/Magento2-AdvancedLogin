<?php
/**
 * Copyright © 2016 Rouven Alexander Rieker
 * See LICENSE.md bundled with this module for license details.
 */
namespace Semaio\AdvancedLogin\Model\Config\Source;

/**
 * Class LoginType
 *
 * @package Semaio\AdvancedLogin\Model\Config\Source
 */
class LoginMode
{
    const LOGIN_TYPE_ONLY_EMAIL = 0;
    const LOGIN_TYPE_ONLY_ATTRIBUTE = 1;
    const LOGIN_TYPE_BOTH = 2;

    /**
     * Retrieve possible customer address types
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            self::LOGIN_TYPE_ONLY_EMAIL     => __('Login via email only'),
            self::LOGIN_TYPE_ONLY_ATTRIBUTE => __('Login via customer attribute only'),
            self::LOGIN_TYPE_BOTH           => __('Login via customer attribute and email'),
        ];
    }
}
