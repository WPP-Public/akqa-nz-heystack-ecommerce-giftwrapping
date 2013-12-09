<?php

namespace Heystack\Subsystem\GiftWrapping\Interfaces;

use Heystack\Subsystem\Ecommerce\Transaction\Interfaces\TransactionModifierInterface;

interface GiftWrappingHandlerInterface extends TransactionModifierInterface
{
    const CONFIG_PRICE_KEY = 'config-price';
    const CONFIG_MESSAGE_KEY = 'config-message';

    public function setActive($active);

    public function isActive();

    public function updateTotal();

    public function getCost();

    public function getMessage();

}