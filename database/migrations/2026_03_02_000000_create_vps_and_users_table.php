<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Creates pivot table for many-to-many relationship between users and vps instances
     * Allows: 1 VPS -> many users (owner, admins, managers)
     *         1 User -> many VPS
     */
    public function up(): void
    {
        // Create table matching exact parent table types:
        // - user_id_vendor: bigint(20) unsigned (from users.id)
        // - instance_id: bigint(11) signed (from vps_instances.id which is NOT unsigned!)
        DB::statement("
            CREATE TABLE vps_and_users (
                id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                user_id_vendor BIGINT UNSIGNED NOT NULL COMMENT 'FK to users.id (bigint(20) unsigned)',
                instance_id BIGINT NOT NULL COMMENT 'FK to vps_instances.id (bigint(11) signed - NOT unsigned)',
                role VARCHAR(255) NOT NULL DEFAULT 'owner' COMMENT 'Role: owner, admin, manager, viewer',
                created_at TIMESTAMP NULL,
                updated_at TIMESTAMP NULL,
                deleted_at TIMESTAMP NULL,

                UNIQUE KEY unique_user_instance (user_id_vendor, instance_id),
                INDEX idx_user_id_vendor (user_id_vendor),
                INDEX idx_instance_id (instance_id),

                CONSTRAINT vps_and_users_user_id_vendor_foreign
                    FOREIGN KEY (user_id_vendor) REFERENCES users(id) ON DELETE CASCADE,
                CONSTRAINT vps_and_users_instance_id_foreign
                    FOREIGN KEY (instance_id) REFERENCES vps_instances(id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vps_and_users');
    }
};
