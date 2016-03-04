# BrauneDigitalMailBundle

This Symfony2-Bundle allows an easy Management of E-Mail-Teamplates with additional translations. E-Mails are listed and can be previewed in SonataAdmin.

## Installation

In order to install this Bundle you will need:
* Doctrine ORM (required) -> Entity-Persistence
* SonataEasyExtends (required)
* BrauneDigitalTranslationBaseBundle (required) -> Translations
* SonataAdmin (optional) -> Backend Management

Just run the following command to install this bundle:
```
composer require braune-digital/mail-bundle
```

And enable the Bundle in your AppKernel.php:
```php
public function registerBundles()
    {
        $bundles = array(
          ...
          new BrauneDigital\TranslationBaseBundle\BrauneDigitalTranslationBaseBundle(),
          new BrauneDigital\MailBundle\BrauneDigitalMailBundle(),
          ...
        );
```

In order to use the bundle you have to
## Extend the Bundle
Just run:
```
php app/console sonata:easy-extends:generate --dest=src BrauneDigitalMailBundle
```

And enable the extended Bundle in your AppKernel.php as well:
```php
public function registerBundles()
    {
        $bundles = array(
          ...
          new Application\BrauneDigital\MailBundle\BrauneDigitalMailBundle()
          ...
        );
```

You only need to set the `user_class` option:
## Configuration
```yml
braune_digital_mail:
    user_class: Application\Sonata\UserBundle\Entity\User # Path to you used User-Entity
    base_template_path: "emails" #used for template suggestions in SonataAdmin, defaults to "emails", which would resolve to app/Resources/views/emails
    #base_template_path: ["emails_password_reset", "emails_registration] #Can be an array of paths as well
    #base_template_path: ~ #Do not use template suggestions (You would have to enter the path manually)
```
