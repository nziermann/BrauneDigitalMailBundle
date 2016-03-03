<?php

namespace BrauneDigital\MailBundle\Service;

interface TemplateSearcherInterface {

    /**
     * @param array $paths
     *
     * @return mixed
     */
    public function getTemplates(array $paths);
}