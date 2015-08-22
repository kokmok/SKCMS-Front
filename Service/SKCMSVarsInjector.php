<?php

namespace SKCMS\FrontBundle\Service;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;
/**
 * Description of SKCMSVarsInjector
 *
 * @author Jona
 */
class SKCMSVarsInjector 
{
    protected $twig;
    protected $container;
    protected $locale;
    protected $skcmsTwigVars;
    protected $multilingue;

    public function __construct(\Twig_Environment $twig, $container)
    {
        
        $this->twig = $twig;
        $this->container = $container;
        $this->skcmsTwigVars = [];
    }

    public function onKernelRequest(\SKCMS\FrontBundle\Event\PreRenderEvent $event)
    {
     
        $this->multilingue = count($this->container->getParameter('skcms_admin.siteInfo'))['locales']>1;
        
        $this->addMenus();
        $this->addSiteInfo();
        $this->addContactInfo();
        $this->addCart();
        
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
}
