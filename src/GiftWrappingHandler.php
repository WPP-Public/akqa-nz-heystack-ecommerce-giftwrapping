<?php

namespace Heystack\GiftWrapping;

use Heystack\Core\EventDispatcher;
use Heystack\Core\Identifier\Identifier;
use Heystack\Core\Interfaces\HasEventServiceInterface;
use Heystack\Core\Interfaces\HasStateServiceInterface;
use Heystack\Core\State\State;
use Heystack\Core\State\StateableInterface;
use Heystack\Core\Storage\Backends\SilverStripeOrm\Backend;
use Heystack\Core\Storage\StorableInterface;
use Heystack\Core\Traits\HasEventServiceTrait;
use Heystack\Core\Traits\HasStateServiceTrait;
use Heystack\Ecommerce\Currency\Interfaces\CurrencyServiceInterface;
use Heystack\Ecommerce\Currency\Interfaces\HasCurrencyServiceInterface;
use Heystack\Ecommerce\Currency\Traits\HasCurrencyServiceTrait;
use Heystack\Ecommerce\Transaction\Traits\TransactionModifierSerializeTrait;
use Heystack\Ecommerce\Transaction\Traits\TransactionModifierStateTrait;
use Heystack\Ecommerce\Transaction\TransactionModifierTypes;
use Heystack\GiftWrapping\Interfaces\GiftWrappingHandlerInterface;
use SebastianBergmann\Money\Money;

/**
 * Class GiftWrappingHandler
 * @package Heystack\GiftWrapping
 */
class GiftWrappingHandler
    implements
        GiftWrappingHandlerInterface,
        StorableInterface,
        \Serializable,
        HasStateServiceInterface,
        HasCurrencyServiceInterface,
        HasEventServiceInterface,
        StateableInterface
{
    use HasStateServiceTrait;
    use HasCurrencyServiceTrait;
    use HasEventServiceTrait;
    use TransactionModifierSerializeTrait;

    /**
     *
     */
    const IDENTIFIER = 'GiftWrapping';

    /**
     * @var \SebastianBergmann\Money\Money
     */
    protected $total;

    /**
     * @var bool
     */
    protected $active = false;

    /**
     * @var array|null
     */
    protected $config;

    /**
     * @param \Heystack\Core\State\State $stateService
     * @param \Heystack\Core\EventDispatcher $eventService
     * @param \Heystack\Ecommerce\Currency\Interfaces\CurrencyServiceInterface $currencyService
     */
    public function __construct(
        State $stateService,
        EventDispatcher $eventService,
        CurrencyServiceInterface $currencyService
    )
    {
        $this->stateService = $stateService;
        $this->eventService = $eventService;
        $this->currencyService = $currencyService;
        $this->total = $this->currencyService->getZeroMoney();
    }

    /**
     * @param bool $active
     * @return void
     */
    public function setActive($active)
    {
        $this->active = $active;

        $this->updateTotal();
    }

    /**
     * @return bool
     */
    public function isActive()
    {
        return $this->active;
    }

    /**
     * Returns a unique identifier for use in the Transaction
     * @return \Heystack\Core\Identifier\Identifier
     */
    public function getIdentifier()
    {
        return new Identifier(self::IDENTIFIER);
    }

    /**
     * Returns the total value of the TransactionModifier for use in the Transaction
     * @return \SebastianBergmann\Money\Money
     */
    public function getTotal()
    {
        return $this->total;
    }

    /**
     * @return void
     */
    public function updateTotal()
    {
        if ($this->isActive()) {
            $this->total = $this->getCost();
        } else {
            $this->total = $this->currencyService->getZeroMoney();
        }

        $this->saveState();
        
        $this->eventService->dispatch(Events::TOTAL_UPDATED);
    }

    /**
     * @return \SebastianBergmann\Money\Money
     */
    public function getCost()
    {
        $currency = $this->currencyService->getActiveCurrency();
        $currencyCode = $currency->getCurrencyCode();

        if ($this->config && isset($this->config[$currencyCode][self::CONFIG_PRICE_KEY])) {
            return \Heystack\Ecommerce\convertStringToMoney($this->config[$currencyCode][self::CONFIG_PRICE_KEY], $currency);
        } else {
            return $this->currencyService->getZeroMoney();
        }
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        $currency = $this->currencyService->getActiveCurrency();
        $currencyCode = $currency->getCurrencyCode();
        
        if (isset($this->config[$currencyCode][self::CONFIG_MESSAGE_KEY])) {
            return $this->config[$currencyCode][self::CONFIG_MESSAGE_KEY];
        } else {
            return '';
        }
    }

    /**
     * Indicates the type of amount the modifier will return
     * Must return a constant from TransactionModifierTypes
     * @return string
     */
    public function getType()
    {
        return $this->isActive() ? TransactionModifierTypes::CHARGEABLE : TransactionModifierTypes::NEUTRAL;
    }

    /**
     * @return string
     */
    public function getStorableIdentifier()
    {
        return self::IDENTIFIER;
    }

    /**
     * @return array
     */
    public function getStorableData()
    {
        return [
            'id' => self::IDENTIFIER,
            'flat' => [
                'Total' => \Heystack\Ecommerce\convertMoneyToString($this->total),
                'Active' => $this->isActive()
            ]
        ];
    }

    /**
     * @return array
     */
    public function getStorableBackendIdentifiers()
    {
        return [
            Backend::IDENTIFIER
        ];
    }

    /**
     * @return string
     */
    public function getSchemaName()
    {
        return 'GiftWrapping';
    }

    /**
     * @param array $config
     * @return void
     */
    public function setConfig(array $config)
    {
        $this->config = $config;
    }

    /**
     * @return mixed
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * @return void
     */
    public function saveState()
    {
        $this->stateService->setByKey(
            self::IDENTIFIER,
            $this->getData()
        );
    }

    /**
     * @return void
     */
    public function restoreState()
    {
        $this->setData($this->stateService->getByKey(self::IDENTIFIER));
    }

    /**
     * @return array
     */
    public function getData()
    {
        return [
            $this->active,
            $this->total,
            $this->config
        ];
    }

    /**
     * @param mixed $data
     * @return void
     */
    public function setData($data)
    {
        if (is_array($data)) {
            list($this->active, $this->total, $this->config) = $data;
        }
    }
}
