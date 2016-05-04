<?php

namespace SKCMS\FrontBundle\Service;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
/**
 * Description of SKCMSVarsInjector
 *
 * @author Jona
 */
class SKCMSVarsInjector 
{
    protected $twig;
    /**
     * @var ContainerInterface
     */
    protected $container;
    protected $locale;
    protected $skcmsTwigVars;
    protected $multilingue;
    protected $skcmsModules;

    public function __construct(\Twig_Environment $twig, $container)
    {
        
        $this->twig = $twig;
        $this->container = $container;
        $this->skcmsTwigVars = [];
        $this->skcmsModules = $container->getParameter('skcms_admin.modules');
    }

    public function onKernelRequest(\SKCMS\FrontBundle\Event\PreRenderEvent $event)
    {
     
        $this->multilingue = count($this->container->getParameter('skcms_admin.siteInfo'))['locales']>1;
        
        $this->addMenus();
        $this->addSiteInfo();
        $this->addContactInfo();
        $this->addCart();
        $this->addBlog();
        
        $this->twig->addGlobal('skcmsVars', null);
        $this->twig->addGlobal('skcmsVars', $this->skcmsTwigVars);
    }
    
    public function addContactInfo()
    {
        $contactUtils = $this->container->get('skcms_core.contactinfos');
        $contactInfos = $contactUtils->get(null,$this->locale);
        $this->skcmsTwigVars['contactInfos'] = $contactInfos;
        
    }
    
    
    public function addSiteInfo()
    {
        $siteInfo = $this->container->getParameter('skcms_admin.siteinfo');
        $this->skcmsTwigVars['siteinfo'] = $siteInfo;
    }
    
    public function addMenus()
    {
        
        $twigMenus = [];
        $menuService = $this->container->get('skcms_core.menuutils');
        $menus = $menuService->getRootMenusFull($this->locale,$this->multilingue);
        
        foreach ($menus as $menu)
        {
            $twigMenus[$menu->getTextId()] = $menu;
        }
      
        $this->skcmsTwigVars['menus'] = $twigMenus;
        
    }
    
    public function addCart()
    {
        
        if (class_exists('\SKCMS\ShopBundle\Entity\Cart'))
        {
            $cartUtils = $this->container->get('skcms_shop.cartutils');
            $cart = $cartUtils->getCurrentCart(false);
            $cart = null === $cart ? new \SKCMS\ShopBundle\Entity\Cart() : $cart;
            $this->skcmsTwigVars['cart'] = $cart;
        }
        
    }
    public function addBlog()
    {

        if ($this->skcmsModules['blog']['enabled'])
        {
            $tags = $this->container->get('doctrine')->getManager()->getRepository('SKCMSBlogBundle:BlogTag')->findAll();
            $this->skcmsTwigVars['blog']['tags'] = $tags;

            $lastestPosts = $this->container->get('doctrine')->getManager()->getRepository('SKCMSBlogBundle:BlogPost')->findBy(
                [],
                ['id'=>'DESC'],
                5
            );
            $this->skcmsTwigVars['blog']['lastestPosts'] = $lastestPosts;

        }

    }
}
