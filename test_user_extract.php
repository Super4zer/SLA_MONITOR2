<?php
$html = file_get_contents(__DIR__ . "/response2.html");

// Regex to extract UID and NAME
// 1. match the checkbox value
// 2. match the <td> that follows
// 3. match the next <td> (ID Number)
// 4. match the next <td> (Name)
preg_match_all('/<input[^>]+type=[\'"]?checkbox[\'"]?[^>]+name=[\'"]?(?:[^>\s\'"]+)[\'"]?[^>]+value=[\'"]?([^>\s\'"]+)[\'"]?>.*?<td[^>]*>(.*?)<\/td>\s*<td[^>]*>(.*?)<\/td>\s*<td[^>]*>([^<]+)<\/td>/is', $html, $m_users);

print_r($m_users[3]); // ID Numbers
print_r($m_users[4]); // names
?>
