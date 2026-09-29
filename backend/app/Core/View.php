<?php

declare(strict_types=1);

namespace App\Core;

use Smarty\Smarty;

class View
{
    private Smarty $smarty;

    /**
     * @param array{
     *     templates: string,
     *     compile: string,
     *     cache: string
     * } $config
     */
    public function __construct(array $config)
    {
        $this->smarty = new Smarty();

        $this->smarty->setTemplateDir($config['templates']);
        $this->smarty->setCompileDir($config['compile']);
        $this->smarty->setCacheDir($config['cache']);
    }

    /**
     * @param array<string, mixed> $data
     */
    public function render(string $template, array $data = []): void
    {
        foreach ($data as $key => $value) {
            $this->smarty->assign($key, $value);
        }

        $this->smarty->display($template);
    }
}
