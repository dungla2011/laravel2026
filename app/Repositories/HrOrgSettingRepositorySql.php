<?php

namespace App\Repositories;

use App\Models\HrOrgSetting;

class HrOrgSettingRepositorySql extends BaseRepositorySql implements HrOrgSettingRepositoryInterface
{
    public function __construct(HrOrgSetting $model)
    {
        $this->model = $model;
    }
}
