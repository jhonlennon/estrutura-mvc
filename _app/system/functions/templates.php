<?php

    /**
     * Carrega um arquivo de template
     * @param string $file
     * @param array $variables
     * @return string
     */
    function load_templace($file, array $variables) {
        ob_start();
        extract($variables);
        require __DIR__ . "/../templates/{$file}.phtml";
        return ob_get_clean();
    }

    /**
     * Cria uma janela modal
     * @param string $title
     * @param string $body
     * @param string $footer
     * @param string $id
     * @param string $class
     * @param string $size lg | sm
     */
    function tpl_modal($title, $body = null, $footer = null, $id = null, $class = null, $size = 'lg') {
        return load_templace('modal', [
            'title' => $title,
            'body' => $body,
            'footer' => $footer,
            'id' => $id,
            'class' => $class,
            'size' => $size,
        ]);
    }
    