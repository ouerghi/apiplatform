<?php
/**
 * Created by PhpStorm.
 * User: Mobelite
 * Date: 08/03/2019
 * Time: 17:00
 */

namespace App\EventSubscriber;


use ApiPlatform\Core\EventListener\EventPriorities;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class PasswordHashSubscriber implements EventSubscriberInterface
{

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::VIEW => ['hasPassword' => EventPriorities::PRE_WRITE]
        ];
    }
    public function hasPassword(GetResponseForControllerResultEvent $event)
    {

    }
}