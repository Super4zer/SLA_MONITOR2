<?php

namespace App\Controllers;

use App\Models\SlaMonitoringModel;

class DashboardController
{
    private SlaMonitoringModel $slaModel;

    public function __construct()
    {
        $this->slaModel = new SlaMonitoringModel();
    }

    // Ubah semua method menjadi void dan lakukan echo langsung
    public function getWaiting(): void
    {
        header('Content-Type: application/json');
        $groupId = $_GET['group_id'] ?? null;
        $data = $this->slaModel->getWaiting(180, $groupId);
        echo json_encode([
            'status' => 'success',
            'data' => $data
        ]);
    }

    public function getOverdue(): void
    {
        header('Content-Type: application/json');
        $groupId = $_GET['group_id'] ?? null;
        $data = $this->slaModel->getOverdue(180, $groupId);
        echo json_encode([
            'status' => 'success',
            'data' => $data
        ]);
    }

    public function getCompleted(): void
    {
        header('Content-Type: application/json');
        $groupId = $_GET['group_id'] ?? null;
        $data = $this->slaModel->getCompleted(180, $groupId);
        echo json_encode([
            'status' => 'success',
            'data' => $data
        ]);
    }

    public function getOverdueResolved(): void
    {
        header('Content-Type: application/json');
        $groupId = $_GET['group_id'] ?? null;
        $data = $this->slaModel->getOverdueResolved($groupId);
        echo json_encode([
            'status' => 'success',
            'data' => $data
        ]);
    }
}