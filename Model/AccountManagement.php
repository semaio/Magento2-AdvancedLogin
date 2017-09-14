<?php
/**
 * Copyright © 2016 Rouven Alexander Rieker
 * See LICENSE.md bundled with this module for license details.
 */
namespace Semaio\AdvancedLogin\Model;

use Magento\Customer\Model\AccountManagement as CustomerAccountManagement;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Exception\EmailNotConfirmedException;
use Magento\Framework\Exception\InvalidEmailOrPasswordException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Customer\Model\Config\Share;
use Semaio\AdvancedLogin\Model\ConfigProvider as AdvancedLoginConfigProvider;
use Semaio\AdvancedLogin\Model\Config\Source\LoginMode;

/**
 * Class AccountManagement
 *
 * @package Semaio\AdvancedLogin\Model
 */
class AccountManagement extends CustomerAccountManagement
{
    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @var \Magento\Customer\Model\CustomerRegistry
     */
    private $customerRegistry;

    /**
     * @var \Magento\Framework\Encryption\EncryptorInterface
     */
    private $encryptor;

    /**
     * @var \Magento\Customer\Model\CustomerFactory
     */
    private $customerFactory;

    /**
     * @var \Magento\Framework\Event\ManagerInterface
     */
    private $eventManager;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var FilterBuilder
     */
    private $filterBuilder;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var ConfigProvider
     */
    private $advancedLoginConfigProvider;

    /**
     * AccountManagement constructor.
     *
     * @param \Magento\Customer\Model\CustomerFactory                      $customerFactory
     * @param \Magento\Framework\Event\ManagerInterface                    $eventManager
     * @param \Magento\Store\Model\StoreManagerInterface                   $storeManager
     * @param \Magento\Framework\Math\Random                               $mathRandom
     * @param \Magento\Customer\Model\Metadata\Validator                   $validator
     * @param \Magento\Customer\Api\Data\ValidationResultsInterfaceFactory $validationResultsDataFactory
     * @param \Magento\Customer\Api\AddressRepositoryInterface             $addressRepository
     * @param \Magento\Customer\Api\CustomerMetadataInterface              $customerMetadataService
     * @param \Magento\Customer\Model\CustomerRegistry                     $customerRegistry
     * @param \Psr\Log\LoggerInterface                                     $logger
     * @param \Magento\Framework\Encryption\EncryptorInterface             $encryptor
     * @param \Magento\Customer\Model\Config\Share                         $configShare
     * @param \Magento\Framework\Stdlib\StringUtils                        $stringHelper
     * @param \Magento\Customer\Api\CustomerRepositoryInterface            $customerRepository
     * @param \Magento\Framework\App\Config\ScopeConfigInterface           $scopeConfig
     * @param \Magento\Framework\Mail\Template\TransportBuilder            $transportBuilder
     * @param \Magento\Framework\Reflection\DataObjectProcessor            $dataProcessor
     * @param \Magento\Framework\Registry                                  $registry
     * @param \Magento\Customer\Helper\View                                $customerViewHelper
     * @param \Magento\Framework\Stdlib\DateTime                           $dateTime
     * @param \Magento\Customer\Model\Customer                             $customerModel
     * @param \Magento\Framework\DataObjectFactory                         $objectFactory
     * @param \Magento\Framework\Api\ExtensibleDataObjectConverter         $extensibleDataObjectConverter
     * @param SearchCriteriaBuilder                                        $searchCriteriaBuilder
     * @param FilterBuilder                                                $filterBuilder
     * @param ConfigProvider                                               $advancedLoginConfigProvider
     */
    public function __construct(
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Math\Random $mathRandom,
        \Magento\Customer\Model\Metadata\Validator $validator,
        \Magento\Customer\Api\Data\ValidationResultsInterfaceFactory $validationResultsDataFactory,
        \Magento\Customer\Api\AddressRepositoryInterface $addressRepository,
        \Magento\Customer\Api\CustomerMetadataInterface $customerMetadataService,
        \Magento\Customer\Model\CustomerRegistry $customerRegistry,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Encryption\EncryptorInterface $encryptor,
        \Magento\Customer\Model\Config\Share $configShare,
        \Magento\Framework\Stdlib\StringUtils $stringHelper,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\Framework\Reflection\DataObjectProcessor $dataProcessor,
        \Magento\Framework\Registry $registry,
        \Magento\Customer\Helper\View $customerViewHelper,
        \Magento\Framework\Stdlib\DateTime $dateTime,
        \Magento\Customer\Model\Customer $customerModel,
        \Magento\Framework\DataObjectFactory $objectFactory,
        \Magento\Framework\Api\ExtensibleDataObjectConverter $extensibleDataObjectConverter,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        FilterBuilder $filterBuilder,
        AdvancedLoginConfigProvider $advancedLoginConfigProvider
    ) {
        parent::__construct(
            $customerFactory,
            $eventManager,
            $storeManager,
            $mathRandom,
            $validator,
            $validationResultsDataFactory,
            $addressRepository,
            $customerMetadataService,
            $customerRegistry,
            $logger,
            $encryptor,
            $configShare,
            $stringHelper,
            $customerRepository,
            $scopeConfig,
            $transportBuilder,
            $dataProcessor,
            $registry,
            $customerViewHelper,
            $dateTime,
            $customerModel,
            $objectFactory,
            $extensibleDataObjectConverter
        );

        $this->customerRepository = $customerRepository;
        $this->customerRegistry = $customerRegistry;
        $this->encryptor = $encryptor;
        $this->customerFactory = $customerFactory;
        $this->eventManager = $eventManager;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->filterBuilder = $filterBuilder;
        $this->storeManager = $storeManager;
        $this->advancedLoginConfigProvider = $advancedLoginConfigProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function authenticate($username, $password)
    {
        try {
            switch ($this->advancedLoginConfigProvider->getLoginMode()) {
                case LoginMode::LOGIN_TYPE_ONLY_ATTRIBUTE:
                    $customer = $this->loginViaCustomerAttributeOnly($username);
                    break;
                case LoginMode::LOGIN_TYPE_BOTH:
                    $customer = $this->loginViaCustomerAttributeOrEmail($username);
                    break;
                default:
                    $customer = $this->loginViaEmailOnly($username);
                    break;
            }
        } catch (NoSuchEntityException $e) {
            throw new InvalidEmailOrPasswordException(__('Invalid login or password.'));
        }

        $this->checkPasswordStrength($password);
        $hash = $this->customerRegistry->retrieveSecureData($customer->getId())->getPasswordHash();
        if (!$this->encryptor->validateHash($password, $hash)) {
            throw new InvalidEmailOrPasswordException(__('Invalid login or password.'));
        }

        if ($customer->getConfirmation() && $this->isConfirmationRequired($customer)) {
            throw new EmailNotConfirmedException(__('This account is not confirmed.'));
        }

        $customerModel = $this->customerFactory->create()->updateData($customer);
        $this->eventManager->dispatch(
            'customer_customer_authenticated',
            ['model' => $customerModel, 'password' => $password]
        );

        $this->eventManager->dispatch('customer_data_object_login', ['customer' => $customer]);

        return $customer;
    }

    /**
     * Process login by email address
     *
     * @param string $username Username
     * @return \Magento\Customer\Api\Data\CustomerInterface
     */
    private function loginViaEmailOnly($username)
    {
        return $this->customerRepository->get($username);
    }

    /**
     * Process login by customer attribute
     *
     * @param string $username Username
     * @return bool|\Magento\Customer\Api\Data\CustomerInterface
     * @throws NoSuchEntityException
     */
    private function loginViaCustomerAttributeOnly($username)
    {
        $customer = $this->findCustomerByLoginAttribute($username);
        if (false == $customer) {
            throw new NoSuchEntityException();
        }

        return $customer;
    }

    /**
     * Process login by customer attribute or email
     *
     * @param string $username Username
     * @return bool|\Magento\Customer\Api\Data\CustomerInterface
     */
    private function loginViaCustomerAttributeOrEmail($username)
    {
        $customer = $this->findCustomerByLoginAttribute($username);
        if (false === $customer) {
            $customer = $this->customerRepository->get($username);
        }

        return $customer;
    }

    /**
     * Find a customer
     *
     * @param string $attributeValue Attribute Value
     * @return bool|\Magento\Customer\Api\Data\CustomerInterface
     */
    private function findCustomerByLoginAttribute($attributeValue)
    {
        // Retrieve the customer login attribute and check if valid
        $loginAttribute = $this->advancedLoginConfigProvider->getLoginAttribute();
        if (false === $loginAttribute) {
            return false;
        }

        // Add website filter if customer accounts are shared per website
        $websiteIdFilter = false;
        if ($this->advancedLoginConfigProvider->getCustomerAccountShareScope() == Share::SHARE_WEBSITE) {
            $websiteIdFilter[] = $this->filterBuilder
                ->setField('website_id')
                ->setConditionType('eq')
                ->setValue($this->storeManager->getStore()->getWebsiteId())
                ->create();
        }

        // Add customer attribute filter
        $customerNumberFilter[] = $this->filterBuilder
            ->setField($this->advancedLoginConfigProvider->getLoginAttribute())
            ->setConditionType('eq')
            ->setValue($attributeValue)
            ->create();

        // Build search criteria
        $searchCriteriaBuilder = $this->searchCriteriaBuilder->addFilters($customerNumberFilter);
        if ($websiteIdFilter) {
            $searchCriteriaBuilder->addFilters($websiteIdFilter);
        }
        $searchCriteria = $searchCriteriaBuilder->create();

        // Retrieve the customer collection and return customer if there was exactly one customer found
        $collection = $this->customerRepository->getList($searchCriteria);
        if ($collection->getTotalCount() == 1) {
            return $collection->getItems()[0];
        }

        return false;
    }
}
