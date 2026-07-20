<?php
/**
 * Simple ZKTeco Library for PHP (UDP 4370)
 * Modified for logklikdsi integration
 */
class ZKTeco {
    private $ip;
    private $port;
    private $socket;
    private $session_id = 0;
    private $userdata = [];

    const CMD_CONNECT = 1000;
    const CMD_EXIT = 1001;
    const CMD_ENABLEDEVICE = 1002;
    const CMD_DISABLEDEVICE = 1003;
    const CMD_ATTLOG_RRQ = 13;
    const CMD_ACK_OK = 2000;
    const CMD_DATA = 1503;

    public function __construct($ip, $port = 4370) {
        $this->ip = $ip;
        $this->port = $port;
    }

    public function connect() {
        if (!filter_var($this->ip, FILTER_VALIDATE_IP)) return false;
        
        $this->socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        socket_set_option($this->socket, SOL_SOCKET, SO_RCVTIMEO, array("sec" => 5, "usec" => 0));
        
        if (!@socket_connect($this->socket, $this->ip, $this->port)) return false;
        
        $command = self::CMD_CONNECT;
        $command_string = $this->createHeader($command, '');
        $chksum = $this->checkSum($command_string);
        $session_id = 0;
        $reply_id = -1 + 65536;
        
        $payload = pack('vvvv', $command, $chksum, $session_id, $reply_id);
        $buf = $this->wrapTCP($payload);
        
        socket_send($this->socket, $buf, strlen($buf), 0);
        
        $reply = '';
        $recv = socket_recv($this->socket, $reply, 1024, 0);
        
        if ($recv > 8) {
            // Remove TCP header (8 bytes usually: 4 byte signature + 4 byte len? No, usually it's [EB EF] [LenL LenH] or similar)
            // But most ZK TCP is just [4 bytes little endian length] + [8 byte original header]
            $data = $this->unwrapTCP($reply);
            $u = unpack('vcmd/vchk/vses/vrep', substr($data, 0, 8));
            $this->session_id = $u['ses'];
            return $u['cmd'] == self::CMD_ACK_OK;
        }
        return false;
    }

    public function disconnect() {
        if ($this->socket) {
            $command = self::CMD_EXIT;
            $payload = $this->createHeader($command, '');
            $buf = $this->wrapTCP($payload);
            socket_send($this->socket, $buf, strlen($buf), 0);
            socket_close($this->socket);
        }
    }

    public function getAttendance() {
        if (!$this->session_id) return [];

        $command = self::CMD_ATTLOG_RRQ;
        $payload = $this->createHeader($command, '');
        $buf = $this->wrapTCP($payload);
        socket_send($this->socket, $buf, strlen($buf), 0);

        $reply = '';
        $attendance = [];
        
        // Receive data chunks
        while (true) {
            $chunk = '';
            $res = socket_recv($this->socket, $chunk, 1024 * 32, 0);
            if ($res <= 0 || !$chunk) break;
            
            $data = $this->unwrapTCP($chunk);
            if (strlen($data) < 8) break;

            $header = unpack('vcmd/vchk/vses/vrep', substr($data, 0, 8));
            if ($header['cmd'] == self::CMD_DATA) {
                $content = substr($data, 8);
                $attendance = array_merge($attendance, $this->parseAttendanceData($content));
            } elseif ($header['cmd'] == self::CMD_ACK_OK) {
                // Done or small acknowledgment
                if (strlen($data) > 8) {
                   $content = substr($data, 8);
                   $attendance = array_merge($attendance, $this->parseAttendanceData($content));
                }
                // If it's just ACK, we might need to listen for more or it's finished
            } else {
                break;
            }
            if (strlen($chunk) < 1024) break; // Arbitrary break for single packet responses
        }
        return $attendance;
    }

    private function wrapTCP($payload) {
        $header = pack('v', 0x5050) . pack('v', strlen($payload)); // Some use 50 50 as signature for TCP
        // Actually, many use: [Length (4 bytes, little endian)]
        // Let's try the common ZK TCP header: [50 50 82 7D] + [4 byte length] or just [4 byte length]
        // Standard ZK TCP: [0x50 0x50 0x82 0x7d] (fixed 4 bytes) + [Length 2 bytes] + [Checksum 2 bytes] + [Payload]
        // BUT simplest one is: [4 bytes length little-endian]
        return pack('V', strlen($payload)) . $payload;
    }

    private function unwrapTCP($packet) {
        // Remove 4 bytes length
        return substr($packet, 4);
    }

    private function parseAttendanceData($data) {
        $records = [];
        $record_size = 40; // Common for newer devices, older was 14
        
        // Auto-detect record size based on data length
        if (strlen($data) % 40 != 0 && strlen($data) % 14 == 0) {
            $record_size = 14;
        }

        for ($i = 0; $i < strlen($data); $i += $record_size) {
            $record = substr($data, $i, $record_size);
            if (strlen($record) < $record_size) break;

            if ($record_size == 14) {
                $u = unpack('vuid/vstatus/Vtimestamp', substr($record, 0, 8));
                $records[] = [
                    'uid' => $u['uid'],
                    'timestamp' => $this->decodeTime($u['timestamp'])
                ];
            } else {
                // Protocol 40 bytes
                $u = unpack('vuid/vstatus/Vtimestamp', substr($record, 0, 8));
                // Extract User ID string (offset 8 for newer devices)
                $uid_str = rtrim(substr($record, 8, 24), "\0");
                $records[] = [
                    'uid' => $uid_str ? $uid_str : $u['uid'],
                    'timestamp' => $this->decodeTime($u['timestamp'])
                ];
            }
        }
        return $records;
    }

    private function decodeTime($t) {
        $second = $t % 60;
        $t = floor($t / 60);
        $minute = $t % 60;
        $t = floor($t / 60);
        $hour = $t % 24;
        $t = floor($t / 24);
        $day = ($t % 31) + 1;
        $t = floor($t / 31);
        $month = ($t % 12) + 1;
        $t = floor($t / 12);
        $year = $t + 2000;
        
        return "$year-" . str_pad($month, 2, '0', STR_PAD_LEFT) . "-" . str_pad($day, 2, '0', STR_PAD_LEFT) . " " . 
               str_pad($hour, 2, '0', STR_PAD_LEFT) . ":" . str_pad($minute, 2, '0', STR_PAD_LEFT) . ":" . str_pad($second, 2, '0', STR_PAD_LEFT);
    }

    private function createHeader($command, $data) {
        $chksum = 0;
        $session_id = $this->session_id;
        $reply_id = 0; // Should increment but many devices don't care initially
        
        $buf = pack('vvvv', $command, $chksum, $session_id, $reply_id) . $data;
        $u = unpack('v*', $buf);
        $sum = array_sum($u);
        
        while ($sum >> 16) {
            $sum = ($sum & 0xFFFF) + ($sum >> 16);
        }
        $chksum = ~$sum & 0xFFFF;
        
        return pack('vvvv', $command, $chksum, $session_id, $reply_id) . $data;
    }

    private function checkSum($p) {
        $u = unpack('v*', $p);
        $sum = array_sum($u);
        while ($sum >> 16) {
            $sum = ($sum & 0xFFFF) + ($sum >> 16);
        }
        return ~$sum & 0xFFFF;
    }
}
