<?php
/**
 * Copyright © 2016 Rouven Alexander Rieker
 * See LICENSE.md bundled with this module for license details.
 */
namespace Semaio\AdvancedLogin\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class ConfigProvider
 *
 * @package Semaio\AdvancedLogin\Model
 */
class ConfigProvider
{
    const XML_PATH_ADVANCEDLOGIN_LOGIN_MODE = 'customer/advancedlogin/login_mode';
    const XML_PATH_ADVANCEDLOGIN_LOGIN_ATTRIBUTE = 'customer/advancedlogin/login_attribute';
    const XML_PATH_CUSTOMER_ACCOUNT_SHARE_SCOPE = 'customer/account_share/scope';

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * ConfigProvider constructor.
     *
     * @param ScopeConfigInterface  $scopeConfig
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(ScopeConfigInterface $scopeConfig, StoreManagerInterface $storeManager)
    {
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
    }

    /**
     * Retrieve the configured login mode
     *
     * @return int
     */
    public function getLoginMode()
    {
        return (int)$this->scopeConfig->getValue(self::XML_PATH_ADVANCEDLOGIN_LOGIN_MODE);
    }

    /**
     * Retrieve the custoemr account share scope
     *
     * @return int
     */
    public function getCustomerAccountShareScope()
    {
        return (int)$this->scopeConfig->getValue(self::XML_PATH_CUSTOMER_ACCOUNT_SHARE_SCOPE);
    }

    /**
     * Retrieve the customer attribute for login
     *
     * @return string
     */
    public function getLoginAttribute()
    {
        $attribute = (string)$this->scopeConfig->getValue(self::XML_PATH_ADVANCEDLOGIN_LOGIN_ATTRIBUTE);
        $attribute = trim($attribute);
        if ($attribute == '') {
            return false;
        }

        return $attribute;
    }
}
