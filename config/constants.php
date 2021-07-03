<?php
return [
    'STATUS' => [
        "PENDING" => 'معلق',
        "ACTIVE" => 'فعال',
        "BLOCK" => 'بلاک',
        "REJECT" => 'رد شده',
        "APPROVED" => 'پذیرش شده',
    ],
    'GLOBAL_PERMISSIONS' => [ //permission is = permission=>label of permission
        'USERS' => [
            'create users' => 'ایجاد',
            'read users' => 'خواندن',
            'update users' => 'به روز رسانی',
            'delete users' => 'حذف',
            'user manage permission' => 'مدیریت دسترسی ها',
        ],
        'TAGS' => [
            'create tags' => 'ایجاد',
            'read tags' => 'خواندن',
            'update tags' => 'به روز رسانی',
            'delete tags' => 'حذف',
        ],
        'DOCUMENTS' => [
            'create documents' => 'ایجاد',
            'read documents' => 'خواندن',
            'update documents' => 'به روز رسانی',
            'delete documents' => 'حذف',
            'verify documents' => 'تایید',
        ],
        'REPORTS' => [
            'read reports' => 'خواندن',
        ]
    ],
    'TAG_LEVEL_PERMISSIONS' => [
        'read documents in tag ' => 'خواندن',
        'create documents in tag ' => 'ایجاد',
        'update documents in tag ' => 'به روز رسانی',
        'delete documents in tag ' => 'حذف',
        'verify documents in tag ' => 'تایید',
    ],
    'DOCUMENT_LEVEL_PERMISSIONS' => [
        'read document ' => 'خواندن',
        'update document ' => 'به روز رسانی',
        'delete document ' => 'حذف',
        'verify document ' => 'تایید',
    ]
];
