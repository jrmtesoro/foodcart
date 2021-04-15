<?php

return array(
    'pdf' => array(
        'enabled' => true,
        'binary' => base_path('vendor\h4cc\wkhtmltopdf\bin\wkhtmltopdf'),
        'timeout' => false,
        'options' => array(
            'footer-center' => 'Page [page] of [toPage]',
            'footer-font-size' => 8
        ),
        'env'     => array(),
    ),
    'image' => array(
        'enabled' => true,
        'binary'  => 'vendor\h4cc\wkhtmltopdf\bin\wkhtmltoimage',
        'timeout' => false,
        'options' => array(),
        'env'     => array(),
    ),
);