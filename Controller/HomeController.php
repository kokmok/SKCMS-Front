<?php

namespace SKCMS\FrontBundle\Controller;

use SKCMS\FrontBundle\Controller\FrontController as Controller;
//use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class HomeController extends Controller
{
    protected $page;
    protected $template;
    protected $templateParams ;
    
    
    public function showHomeAction($_locale)
    {
//        $this->getRequest()->getSession()->set('_locale', $_locale);
        $this->locale = $_locale;
        $this->setTemplateParams();
        $this->modifyTemplate('SKCMSFrontBundle:pages-templates:home.html.twig');
        return $this->renderPage();
    }
    
    
    
    
    protected function setCustomTemplateParams()
    {
        
    }

    
    
}
