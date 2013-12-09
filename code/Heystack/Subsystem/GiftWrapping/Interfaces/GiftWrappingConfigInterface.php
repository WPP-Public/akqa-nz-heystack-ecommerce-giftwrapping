<?php

namespace Heystack\Subsystem\GiftWrapping\Interfaces;


interface GiftWrappingConfigInterface
{
    public function getCurrencyCode();

    public function getPrice();

    public function getMessage();

}