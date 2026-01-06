<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class VpsOsVersionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $osVersions = [
            [
                'name' => 'Ubuntu 24.04 LTS',
                'slug' => 'ubuntu-24-04-lts',
                'description' => 'Latest long-term support version with 5 years of standard support',
                'display_order' => 1,
                'is_active' => true,
            ],
            [
                'name' => 'Ubuntu 22.04 LTS',
                'slug' => 'ubuntu-22-04-lts',
                'description' => 'Stable long-term support version with 5 years of standard support',
                'display_order' => 2,
                'is_active' => true,
            ],
            [
                'name' => 'Ubuntu 20.04 LTS',
                'slug' => 'ubuntu-20-04-lts',
                'description' => 'Long-term support version (EOL: April 2025)',
                'display_order' => 3,
                'is_active' => true,
            ],
            [
                'name' => 'Debian 12 Bookworm',
                'slug' => 'debian-12-bookworm',
                'description' => 'Latest stable Debian release with long-term support',
                'display_order' => 4,
                'is_active' => true,
            ],
            [
                'name' => 'Debian 11 Bullseye',
                'slug' => 'debian-11-bullseye',
                'description' => 'Stable Debian release with long-term support',
                'display_order' => 5,
                'is_active' => true,
            ],
            [
                'name' => 'AlmaLinux 9',
                'slug' => 'almalinux-9',
                'description' => 'Free, open-source RHEL-compatible OS with 10 years support',
                'display_order' => 6,
                'is_active' => true,
            ],
            [
                'name' => 'AlmaLinux 8',
                'slug' => 'almalinux-8',
                'description' => 'Stable RHEL-compatible OS with long-term support',
                'display_order' => 7,
                'is_active' => true,
            ],
            [
                'name' => 'Rocky Linux 9',
                'slug' => 'rocky-linux-9',
                'description' => 'Community-driven RHEL-compatible OS with 10 years support',
                'display_order' => 8,
                'is_active' => true,
            ],
            [
                'name' => 'Rocky Linux 8',
                'slug' => 'rocky-linux-8',
                'description' => 'Stable RHEL-compatible OS with long-term support',
                'display_order' => 9,
                'is_active' => true,
            ],
            [
                'name' => 'CentOS 8 Stream',
                'slug' => 'centos-8-stream',
                'description' => 'Rolling release CentOS with continuous updates',
                'display_order' => 10,
                'is_active' => true,
            ],
            [
                'name' => 'CentOS 7',
                'slug' => 'centos-7',
                'description' => 'Legacy CentOS with long-term support (EOL: June 2024)',
                'display_order' => 11,
                'is_active' => false,
            ],
            [
                'name' => 'Windows Server 2022',
                'slug' => 'windows-server-2022',
                'description' => 'Latest Windows Server OS with 10 years support',
                'display_order' => 12,
                'is_active' => true,
            ],
            [
                'name' => 'Windows Server 2019',
                'slug' => 'windows-server-2019',
                'description' => 'Stable Windows Server with long-term support (EOL: January 2029)',
                'display_order' => 13,
                'is_active' => true,
            ],
        ];

        foreach ($osVersions as $os) {
            DB::table('vps_os_versions')->updateOrInsert(
                ['name' => $os['name']],
                array_merge($os, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }
    }
}
