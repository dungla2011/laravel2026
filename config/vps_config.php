<?php

return [
    'specs' => [
        'n_cpu_core' => [
            'desc' => "Core CPU",
            'max' => 64,
            'min' => 1,
            'free' => 0,
            'step' => 1,
            'price' => 50,  // K (thousands)
        ],
        'n_ram_gb' => [
            'desc' => "Memory <br><span style='font-size: 80%'>(GB)</span>",
            'max' => 512,
            'min' => 1,
            'free' => 0,
            'step' => 1,
            'price' => 30,  // K (thousands)
        ],
        'n_gb_disk' => [
            'desc' => "SSD (GB)",
            'max' => 2048,
            'min' => 20,
            'free' => 0,
            'step' => 10,
            'rounding' => 10,  // Làm tròn theo 10 GB
            'price' => 1,  // K (thousands)
        ],
        'n_ip_address' => [
            'desc' => "IP Address",
            'max' => 100,
            'min' => 1,
            'free' => 1,
            'step' => 1,
            'price' => 50,  // K (thousands)
        ],
        'n_network_mbit' => [
            'desc' => "Network <br><span style='font-size: 80%'>(Mbit share)</span>",
            'max' => 10000,
            'min' => 200,
            'free' => 0,
            'step' => 100,
            'disable_change' => true
        ],
        'n_network_dedicated_mbit' => [
            'desc' => "Network <br><span style='font-size: 80%'>(dedicated)</span>",
            'max' => 10000,
            'min' => 0,
            'free' => 0,
            'step' => 100,
            'rounding' => 100,  // Làm tròn theo 100 Mbps
            'price' => 1000,  // K (thousands) per 100 Mbps
        ]
    ],

    // Default specs (khi không truyền parameter)
    'defaults' => [
        'n_cpu_core' => 1,
        'n_ram_gb' => 1,
        'n_gb_disk' => 20,
        'n_network_mbit' => 200,
        'n_network_dedicated_mbit' => 0,
        'n_ip_address' => 1,
    ],
];
