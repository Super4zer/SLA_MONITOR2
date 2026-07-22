<?php

namespace App\Controllers;

use App\Models\SlaMonitoringModel;
use App\Models\StaffWhitelistModel;
use App\Models\GroupWhitelistModel;
use App\Models\Enums\SlaStatus;

class WebhookController
{
    private SlaMonitoringModel $slaModel;
    private StaffWhitelistModel $staffModel;
    private GroupWhitelistModel $groupModel;

    public function __construct()
    {
        $this->slaModel = new SlaMonitoringModel();
        $this->staffModel = new StaffWhitelistModel();
        $this->groupModel = new GroupWhitelistModel();
    }

    public function handle(): array
    {
        // 1. Ambil raw input
        $rawPayload = file_get_contents('php://input');

        // Log untuk debugging
        $logDir = __DIR__ . '/../../logs';
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }

        $data = json_decode($rawPayload, true);
        if (!$data) {
            $data = $_POST;
        }

        if (empty($data)) {
            file_put_contents($logDir . '/webhook.log', "[" . date('Y-m-d H:i:s') . "] Empty payload" . PHP_EOL, FILE_APPEND);
            http_response_code(400);
            return ['status' => 'error', 'message' => 'Empty payload'];
        }

        // 2. Filter status perangkat (ping event)
        if (isset($data['status']) && isset($data['deviceId']) && !isset($data['message']) && !isset($data['msg'])) {
            return ['status' => 'ignored', 'message' => 'Device status event, not a chat message'];
        }

        // 3. Normalisasi Data (Mendukung Berbagai Format Wablas Webhook)
        
        // A. Cek Group ID
        $groupIdRaw = null;
        if (isset($data['group']) && is_array($data['group'])) {
            $groupIdRaw = $data['group']['group_id'] ?? $data['group']['id'] ?? null;
        } elseif (isset($data['group']) && is_string($data['group'])) {
            $groupIdRaw = $data['group'];
        }
        if (!$groupIdRaw) {
            $groupIdRaw = $data['group_id'] ?? $data['groupId'] ?? $data['id_group'] ?? ($data['data']['group_id'] ?? null);
        }

        // B. Cek Sender Phone (Nomor Pengirim)
        $senderPhoneRaw = null;
        if (isset($data['group']) && is_array($data['group']) && !empty($data['group']['sender'])) {
            $senderPhoneRaw = $data['group']['sender'];
        }
        if (!$senderPhoneRaw) {
            $senderPhoneRaw = $data['sender'] ?? $data['phone'] ?? $data['from'] ?? ($data['data']['sender'] ?? null);
        }

        // C. Cek Message Content (Isi Pesan)
        $messageContent = '';
        if (isset($data['message'])) {
            if (is_array($data['message'])) {
                $messageContent = $data['message']['text'] ?? $data['message']['caption'] ?? json_encode($data['message']);
            } else {
                $messageContent = (string) $data['message'];
            }
        } elseif (isset($data['text'])) {
            $messageContent = (string) $data['text'];
        } elseif (isset($data['msg'])) {
            $messageContent = (string) $data['msg'];
        }

        // D. Cek Flag Group
        $isGroup = false;
        if (isset($data['isGroup'])) {
            $isGroup = filter_var($data['isGroup'], FILTER_VALIDATE_BOOLEAN) || $data['isGroup'] === 'true' || $data['isGroup'] === 1 || $data['isGroup'] === '1';
        }
        // Jika groupId ada, otomatis ini pesan grup
        if ($groupIdRaw) {
            $isGroup = true;
        }

        // E. Clean / Normalize Group ID (Hapus suffix @g.us jika ada)
        $groupId = null;
        if ($groupIdRaw) {
            $groupId = trim(explode('@', (string) $groupIdRaw)[0]);
        }

        // F. Clean / Normalize Sender Phone (Nomor HP Pengirim)
        $senderPhone = null;
        if ($senderPhoneRaw) {
            $senderPhone = trim(explode('@', (string) $senderPhoneRaw)[0]);
            $senderPhone = preg_replace('/[^0-9]/', '', $senderPhone);
        }

        // Log detail parsing ke file log
        $logMessage = sprintf(
            "[%s] PAYLOAD: %s | PARSED -> isGroup: %s, groupId: %s, sender: %s, msg: %s",
            date('Y-m-d H:i:s'),
            $rawPayload,
            $isGroup ? 'true' : 'false',
            $groupId ?? 'null',
            $senderPhone ?? 'null',
            $messageContent
        );
        file_put_contents($logDir . '/webhook.log', $logMessage . PHP_EOL, FILE_APPEND);

        // 4. Validasi Dasar
        if (!$isGroup || !$groupId || !$senderPhone) {
            return [
                'status' => 'ignored',
                'message' => 'Not a group message or missing required fields. isGroup=' . ($isGroup ? 'true' : 'false') . ', groupId=' . ($groupId ?? 'null') . ', sender=' . ($senderPhone ?? 'null')
            ];
        }

        // 5. Cek Whitelist Grup
        if (!$this->groupModel->isWhitelistedGroup($groupId)) {
            file_put_contents($logDir . '/webhook.log', sprintf("[%s] IGNORED: Group %s not whitelisted", date('Y-m-d H:i:s'), $groupId) . PHP_EOL, FILE_APPEND);
            return ['status' => 'ignored', 'message' => 'Group not whitelisted: ' . $groupId];
        }

        $timeNow = date('Y-m-d H:i:s');
        $isStaff = $this->staffModel->isStaff($senderPhone);

        // 6. Logika Bisnis (Staff vs Klien)
        if ($isStaff) {
            // Staff membalas: Update SLA
            $pendingComplaints = $this->slaModel->getAllUnrespondedComplaints($groupId);

            if (!empty($pendingComplaints)) {
                foreach ($pendingComplaints as $complaint) {
                    $timeReceived = $complaint['time_received'];
                    $slaSeconds = strtotime($timeNow) - strtotime($timeReceived);

                    // SLA 180 detik
                    $statusSla = $slaSeconds <= 180 ? SlaStatus::HIJAU : SlaStatus::MERAH;

                    $this->slaModel->updateResponse(
                        $complaint['id_monitoring'],
                        $senderPhone,
                        $timeNow,
                        $slaSeconds,
                        $statusSla
                    );
                }
                file_put_contents($logDir . '/webhook.log', sprintf("[%s] SUCCESS: Updated %d SLA complaint(s) by staff %s", date('Y-m-d H:i:s'), count($pendingComplaints), $senderPhone) . PHP_EOL, FILE_APPEND);
                return ['status' => 'success', 'message' => count($pendingComplaints) . ' SLA record(s) updated'];
            }

            file_put_contents($logDir . '/webhook.log', sprintf("[%s] IGNORED: Staff %s chatted but no pending complaint in group %s", date('Y-m-d H:i:s'), $senderPhone, $groupId) . PHP_EOL, FILE_APPEND);
            return ['status' => 'ignored', 'message' => 'No pending complaint in this group'];

        } else {
            // Klien bertanya: Insert baru
            $insertId = $this->slaModel->insertComplaint(
                $groupId,
                $senderPhone,
                $messageContent,
                $timeNow
            );
            file_put_contents($logDir . '/webhook.log', sprintf("[%s] SUCCESS: Inserted complaint #%d from client %s in group %s", date('Y-m-d H:i:s'), $insertId, $senderPhone, $groupId) . PHP_EOL, FILE_APPEND);
            return ['status' => 'success', 'message' => 'Complaint logged', 'id' => $insertId];
        }
    }
}