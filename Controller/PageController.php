<?php

namespace SKCMS\FrontBundle\Controller;

//use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use SKCMS\FrontBundle\Controller\FrontController as Controller;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;


class PageController extends Controller
{
    protected $templateParams;
    protected $template;
    protected $page;
    protected $locale;
    protected $slug;
    protected $pageNumber;
    
    public function showPageAction($slug,$page,$_locale = null)
    {
        
        if ($_locale == null)
        {
            $_locale = $this->getRequest()->getLocale();
        }
        //        
        $this->locale = $_locale;
        $this->slug = $slug;
        $this->pageNumber = $page;
        $this->setTemplateParams();
        
        if (null === $this->page)
        {
            throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException('page doesn\'t exists');
        }
        
        if ($this->page->getRedirectRoute() !== null)
        {
            if ($this->page->getForward()=== true)
            {
                $router = $this->get('router');
                $routes = $router->getRouteCollection();
                $routeDefaults = $routes->get($this->page->getRedirectRoute())->getDefaults();
                $controller = $routeDefaults['_controller'];
                return $this->forward($controller);
            }
            else
            {
                $url = $this->generateUrl($this->page->getRedirectRoute());
                return $this->redirect($url);
            }
            
            
            
            
        }
        

        if ($this->page->getMinRoleAccess() && $this->page->getMinRoleAccess() != 'ANON')
        {
            if (false === $this->get('security.context')->isGranted($this->page->getMinRoleAccess()))
            {
                throw new AccessDeniedException();
            }
        }
        
        $this->processTemplate();
        return $this->renderPage();
    }
    
    protected function processTemplate()
    {
        if (null !== $this->page->getTemplate())
        {
            $this->templateFileName = $this->page->getTemplate()->getFile();
        }
        else
        {
            $this->templateFileName = 'page';
        }
        $this->modifyTemplate( 'SKCMSFrontBundle:pages-templates:'.$this->templateFileName.'.html.twig');
        
    }
    
    protected function setTemplateParams()
    {

        
        $slugUtils = $this->get('skcms_core.slugutils');
        $page = $slugUtils->getPageBySlug($this->slug,$this->locale);
        
        
        
        $this->templateParams['page'] = $page;
        $this->page = $this->templateParams['page'];
        
        
        $listUtils = $this->get('skcms_core.listsutils');
        $this->templateParams['lists'] = $listUtils->getPageList($this->page);
        
        $this->addTemplateParam('currentPage', $this->pageNumber);
        $modulesConfig = $this->getParameter('skcms_admin.modules');
        if ($modulesConfig['contact']['enabled']) {
            $contact = $this->get('skcms.contact.form');
            $contactForm = $contact->get();
            if ($contactForm instanceof \Symfony\Component\HttpFoundation\RedirectResponse) {
                $this->forceResponse = $contactForm;
            } else {
                $this->addTemplateParam('contactForm', $contactForm);
            }
        }

        parent::setTemplateParams();
        
    }
    
   
    
    
    
    
}
