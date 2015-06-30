<?php
namespace SKCMS\FrontBundle\Event;
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of PreRenderEvent
 *
 * @author Jona
 */
class PreRenderEvent extends \Symfony\Component\EventDispatcher\Event
{
    
    private $request;
    
    public function __construct(\Symfony\Component\HttpFoundation\Request $request)
    {
        $this->request = $request;
    }
    
    
    public function getRequest()
    {
        return $this->request;
    }
}
