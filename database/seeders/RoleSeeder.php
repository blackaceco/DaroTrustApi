<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            [
                ['title' => "Super Admin"],
                [
                    ['featureTitle' => "create_admins"],
                    ['featureTitle' => "view_admins"],
                    ['featureTitle' => "edit_admins"],
                    ['featureTitle' => "delete_admins"],
                    ['featureTitle' => "reset_admins"],

                    ['featureTitle' => "create_roles"],
                    ['featureTitle' => "view_roles"],
                    ['featureTitle' => "edit_roles"],
                    ['featureTitle' => "delete_roles"],
                    ['featureTitle' => "assign_roles"],

                    ['featureTitle' => "create_content"],
                    ['featureTitle' => "edit_content"],
                    ['featureTitle' => "delete_content"],
                    ['featureTitle' => "reset_content"],
                    ['featureTitle' => "publish_content"],
                    ['featureTitle' => "unpublish_content"],
                    ['featureTitle' => "moderate_content"],

                    ['featureTitle' => "view_analytics"],
                    ['featureTitle' => "generate_reports"],
                    ['featureTitle' => "export_data"],

                    ['featureTitle' => "configure_application"],
                    ['featureTitle' => "manage_permissions"],
                    ['featureTitle' => "manage_application_features"],

                    ['featureTitle' => "backup_and_restore"],
                    ['featureTitle' => "system_logs"],
                    ['featureTitle' => "system_health"],
                ]
            ],
            [
                ['title' => "Admin Manager"],
                [
                    ['featureTitle' => "create_admins"],
                    ['featureTitle' => "view_admins"],
                    ['featureTitle' => "edit_admins"],
                    ['featureTitle' => "delete_admins"],
                    ['featureTitle' => "reset_admins"],

                    ['featureTitle' => "create_roles"],
                    ['featureTitle' => "view_roles"],
                    ['featureTitle' => "edit_roles"],
                    ['featureTitle' => "delete_roles"],
                    ['featureTitle' => "assign_roles"],
                ]
            ],
            [
                ['title' => "Content Manager"],
                [
                    ['featureTitle' => "create_content"],
                    ['featureTitle' => "edit_content"],
                    ['featureTitle' => "delete_content"],
                    ['featureTitle' => "reset_content"],
                    ['featureTitle' => "publish_content"],
                    ['featureTitle' => "unpublish_content"],
                    ['featureTitle' => "moderate_content"],

                ]
            ],
            [
                ['title' => "Order Manager"],
                []
            ],
            [
                ['title' => "Analytics Manager"],
                [
                    ['featureTitle' => "view_analytics"],
                    ['featureTitle' => "generate_reports"],
                    ['featureTitle' => "export_data"],
                ]
            ],
            [
                ['title' => "Support Staff"],
                []
            ],
            [
                ['title' => "Moderator"],
                []
            ],
            [
                ['title' => "Finance Manager"],
                []
            ],
            [
                ['title' => "Inventory Manager"],
                []
            ],
            [
                ['title' => "Settings Administrator"],
                [
                    ['featureTitle' => "configure_application"],
                    ['featureTitle' => "manage_permissions"],
                    ['featureTitle' => "manage_application_features"],

                    ['featureTitle' => "backup_and_restore"],
                    ['featureTitle' => "system_logs"],
                    ['featureTitle' => "system_health"],
                ]
            ],
            [
                ['title' => "Reports Viewer"],
                []
            ],
            [
                ['title' => "Auditor"],
                []
            ],
        ];

        // creating
        foreach ($roles as $role)
        {
            $roleModel = Role::create($role[0]);
            foreach ($role[1] ?? [] as $permission) {
                $roleModel->permissions()->create($permission);
            }
        }
    }
}
