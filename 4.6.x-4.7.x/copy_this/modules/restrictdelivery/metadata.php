<?php
$aModule = array(
    'id'          => 'restrictdelivery',
    'title'       => 'Restriction for articles to certain delivery set',
    'description' =>  array(
        'de'=>'Versandart ist nur g&uuml;ltig wenn alle Artikel im Warenkorb damit verschickt werden k&ouml;nnen',
        'en'=>'Deliveryset is only valid if all Articles can be shipped with it',
    ),
    'version'     => '1.0',
    'url'         => 'http://zunderweb.de',
    'email'       => 'info@zunderweb.de',
    'author'      => 'Zunderweb',
    'extend'      => array(
        'oxdelivery' => 'restrictdelivery/restrictdelivery_oxdelivery',
        'oxdeliverylist' => 'restrictdelivery/restrictdelivery_oxdeliverylist',
    ),
);