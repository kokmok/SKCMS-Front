<?php

namespace SKCMS\FrontBundle\Controller;

//use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use SKCMS\FrontBundle\Controller\FrontController as Controller;

class EntityController extends Controller
{
    protected $templateParams;
    protected $template;
    protected $entity;
    protected $format;
    protected $locale;
    protected $slug;
    
    
    public function showEntityAction($slug,$_format,$_locale = null)
    {
        if (null === $_locale)
        {
            $_locale = $this->getRequest()->getLocale();
        }
//        die($this->getRequest()->getLocale());
//        
//        $this->getRequest()->getSession()->set('_locale', $_locale);
        $this->format = $_format;
        $this->locale = $_locale;
        $this->slug = $slug;
        $this->setTemplateParams();
//        $this->setTemplate();
        $this->dynamicTemplate();
        return $this->renderPage();
    }
    
    
    
    protected function setTemplateParams()
    {
       
        
        $entitiesParams = $this->container->getParameter('skcms_admin.entities');
//        $entityParams = $entitiesParams['Page'];
        
        $em = $this->getSKManager();

        $repo = $this->getDoctrine()->getManager()->getRepository('SKCMS\CoreBundle\Entity\Translation\EntityTranslation');
        
        
//        $page = $repo->findAllTranslations('slug',$slug,$entityParams['class']);
        $translationEntity = $repo->findObjectBySlug($this->slug,$this->locale);
        if (null == $translationEntity)
        {
            throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException('entity doesn\'t exists');
        }
        
        $entityClass = $translationEntity->getObjectClass();
        
        $repo = $em->getRepository('\\'.$entityClass);
        $this->entity = $repo->find($translationEntity->getForeignKey(),$this->locale);

        $this->templateParams['entity'] = $this->entity;

        if ($this->entity instanceof \SKCMS\ShopBundle\Entity\SKBaseProduct)
        {
            $productUtils = $this->get('skcms_shop.productutils');
            $productForm = $productUtils->getProductForm($this->entity);
            $this->addTemplateParam('shopForm', $productForm);

        }
        
        parent::setTemplateParams();
        
    }
    
    protected function dynamicTemplate()
    {
        
        $className = substr(get_class($this->entity),strrpos(get_class($this->entity),'\\')+1);
//        die('SKCMSFrontBundle:entity-templates:'.strtolower($className).'.'.$this->format.'.twig');
        $this->modifyTemplate( 'SKCMSFrontBundle:entity-templates:'.strtolower($className).'.'.$this->format.'.twig');
    }
    
   
    
    
    
    
}
