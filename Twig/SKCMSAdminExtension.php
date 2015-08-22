<?php

namespace SKCMS\FrontBundle\Twig;

/**
 * Description of SKCMSAdminExtension
 *
 * @author Jona
 */

class SKCMSAdminExtension extends \Twig_Extension
{
    private $container;
    private $locale;
    
    public function __construct($container)
    {
        $this->container = $container;
        if ($container->isScopeActive('request') && null !== $this->container->get('request')->get('_route'))
        {
            $this->locale = $this->container->get('request')->getLocale();

        }
        

    }
    public function getName()
    {
        return 'skcms_extension';
    }
    
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('skListEntities', [$this,'skListEntities']),
            new \Twig_SimpleFilter('getClass', [$this,'getClass']),
            new \Twig_SimpleFilter('beautifulDate', [$this,'beautifulDate']),
            new \Twig_SimpleFilter('skEntityPath', [$this,'skEntityPath']),
            new \Twig_SimpleFilter('translation', [$this,'translation']),
            new \Twig_SimpleFilter('shuffle', [$this,'shuffle']),
            new \Twig_SimpleFilter('validPromotion', [$this,'validPromotion'])
        );
    }
    
    
    

    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('switchLanguageLink',[$this,'switchLanguageLink']),
            new \Twig_SimpleFunction('isHomePage',[$this,'isHomePage'])
            
        );
    }
    
    static public function validPromotion(\SKCMS\ShopBundle\Entity\Promotion $promotion= null)
    {
        if (null===$promotion)
        {
            return false;
        }
        $now = new \DateTime();
        $nowts = $now->getTimestamp();
        
        if ($promotion->isActive() && $promotion->getDateStart()->getTimestamp() < $nowts && $promotion->getDateEnd()->getTimestamp()> $nowts)
        {
            return true;
        }
        return false;
    }
    
    public function shuffle($input)
    {
        if (is_array($input))
        {
            shuffle($input);
        }
        return $input;
    }
    public function isHomePage()
    {
        $isHome =  $this->container->get('request')->get('route') == 'skcms_front_home' || $this->container->get('request')->get('route') == 'skcms_front_home' ? true : false;
        return $isHome;
    }
    public function switchLanguageLink($translatedLocale)
    {
        $router = $this->container->get('router');
        $em =  $this->container->get('doctrine')->getManager();
        
        
        $route = $this->container->get('request')->get('_route');
        $this->locale = $this->container->get('request')->getLocale();
        
        $routeParams = $this->container->get('request')->get('_route_params');
        
        $routeParams['_locale']=$translatedLocale;
        
        
//        return $this->locale;
        
        
        if (isset($routeParams['slug']))
        {
            $repo = $em->getRepository('SKCMS\CoreBundle\Entity\Translation\EntityTranslation');
            $translationEntity = $repo->findObjectBySlug($routeParams['slug'],$this->locale);
           
            
            $translatedTranslation = $repo->findOneBy([
                                            'foreignKey'=>$translationEntity->getForeignKey(),
                                            'locale'=>$translatedLocale,
                                            'objectClass'=>$translationEntity->getObjectClass(),
                                            'field'=>'slug'
                ]);
            
            if (null !== $translatedTranslation)
            {
                $routeParams['slug'] = $translatedTranslation->getContent();
            }
        }
        
        return $router->generate($route,$routeParams);
    }
    
    
    
    public function translation($text,$translatedLocale)
    {
//        $transRepo = $this->container->get('doctrine')->getManager()->getRepository('SKCMS\CoreBundle\Entity\Translation\EntityTranslation');
//        
//        $field = $repo->findTranslation();
    }
    
    public function skEntityPath($entity,$format = 'html',$mulilingue = true,$absolute = false)
    {
//        if ($entity === null)
//        {
//            return null;
//        }
        $this->locale = $this->container->get('request')->getLocale();
        $router = $this->container->get('router');
        
        return $router->generate(
                $mulilingue ? 'skcms_front_entity_multilingue' : 'skcms_front_entity',
                $mulilingue ? ['slug'=>$entity->getSlug(),'_locale'=>$this->locale,'_format'=>$format] : ['slug'=>$entity->getSlug(),'_format'=>$format],
                $absolute
                );
        
    }
    
    public function skListEntities($page)
    {
        $listUtils = $this->container->get('skcms_core.listsutils');
        $list = $listUtils->getPageList($page);
        return $list;
    }
    
    public function getClass($entity,$absolute = false)
    {
        if ($absolute)
        {
            return get_class($entity);
        }
        else
        {
            return substr(get_class($entity),  strrpos(get_class($entity), '\\' ) +1 );
        }
        
    }
    public function beautifulDate(\DateTime $date,$locale)
    {
                
        $months = 
            [
                'fr'=>['janvier','février','mars','avril','mai','juin','juillet','août','septembre','octobre','novembre','décembre'],
                'en'=>['January','February','March','April','May','June','July','Augustus','September','October','November','December']
            ];
        
        $month = $months[$locale][$date->format('n')-1];
        
        switch ($locale)
        {
            case 'fr':
                return $date->format('j').' '.$month.' '.$date->format('Y');
                break;
            case 'en':
                return $month. ' '.$date->format('j').$date->format('S').' '.$date->format('Y');
                break; 
        }
        
    }
}
