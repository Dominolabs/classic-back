<?php

return [
    'title' => 'Новости',
    'author' => 'Devseonet',
    'version' => '1.0.0',
    'sort_order' => 6,
    'menu' => [
        'label' => 'Новости',
        'icon' => 'calendar',
        'items' => [
            ['label' => 'Новости', 'icon' => 'calendar', 'url' => ['/admin/event/event']],
            ['label' => 'Категории', 'icon' => 'sitemap', 'url' => ['/admin/event/event-category']],
            ['label' => 'Таги', 'icon' => 'tags', 'url' => ['/admin/event/tag']],
        ],
    ],
];
