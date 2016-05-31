<?php
/**
 * Created by jona on 27/05/16
 */

namespace SKCMS\FrontBundle\Event;


use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;

class ExceptionListener
{
    private $eventDispatcher;
    public function setEventDispatcher(EventDispatcherInterface $eventDispatcher){
        $this->eventDispatcher = $eventDispatcher;
    }

    public function onException(GetResponseForExceptionEvent $event){

        $allowedCodes = [404,403];

        if (in_array($event->getException()->getCode(),$allowedCodes)){
            $event = new \SKCMS\FrontBundle\Event\PreRenderEvent($event->getRequest());
            $this->eventDispatcher
                ->dispatch(\SKCMS\FrontBundle\Event\SKCMSFrontEvents::PRE_RENDER, $event);

        }



    }

}