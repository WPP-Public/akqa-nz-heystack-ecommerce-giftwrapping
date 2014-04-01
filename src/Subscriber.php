<?php

namespace Heystack\GiftWrapping;

use Heystack\Core\State\State;
use Heystack\Core\Storage\Backends\SilverStripeOrm\Backend;
use Heystack\Core\Traits\HasEventServiceTrait;
use Heystack\Core\Traits\HasStateServiceTrait;
use Heystack\Ecommerce\Currency\Events as CurrencyEvents;
use Heystack\Ecommerce\Transaction\Events as TransactionEvents;
use Heystack\GiftWrapping\Interfaces\GiftWrappingHandlerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Handles both subscribing to events and acting on those events needed for TaxHandler to work properly
 *
 * @copyright  Heyday
 * @author Glenn Bautista <glenn@heyday.co.nz>
 * @package Ecommerce-GiftWrapping
 * @see Symfony\Component\EventDispatcher
 */
class Subscriber implements EventSubscriberInterface
{
    use HasStateServiceTrait;
    use HasEventServiceTrait;

    /**
     * Holds the GiftWrapping Handler
     * @var \Heystack\GiftWrapping\Interfaces\GiftWrappingHandlerInterface
     */
    protected $giftWrappingHandler;
    
    protected $currencyChanging;

    /**
     * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface $eventService
     * @param \Heystack\GiftWrapping\Interfaces\GiftWrappingHandlerInterface $giftWrappingHandler
     * @param \Heystack\Core\State\State $stateService
     */
    public function __construct(
        EventDispatcherInterface $eventService,
        GiftWrappingHandlerInterface $giftWrappingHandler,
        State $stateService
    )
    {
        $this->eventService = $eventService;
        $this->giftWrappingHandler = $giftWrappingHandler;
        $this->stateService = $stateService;
    }

    /**
     * Returns an array of events to subscribe to and the methods to call when those events are fired
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            Events::TOTAL_UPDATED                                            => ['onTotalUpdated', 0],
            CurrencyEvents::CHANGED                                          => ['onCurrencyChanged', 0],
            sprintf('%s.%s', Backend::IDENTIFIER, TransactionEvents::STORED) => ['onTransactionStored', 0]
        ];
    }

    /**
     * Called to update the GiftWrappingHandler's total
     */
    public function onCurrencyChanged()
    {
        $this->currencyChanging = true;
        $this->giftWrappingHandler->updateTotal();
        $this->currencyChanging = false;
    }

    /**
     * Called after the GiftWrappingHandler's total is updated.
     * Tells the transaction to update its total.
     */
    public function onTotalUpdated()
    {
        if (!$this->currencyChanging) {
            $this->eventService->dispatch(TransactionEvents::UPDATE);
        }
    }

    /**
     * Remove the required state
     */
    public function onTransactionStored()
    {
        $this->stateService->removeByKey(GiftWrappingHandler::IDENTIFIER);
    }
}
