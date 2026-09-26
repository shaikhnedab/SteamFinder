<?php

return [

    'title' => 'Steam Finder',
    'steam_finder' => '蒸汽探测器',

    'enter_steamid' => '输入 SteamID',
    'search_placeholder' => 'SteamID / SteamID3 / SteamID64 / 自定义 URL / 完整 URL',
    'search' => '搜索',
    'search_button' => '搜索',

    'result' => '结果',
    'player_name' => '玩家名称',
    'steamid' => 'SteamID',
    'steamid64' => 'SteamID64',
    'steamid3' => 'SteamID3',
    'custom_url' => '自定义网址',
    'profile_url' => '个人资料 URL / 永久个人资料链接',
    'fivem_hex' => 'FiveM, HEX',
    'account_id' => '帐户 ID',
    'real_name' => '真实姓名',
    'profile_state' => '个人资料状态',
    'profile_created' => '配置文件创建',
    'vacbanned' => 'VACBanned',
    'last_ban' => '自上次禁令以来的天数',
    'invite_url' => '邀请网址',
    'csgo' => 'CSGO',
    'extra' => '额外',

    'search_alert' => '此应用将通过输入任何 steamid 格式提取您的 Steam 详细信息。',

    /* Field-instrument UI */
    'kicker' => 'Steam 身份工具',
    'hero_title' => '将任意 SteamID 转换为它的所有格式。',
    'hero_text' => '粘贴 SteamID、SteamID3、SteamID64、自定义网址或完整的个人资料链接，即可获取全部标识符、个人资料状态、VAC 状态和邀请链接——每项一键复制。',
    'search_note' => '接受 SteamID、SteamID3、SteamID64、自定义网址和完整的 steamcommunity.com 链接。',
    'accepts_label' => '支持的输入格式',
    'result_label' => '为每个个人资料解析的字段',
    'identifiers' => '标识符',
    'identity' => '身份信息',
    'account_safety' => '账号安全',
    'playtime' => '游戏时间',

    /* badge values (previously hardcoded English in the view) */
    'state_online' => '在线',
    'state_offline' => '离线',
    'state_busy' => '忙碌',
    'state_away' => '离开',
    'banned' => '已被封禁',
    'no_bans' => '无封禁',
    'ban_days' => '{0} 无封禁|{1} 距上次封禁 1 天|[2,*] 距上次封禁 :count 天',

    'open_profile' => '打开个人资料',
    'copy_action' => '复制 :label',
    'copied' => '已复制',
    'copy_failed' => '复制失败 — 请手动选择并复制',
    'theme_toggle' => '切换主题',
    'dismiss' => '关闭',

    /* controller messages */
    'profile_found' => '已找到个人资料！',
    'error_invalid_input' => '请输入有效的搜索内容。',
    'error_invalid_format' => '无法识别的 ID 格式。请使用 SteamID、SteamID3、SteamID64、自定义网址或完整的 steamcommunity.com 链接。',
    'error_check_id' => '获取数据失败，请检查 ID！',
    'error_no_api_key' => '未配置 Steam API 密钥。',
    'error_api_unreachable' => '无法连接 Steam API，请稍后重试。',

    /* error pages */
    'back_home' => '返回搜索',
    'error_404_title' => '页面不存在',
    'error_404_text' => '你访问的地址不是 Steam 个人资料。请返回并尝试 SteamID、自定义网址或完整的个人资料链接。',
    'error_500_title' => '出错了',
    'error_500_text' => '服务器遇到了意外错误。该错误已记录，请稍后重试。',

];
