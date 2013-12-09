<?php

namespace Heystack\Subsystem\GiftWrapping\Interfaces;

use Heystack\Subsystem\Ecommerce\Transaction\Interfaces\TransactionModifierInterface;

interface GiftWrappingHandlerInterface extends TransactionModifierInterface
{
    public function setActive($active);

    public function isActive();

    public function updateTotal();

    public function getCost();

}