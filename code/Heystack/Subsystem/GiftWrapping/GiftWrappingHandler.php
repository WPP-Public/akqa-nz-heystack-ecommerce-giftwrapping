<?php

namespace Heystack\Subsystem\GiftWrapping;

use Heystack\Subsystem\Core\Identifier\Identifier;
use Heystack\Subsystem\Core\State\State;
use Heystack\Subsystem\Core\Storage\StorableInterface;
use Heystack\Subsystem\Core\ViewableData\ViewableDataInterface;
use Heystack\Subsystem\Ecommerce\Transaction\Interfaces\TransactionModifierInterface;
use Heystack\Subsystem\Ecommerce\Transaction\TransactionModifierTypes;
use Heystack\Subsystem\Core\Storage\Backends\SilverStripeOrm\Backend;

class GiftWrappingHandler implements TransactionModifierInterface, ViewableDataInterface, StorableInterface
{
    const IDENTIFIER = 'gift-wrapping';

    /**
     * @var State
     */
    protected $stateService;

    protected $total;

    /**
     * @param $total
     * @param State $stateService
     */
    public function __construct($total, State $stateService)
    {
        $this->total = $total;
        $this->stateService = $stateService;
    }

    /**
     * Defines what methods the implementing class implements dynamically through __get and __set
     */
    public function getDynamicMethods()
    {
        return array(
            'getTotal',
            'isActive'
        );
    }

    /**
     * Returns an array of SilverStripe DBField castings keyed by field name
     */
    public function getCastings()
    {
        return array(
            'getTotal' => 'Money',
            'isActive' => 'Boolean'
        );
    }

    public function setValue($value)
    {
        $this->total = $value;
    }

    public function getValue()
    {
        return $this->total;
    }

    public function isActive()
    {
        return $this->stateService->getByKey(self::IDENTIFIER);
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
        if ($this->isActive()) {
            return $this->total;
        } else {
            return 0;
        }
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
        return array(
            'id' => 'GiftWrapping',
            'flat' => array(
                'Total' => $this->getTotal(),
                'Active' => $this->isActive()
            )
        );
    }

    /**
     * @return mixed
     */
    public function getStorableBackendIdentifiers()
    {
        return array(
            Backend::IDENTIFIER
        );
    }

    /**
     * @return mixed
     */
    public function getSchemaName()
    {
        return 'GiftWrapping';
    }


}
