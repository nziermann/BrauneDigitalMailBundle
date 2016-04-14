# BrauneDigitalMailBundle

This Symfony2-Bundle allows an easy Management of E-Mail-Templates with additional translations. E-Mails are listed and can be previewed in SonataAdmin.

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

## Mail-Templates

*emails/confirm.html.twig*:
```twig
{% extends 'emails/layout.html.twig' %}

{% block body %}
	{{ object.template.body|raw }}
	---USER_NAME{{ object.object.username }}---
	---CONFIRMATION_LINK{% if object.object.confirmationToken is not empty %}{{ url('fos_user_registration_password_confirm', {token: object.object.confirmationToken}) }}{% else %}{{ url('fos_user_registration_password_confirm', {token: 'na'}) }}{% endif %}---
{% endblock %}
```

Where `---USER_NAME{{ object.object.username }}---`would be a generated placeholder with id *USER_NAME*.
An `txt.twig` file is addionally used to append the Content as plain text as well:  
  
*emails/confirm.txt.twig*:
````
{{ object.template.body|raw|striptags }}
---USERNAME{{ object.object.username }}---
---CONFIRMATION_LINK{% if object.object.confirmationToken is not empty %}{{ url('fos_user_registration_password_confirm', {token: object.object.confirmationToken}) }}{% else %}{{ url('fos_user_registration_password_confirm', {token: 'na'}) }}{% endif %}---
```

Placeholders can then be used in the Template-Description (layout path has to be the same):
```
Dear ###USER_NAME###

Thank you for registering. In order to complete your registration, you need to confirm your email address. To do so, click on the following link:

###CONFIRMATION_LINK###

Best regards
```
##Types of Mails
There are currenty two types of Mails:
* Standard Mail
* User Mail (used for mails regarding a single or two users)

## Send Mails
In order to send mails one has to get the template by entering the layout path and creating a new mail:
```php
$layout = 'emails/confirm.html.twig';
$mailService = $this->get('braunedigital.mail.service.mail');
$template = $this->getDoctrine()->getRepository('BrauneDigital\MailBundle\Entity\MailTemplate')->findOneBy(array(
    'layout' => $layout
));

if ($template) {
    $mail = new UserMail();
    $mail->setStatus(UserMail::STATUS_WAITING_FOR_SENDING);
    $mail->setTemplate($template);
    $mail->setObject($user);
    $mail->setObject2(null);
    $em->persist($mail);
    $em->flush();
    $mailService->handle($mail);
}
```
The template will now be rendered and the user is available as `object` in the template.
The locale and recipient adress are being loaded from the first user (`object`).
Or for user independent mails:

```php
$layout = 'emails/static_mail.html.twig';
$mailService = $this->get('braunedigital.mail.service.mail');
$template = $this->getDoctrine()->getRepository('BrauneDigital\MailBundle\Entity\MailTemplate')->findOneBy(array(
    'layout' => $layout
));

if ($template) {
    $mail = new Mail();
    $mail->setStatus(UserMail::STATUS_WAITING_FOR_SENDING);
    $mail->setTemplate($template);
    $mail->setRecipient($email);
    $mail->setLocale('en');
    $em->persist($mail);
    $em->flush();
    $mailService->handle($mail);
}
```
