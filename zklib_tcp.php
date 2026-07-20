<?php
/**
 * ZKLibraryTCP - PHP ZKTeco Library (TCP 4370)
 * Optimized for machines requiring TCP connection with 0x5050827D magic header.
 */
class ZKLibraryTCP {
    private $ip;
    private $port;
    private $socket;
    private $session_id = 0;
    private $reply_id = 0;

    const CMD_CONNECT = 1000;
    const CMD_EXIT = 1001;
    const CMD_ATTLOG_RRQ = 13;
    const CMD_ACK_OK = 2000;
    const CMD_PREPARE_DATA = 1500;
    const CMD_ACK_DATA = 1503;
    const CMD_DATA = 1501;

    public function __construct($ip, $port = 4370) {
        $this->ip = $ip;
        $this->port = $port;
    }

    private function createHeader($command, $command_string, $session_id, $reply_id) {
        // Standard ZK 8-byte header payload
        $buf = pack('vvvv', $command, 0, $session_id, $reply_id) . $command_string;
        $u = unpack('v*', $buf);
        $sum = array_sum($u);
        while ($sum >> 16) {
            $sum = ($sum & 0xFFFF) + ($sum >> 16);
        }
        $chksum = ~$sum & 0xFFFF;
        $zkHeader = pack('vvvv', $command, $chksum, $session_id, $reply_id) . $command_string;
        
        // TCP Wrap per research: [Magic 4 bytes] [Length 4 bytes] [ZK Header 8 bytes...]
        // Magic Prefix: 0x50 0x50 0x82 0x7D
        $magic = "\x50\x50\x82\x7d";
        $size = pack('V', strlen($zkHeader)); // 4-byte little-endian
        
        return $magic . $size . $zkHeader;
    }

    public function connect() {
        $this->socket = @socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        @socket_set_option($this->socket, SOL_SOCKET, SO_RCVTIMEO, array("sec" => 5, "usec" => 0));
        @socket_set_option($this->socket, SOL_SOCKET, SO_SNDTIMEO, array("sec" => 5, "usec" => 0));
        
        if (!@socket_connect($this->socket, $this->ip, $this->port)) return false;

        $header = $this->createHeader(self::CMD_CONNECT, '', 0, 0);
        @socket_send($this->socket, $header, strlen($header), 0);
        
        $reply = @socket_read($this->socket, 1024);
        if ($reply && strlen($reply) >= 16) {
            // Skip 8 bytes TCP header (4 bytes magic + 4 bytes size)
            $u = unpack('vcmd/vchk/vses/vrep', substr($reply, 8, 8));
            $this->session_id = $u['ses'];
            $this->reply_id = $u['rep'];
            return $u['cmd'] == self::CMD_ACK_OK;
        }
        return false;
    }

    public function disconnect() {
        if ($this->socket && $this->session_id) {
            $header = $this->createHeader(self::CMD_EXIT, '', $this->session_id, $this->reply_id);
            @socket_send($this->socket, $header, strlen($header), 0);
            @socket_close($this->socket);
        }
    }

    public function getAttendance() {
        if (!$this->session_id) return [];

        $header = $this->createHeader(self::CMD_ATTLOG_RRQ, '', $this->session_id, $this->reply_id);
        @socket_send($this->socket, $header, strlen($header), 0);
        
        $reply = @socket_read($this->socket, 1024);
        if (!$reply || strlen($reply) < 16) return [];

        $header_resp = unpack('vcmd/vchk/vses/vrep', substr($reply, 8, 8));
        
        if ($header_resp['cmd'] == self::CMD_ACK_DATA || $header_resp['cmd'] == self::CMD_PREPARE_DATA) {
            $size = unpack('Vsize', substr($reply, 16, 4))['size'];
            $attendance_data = substr($reply, 20); // Data starts after TCP (8) + ZK (8) + Size field (4)?
            // Actually research says ZK Header is 8 bytes. TCP header is 8 bytes.
            // If CMD_PREPARE_DATA, payload starts at offset 16.
            
            while (strlen($attendance_data) < $size) {
                $packet = @socket_read($this->socket, 2048);
                if (!$packet) break;
                // Check if packet has TCP header
                if (strlen($packet) > 8 && substr($packet, 0, 4) == "\x50\x50\x82\x7d") {
                    $attendance_data .= substr($packet, 16); // Skip TCP (8) and ZK (8) headers
                } else {
                    $attendance_data .= $packet;
                }
            }
            return $this->parseData(substr($attendance_data, 0, $size));
        }
        return [];
    }

    private function parseData($data) {
        $records = [];
        $record_size = (strlen($data) % 40 == 0) ? 40 : 14;
        for ($i = 0; $i < strlen($data); $i += $record_size) {
            $record = substr($data, $i, $record_size);
            if (strlen($record) < $record_size) break;
            $u = unpack('vuid/vstatus/Vtimestamp', substr($record, 0, 8));
            if ($record_size == 14) {
                $records[] = ['uid' => $u['uid'], 'timestamp' => $this->decodeTime($u['timestamp'])];
            } else {
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
