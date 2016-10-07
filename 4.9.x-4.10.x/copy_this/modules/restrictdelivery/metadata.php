<?php
$aModule = array(
    'id'          => 'restrictdelivery',
    'title'       => 'Zunderweb Check Valid Deliveries',
    'description' =>  array(
        'de'=>'Eine Versandart ist nur w&auml;hlbar wenn alle Artikel im Warenkorb damit verschickt werden k&ouml;nnen',
        'en'=>'A delivery is only selectable if all articles can be shipped with it',
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