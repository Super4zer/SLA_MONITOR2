<?php
/**
 * ZKLibrary - PHP ZKTeco Library (UDP 4370)
 * Works for most ZK based devices.
 */
class ZKLibrary {
    private $ip;
    private $port;
    private $socket;
    private $session_id = 0;
    private $reply_id = 0;

    const CMD_CONNECT = 1000;
    const CMD_EXIT = 1001;
    const CMD_ENABLEDEVICE = 1002;
    const CMD_DISABLEDEVICE = 1003;
    const CMD_ATTLOG_RRQ = 13;
    const CMD_ACK_OK = 2000;
    const CMD_ACK_ERROR = 2001;
    const CMD_ACK_DATA = 1503;
    const CMD_PREPARE_DATA = 1500;
    const CMD_DATA = 1501;

    public function __construct($ip, $port = 4370) {
        $this->ip = $ip;
        $this->port = $port;
    }

    private function createHeader($command, $command_string, $session_id, $reply_id) {
        $buf = pack('vvvv', $command, 0, $session_id, $reply_id) . $command_string;
        $u = unpack('v*', $buf);
        $sum = array_sum($u);
        while ($sum >> 16) {
            $sum = ($sum & 0xFFFF) + ($sum >> 16);
        }
        $chksum = ~$sum & 0xFFFF;
        return pack('vvvv', $command, $chksum, $session_id, $reply_id) . $command_string;
    }

    public function connect() {
        $this->socket = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
        socket_set_option($this->socket, SOL_SOCKET, SO_RCVTIMEO, array("sec" => 5, "usec" => 0));
        
        $command = self::CMD_CONNECT;
        $command_string = '';
        $session_id = 0;
        $reply_id = 0;
        $header = $this->createHeader($command, $command_string, $session_id, $reply_id);
        
        socket_sendto($this->socket, $header, strlen($header), 0, $this->ip, $this->port);
        
        $res = @socket_recvfrom($this->socket, $reply, 1024, 0, $this->ip, $this->port);
        if ($res) {
            $u = unpack('vcmd/vchk/vses/vrep', substr($reply, 0, 8));
            $this->session_id = $u['ses'];
            $this->reply_id = $u['rep'];
            return $u['cmd'] == self::CMD_ACK_OK;
        }
        return false;
    }

    public function disconnect() {
        if ($this->socket && $this->session_id) {
            $header = $this->createHeader(self::CMD_EXIT, '', $this->session_id, $this->reply_id);
            socket_sendto($this->socket, $header, strlen($header), 0, $this->ip, $this->port);
            socket_close($this->socket);
        }
    }

    public function getAttendance() {
        if (!$this->session_id) return [];

        $header = $this->createHeader(self::CMD_ATTLOG_RRQ, '', $this->session_id, $this->reply_id);
        socket_sendto($this->socket, $header, strlen($header), 0, $this->ip, $this->port);
        
        $res = @socket_recvfrom($this->socket, $reply, 1024, 0, $this->ip, $this->port);
        if (!$res) return [];

        $header_resp = unpack('vcmd/vchk/vses/vrep', substr($reply, 0, 8));
        if ($header_resp['cmd'] == self::CMD_ACK_DATA || $header_resp['cmd'] == self::CMD_PREPARE_DATA) {
            // Some devices return CMD_PREPARE_DATA (1500) and we must listen for packets
            $size = unpack('Vsize', substr($reply, 8, 4))['size'];
            $attendance_data = '';
            while (strlen($attendance_data) < $size) {
                if (!@socket_recvfrom($this->socket, $packet, 1024 + 8, 0, $this->ip, $this->port)) break;
                $attendance_data .= substr($packet, 8);
            }
            return $this->parseData($attendance_data);
        } else if ($header_resp['cmd'] == self::CMD_ACK_OK && strlen($reply) > 8) {
            // Small amount of data returned in one packet
            return $this->parseData(substr($reply, 8));
        }
        return [];
    }

    private function parseData($data) {
        $records = [];
        $record_size = (strlen($data) % 40 == 0) ? 40 : 14;
        for ($i = 0; $i < strlen($data); $i += $record_size) {
            $record = substr($data, $i, $record_size);
            if (strlen($record) < $record_size) break;
            
            if ($record_size == 14) {
                $u = unpack('vuid/vstatus/Vtimestamp', substr($record, 0, 8));
                $records[] = ['uid' => $u['uid'], 'timestamp' => $this->decodeTime($u['timestamp'])];
            } else {
                $u = unpack('vuid/vstatus/Vtimestamp', substr($record, 0, 8));
                $uid_str = rtrim(substr($record, 8, 24), "\0");
                $records[] = ['uid' => $uid_str ? $uid_str : $u['uid'], 'timestamp' => $this->decodeTime($u['timestamp'])];
            }
        }
        return $records;
    }

    private function decodeTime($t) {
        $second = $t % 60; $t = floor($t / 60);
        $minute = $t % 60; $t = floor($t / 60);
        $hour = $t % 24; $t = floor($t / 24);
        $day = ($t % 31) + 1; $t = floor($t / 31);
        $month = ($t % 12) + 1; $t = floor($t / 12);
        $year = $t + 2000;
        return "$year-" . str_pad($month, 2, '0', STR_PAD_LEFT) . "-" . str_pad($day, 2, '0', STR_PAD_LEFT) . " " . 
               str_pad($hour, 2, '0', STR_PAD_LEFT) . ":" . str_pad($minute, 2, '0', STR_PAD_LEFT) . ":" . str_pad($second, 2, '0', STR_PAD_LEFT);
    }
}
