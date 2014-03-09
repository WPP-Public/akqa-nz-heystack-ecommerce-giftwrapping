<?php

namespace Heystack\GiftWrapping\Interfaces;

use Heystack\Ecommerce\Transaction\Interfaces\TransactionModifierInterface;

/**
 * Interface GiftWrappingHandlerInterface
 * @package Heystack\GiftWrapping\Interfaces
 */
interface GiftWrappingHandlerInterface extends TransactionModifierInterface
{
    /**
     *
     */
    const CONFIG_PRICE_KEY = 'config-price';
    /**
     *
     */
    const CONFIG_MESSAGE_KEY = 'config-message';

    /**
     * @param $active
     * @return mixed
     */
    public function setActive($active);

    /**
     * @return mixed
     */
    public function isActive();

    /**
     * @return void
     */
    public function updateTotal();

    /**
     * @return mixed
     */
    public function getCost();

    /**
     * @return mixed
     */
    public function getMessage();
}