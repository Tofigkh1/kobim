<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Table Columns
    |--------------------------------------------------------------------------
    */

    'column.name' => 'Ad',
    'column.guard_name' => 'Mühafizə adı',
    'column.roles' => 'Rollar',
    'column.permissions' => 'İcazələr',
    'column.updated_at' => 'Yenilənib',

    /*
    |--------------------------------------------------------------------------
    | Form Fields
    |--------------------------------------------------------------------------
    */

    'field.name' => 'Ad',
    'field.guard_name' => 'Mühafizə adı ',
    'field.permissions' => 'İcazələr',
    'field.select_all.name' => 'Hamısını seç',
    'field.select_all.message' => 'Bu rol üçün bütün icazələri aktivləşdirin <span class="text-primary font-medium">Aktivdir</span>',

    /*
    |--------------------------------------------------------------------------
    | Navigation & Resource
    |--------------------------------------------------------------------------
    */

    'nav.group' => 'Mühafizə',
    'nav.role.label' => 'Rollar',
    'nav.role.icon' => 'heroicon-o-shield-check',
    'resource.label.role' => 'Rol',
    'resource.label.roles' => 'Rollar',

    /*
    |--------------------------------------------------------------------------
    | Section & Tabs
    |--------------------------------------------------------------------------
    */

    'section' => 'Müəssisələr',
    'resources' => 'Resurslar',
    'widgets' => 'Vidjetlər',
    'pages' => 'Səhifələr',
    'custom' => 'Fərdi İcazələr',

    /*
    |--------------------------------------------------------------------------
    | Messages
    |--------------------------------------------------------------------------
    */

    'forbidden' => 'Giriş icazəniz yoxdur',

    /*
    |--------------------------------------------------------------------------
    | Resource Permissions' Labels
    |--------------------------------------------------------------------------
    */

    'resource_permission_prefixes_labels' => [
        'view' => 'Baxış',
        'view_any' => 'İstənilənə baxış',
        'create' => 'Yaratmaq',
        'update' => 'Redakte etmək',
        'delete' => 'Silmək',
        'delete_any' => 'İstəniləni silmək',
        'force_delete' => 'Məcburi silmək',
        'force_delete_any' => 'İstəniləni məcburi silmək',
        'restore' => 'Bərpa etmək',
        'reorder' => 'Yenidən sıralama',
        'restore_any' => 'Hər hansı birini bərpa edin',
        'replicate' => 'Kopyalama',
    ],
];
