<?php

namespace BrauneDigital\MailBundle\Service;

use Doctrine\DBAL\Exception\InvalidArgumentException;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpKernel\Kernel;

class TemplateSearcher implements TemplateSearcherInterface     {

    protected $kernel;

    protected $filter;

    protected $baseDir;

    public function __construct(Kernel $kernel, $baseDir = 'Resources/views',  $filter = null) {
        $this->kernel = $kernel;
        $this->baseDir = $baseDir;
        $this->filter = $filter;
    }

    /**
     * @param array $paths
     *
     * @return array
     */
    public function getTemplates(array $paths) {

        $templates = array();

        if(count($paths) == 0) {
            return $templates;
        }

        foreach($paths as $path) {
            $finder = new Finder();

            $dir = $this->kernel->getRootDir() . '/' . $this->baseDir . '/' . $path;
            if(is_dir($dir)) {

                $finder->ignoreUnreadableDirs();
                $finder->followLinks();
                $finder->files();

                if($this->filter) {
                    $finder->name($this->filter);
                }

                $finder->files()->in($dir);
                foreach ($finder as $file) {
                    $templates[] = $path . '/' .$file->getRelativePathname();
                }
            } else {
                throw new \InvalidArgumentException("The directory ". $dir . " does not exist, please check your config (braune_digital_mail.base_template_path)");
            }
        }

        return  array_unique($templates);
    }
}