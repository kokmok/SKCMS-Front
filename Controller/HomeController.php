<?php

namespace SKCMS\FrontBundle\Controller;

use SKCMS\FrontBundle\Controller\PageController as Controller;
//use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class HomeController extends Controller
{
    protected $page;
    protected $template;
    protected $templateParams ;
    
    
    public function showHomeAction( \Symfony\Component\HttpFoundation\Request $request)
    {
        
        return $this->showHomeMultilingueAction($request->getLocale());
        
    }
    
    public function showHomeMultilingueAction($_locale )
    {
        
        $this->locale = $_locale;
        $this->slug = 'home';
        $this->setTemplateParams();
        $this->modifyTemplate('SKCMSFrontBundle:pages-templates:home.html.twig');
        return $this->renderPage();
    }
    
    
    
    
    protected function setCustomTemplateParams()
    {
        
    }

    
    
}
