<?php

namespace SKCMS\FrontBundle\Controller;

//use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use SKCMS\FrontBundle\Controller\FrontController as Controller;

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
       
        
//        $entitiesParams = $this->container->getParameter('skcms_admin.entities');
//        $entityParams = $entitiesParams['Page'];
        
        $slugUtils = $this->get('skcms_core.slugutils');
        $page = $slugUtils->getPageBySlug($this->slug,$this->locale);
//        $em = $this->getSKManager();
//
//        $repo = $this->getDoctrine()->getManager()->getRepository('SKCMS\CoreBundle\Entity\Translation\EntityTranslation');
//        
//        
////        $page = $repo->findAllTranslations('slug',$slug,$entityParams['class']);
//        $translationEntity = $repo->findObjectByTranslation('slug',$this->slug,$entityParams['class'],$this->locale);
//        if (null == $translationEntity)
//        {
//            throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException('page doesn\'t exists');
//        }
//        
//        $repo = $em->getRepository($entityParams['bundle'].'Bundle:Page');
//        $this->templateParams['page'] = $repo->findFull($translationEntity->getForeignKey(),$this->locale);
        $this->templateParams['page'] = $page;
        $this->page = $this->templateParams['page'];
        
        
        $listUtils = $this->get('skcms_core.listsutils');
        $this->templateParams['lists'] = $listUtils->getPageList($this->page);
        
        $this->addTemplateParam('currentPage', $this->pageNumber);
        
//        $this->setCustomTemplateParams();
        parent::setTemplateParams();
        
    }
    
   
    
    
    
    
}
