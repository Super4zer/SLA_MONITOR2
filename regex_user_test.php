<?php
$html = file_get_contents('d:/PROJEK NGODINK/magang anjeng/logklikdsi-main/response_user.html');
$regex = '/<input type=checkbox name=uid value=(\d+)>\s*<\/td>\s*<td.*?<\/td>\s*<td.*?>(\d+)<\/td>\s*<td.*?>([^<]*)<\/td>\s*<td.*?>[^<]*<\/td>\s*<td.*?>[^<]*<\/td>\s*<td.*?>([^<]*)<\/td>/is';
preg_match_all($regex, $html, $matches, PREG_SET_ORDER);
print_r($matches);
