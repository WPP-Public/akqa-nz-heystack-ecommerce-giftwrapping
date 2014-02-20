<?php

namespace Heystack\Subsystem\GiftWrapping;

use Heystack\Subsystem\Core\Identifier\Identifier;
use Heystack\Subsystem\Core\Interfaces\HasDataInterface;
use Heystack\Subsystem\Core\Interfaces\HasStateServiceInterface;
use Heystack\Subsystem\Core\State\State;
use Heystack\Subsystem\Core\Storage\Backends\SilverStripeOrm\Backend;
use Heystack\Subsystem\Core\Storage\StorableInterface;
use Heystack\Subsystem\Ecommerce\Currency\Interfaces\CurrencyServiceInterface;
use Heystack\Subsystem\Ecommerce\Transaction\Traits\TransactionModifierSerializeTrait;
use Heystack\Subsystem\Ecommerce\Transaction\Traits\TransactionModifierStateTrait;
use Heystack\Subsystem\Ecommerce\Transaction\TransactionModifierTypes;
use Heystack\Subsystem\GiftWrapping\Interfaces\GiftWrappingHandlerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class GiftWrappingHandler implements GiftWrappingHandlerInterface, StorableInterface, \Serializable, HasStateServiceInterface, HasDataInterface
{
    use TransactionModifierStateTrait;
    use TransactionModifierSerializeTrait;

    const IDENTIFIER = 'gift-wrapping-handler';
    const TOTAL_KEY = 'total';
    const ACTIVE_KEY = 'active';
    const CONFIG_KEY = 'config';

    /**
     * @var State
     */
    protected $stateService;
    protected $eventService;
    protected $currencyService;
    protected $data;

    /**
     * @param $total
     * @param State $stateService
     */
    public function __construct(State $stateService, EventDispatcherInterface $eventService, CurrencyServiceInterface $currencyService)
    {
        $this->stateService = $stateService;
        $this->eventService = $eventService;
        $this->currencyService = $currencyService;
    }

    public function setActive($active)
    {
        $this->data[self::ACTIVE_KEY] = (bool) $active;

        $this->updateTotal();
    }

    public function isActive()
    {
        return isset($this->data[self::ACTIVE_KEY]) ? $this->data[self::ACTIVE_KEY] : false;
    }

    /**
     * Returns a unique identifier for use in the Transaction
     * @return \Heystack\Subsystem\Core\Identifier\Identifier
     */
    public function getIdentifier()
    {
        return new Identifier(self::IDENTIFIER);
    }

    /**
     * Returns the total value of the TransactionModifier for use in the Transaction
     */
    public function getTotal()
    {
        return isset($this->data[self::TOTAL_KEY]) ? $this->data[self::TOTAL_KEY] : 0;
    }

    public function updateTotal()
    {
        $total = 0;

        if ($this->isActive()) {

            $total = $this->getCost();

        }

        $this->data[self::TOTAL_KEY] = $total;

        $this->saveState();

        $this->eventService->dispatch(Events::TOTAL_UPDATED);

    }

    public function getCost()
    {
        $currencyCode = $this->currencyService->getActiveCurrencyCode();

        return isset($this->data[self::CONFIG_KEY][$currencyCode][self::CONFIG_PRICE_KEY]) ? $this->data[self::CONFIG_KEY][$currencyCode][self::CONFIG_PRICE_KEY] : 0;
    }

    public function getMessage()
    {
        $currencyCode = $this->currencyService->getActiveCurrencyCode();

        return isset($this->data[self::CONFIG_KEY][$currencyCode][self::CONFIG_MESSAGE_KEY]) ? $this->data[self::CONFIG_KEY][$currencyCode][self::CONFIG_MESSAGE_KEY] : '';
    }

    /**
     * Indicates the type of amount the modifier will return
     * Must return a constant from TransactionModifierTypes
     */
    public function getType()
    {
        return $this->isActive() ? TransactionModifierTypes::CHARGEABLE : TransactionModifierTypes::NEUTRAL;
    }

    /**
     * @return mixed
     */
    public function getStorableIdentifier()
    {
        return self::IDENTIFIER;
    }

    /**
     * @return mixed
     */
    public function getStorableData()
    {
        return [
            'id' => 'GiftWrapping',
            'flat' => [
                'Total' => $this->getTotal(),
                'Active' => $this->isActive()
            ]
        ];
    }

    /**
     * @return mixed
     */
    public function getStorableBackendIdentifiers()
    {
        return [
            Backend::IDENTIFIER
        ];
    }

    /**
     * @return mixed
     */
    public function getSchemaName()
    {
        return 'GiftWrapping';
    }

    /**
     * @param array $data
     */
    public function setData($data)
    {
        $this->data = $data;
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param HasStateServiceInterface $eventService
     * @return mixed
     */
    public function setStateService(State $stateService)
    {
        $this->stateService = $stateService;
    }

    public function getStateService()
    {
        return $this->stateService;
    }

    public function setConfig(array $config)
    {
        $this->data[self::CONFIG_KEY] = $config;
    }

    public function getConfig()
    {
        return isset($this->data[self::CONFIG_KEY]) ? $this->data[self::CONFIG_KEY] : null;
    }


}
