<?php

namespace IO\OrderBundle\Enum;

/**
 * Description of ItemTypeEnum
 */
class OrderStatusEnum
{
    const STATUS_INIT = 'INIT';
    const STATUS_WAITING = 'WAITING';
    const STATUS_IN_PROGRESS = 'IN_PROGRESS';
    const STATUS_READY = 'READY';
    const STATUS_CLOSED = 'CLOSED';
    const STATUS_CANCELED = 'CANCELED';
}
