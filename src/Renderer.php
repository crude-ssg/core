<?php

namespace CrudeSSG;

use Twig\Environment;
use Twig\Extension\ExtensionInterface;
use Twig\Loader\FilesystemLoader;

class Renderer
{
    private Environment $twig;

    public function __construct(string $templatesPath)
    {
        $loader = new FilesystemLoader($templatesPath);
        $this->twig = new Environment($loader, []);
    }

    public function addExtension(ExtensionInterface $extension)
    {
        $this->twig->addExtension($extension);
    }

    public function render(Page $page)
    {
        $output = $this->twig->render($page->template, [
            ...$page->getContext(),
            'params' => $page->getParams(),
        ]);
        return $output;
    }
}
