<?php

namespace SKCMS\FrontBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class FrontController extends Controller
{
    protected $page;
    protected $template;
    protected $templateParams ;
    protected $forceResponse;
    protected $locale;
    
    public function __construct()
    {
        $this->templateParams = [];
    }
        
    protected function setTemplateParams()
    {
        
        $this->templateParams['user'] = $this->getUser();
        
        $this->setCustomTemplateParams();
    }
    
    
    protected function setCustomTemplateParams()
    {
        
    }
    
    protected function setTemplate($template = null)
    {
        $this->template = $template;
        
        if ( !$this->get('templating')->exists($this->template) ) 
        {
            throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException('template '.$template.' doesn\'t exists');
        }
    }
    
    protected function modifyTemplate($template)
    {
        
        $this->setTemplate( $template);
        
//        if ( !$this->get('templating')->exists($this->template) ) 
//        {
//            throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException('template doesn\'t exists');
//        }
    }
    
    public function renderPage()
    {
        if ($this->forceResponse !== null)
        {
            return $this->forceResponse;
        }
        $event = new \SKCMS\FrontBundle\Event\PreRenderEvent($this->getRequest());
        $this->get('event_dispatcher')
            ->dispatch(\SKCMS\FrontBundle\Event\SKCMSFrontEvents::PRE_RENDER, $event);

        return $this->render($this->template,$this->templateParams);
    }
    
    protected function addTemplateParam($key,$value)
    {
        $this->templateParams[$key] = $value;
    }
    
    
    protected function getSKManager()
    {
        return $this->get('skcms.corebundle.manager.entity');
    }
}
