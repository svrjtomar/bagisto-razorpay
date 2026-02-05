<?php

return [
    [
        'key'  => 'sales.payment_methods.razorpay',
        'name' => 'Razorpay',
        'info' => 'Razorpay Payment Gateway for Bagisto. Supports Cards, UPI, Net Banking, Wallets, and EMI.<div style="margin-top: 12px; padding: 12px; background: #f8f9ff; border: 1px solid #e3f2fd; border-radius: 8px;"><p style="margin: 0; font-size: 13px; color: #424242;">✔ Secure hosted checkout<br>✔ Sandbox & Production support<br>✔ Automatic order verification</p></div>',
        'sort' => 6,

        'fields' => [
            [
                'name'          => 'title',
                'title'         => 'admin::app.configuration.index.sales.payment-methods.title',
                'type'          => 'text',
                'default_value' => 'Razorpay',
                'validation'    => 'required',
                'channel_based' => true,
                'locale_based'  => true,
            ],

            [
                'name'          => 'description',
                'title'         => 'admin::app.configuration.index.sales.payment-methods.description',
                'type'          => 'textarea',
                'default_value' => 'Pay securely using Razorpay',
                'channel_based' => true,
                'locale_based'  => true,
            ],

            [
                'name'          => 'image',
                'title'         => 'Payment Method Icon',
                'type'          => 'image',
                'channel_based' => false,
                'locale_based'  => false,
                'validation'    => 'mimes:bmp,jpeg,jpg,png,webp',
                'info'          => 'Upload an icon to display in checkout (recommended size: 100x50px)',
            ],

           

            [
                'name'          => 'key_id',
                'title'         => 'Key ID',
                'type'          => 'text',
                'validation'    => 'required',
                'channel_based' => false,
                'locale_based'  => false,
                'info'          => 'Razorpay API Key ID (from Dashboard)',
            ],

            [
                'name'          => 'key_secret',
                'title'         => 'Key Secret',
                'type'          => 'password',
                'validation'    => 'required',
                'channel_based' => false,
                'locale_based'  => false,
                'info'          => 'Razorpay API Key Secret (keep this confidential)',
            ],

            [
                'name'          => 'active',
                'title'         => 'admin::app.configuration.index.sales.payment-methods.status',
                'type'          => 'boolean',
                'default_value' => false,
                'validation'    => 'required',
                'channel_based' => false,
                'locale_based'  => true,
            ],
            
            
[
    'name'          => 'webhook_secret',
    'title'         => 'Webhook Secret',
    'type'          => 'password',
    'validation'    => 'required',
    'channel_based' => false,
    'locale_based'  => false,
    'info'          => 'Paste the Razorpay webhook secret from Dashboard → Webhooks',
],
            
            [
    'name'    => 'sort',
    'title'   => 'admin::app.configuration.index.sales.payment-methods.sort-order',
    'type'    => 'select',
    'options' => [
        [
            'title' => '0',
            'value' => 0,
        ],
        [
            'title' => '1',
            'value' => 1,
        ],
        [
            'title' => '2',
            'value' => 2,
        ],
        [
            'title' => '3',
            'value' => 3,
        ],
        [
            'title' => '4',
            'value' => 4,
        ],
        [
            'title' => '5',
            'value' => 5,
        ],
        [
            'title' => '6',
            'value' => 6,
        ],
    ],
    'default_value' => 6,
    'channel_based' => false,
    'locale_based'  => false,
],





            
        ],
    ],
];
