# SKCMS-Front

This package is currently under development, more documentation and features will come soon.


# The Front bundle of SKCMS for Symfony2

##Usage
you have to override the bundle (yml config)
```
 $ php app/console generate:bundle
 $ FrontBundle

```
and in you FrontBundle.php
```
 public function getParent(){
    return 'SKCMSFrontBundle';
 }
```

Now everything is in the FrontBundle\Resources\views folder. Create a subfolder named pages-templates and a file named home.html.twig

#To be continued
