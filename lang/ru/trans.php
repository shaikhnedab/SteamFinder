<?php

return [

    'title' => 'Поиск Steam',
    'steam_finder' => 'Поиск Steam',

    'enter_steamid' => 'Введите SteamID',
    'search_placeholder' => 'SteamID / SteamID3 / SteamID64 / Пользовательский URL / Полный URL',
    'search' => 'Поиск',
    'search_button' => 'Поиск',

    'result' => 'результат',
    'player_name' => 'Имя игрока',
    'steamid' => 'SteamID',
    'steamid64' => 'SteamID64',
    'steamid3' => 'SteamID3',
    'custom_url' => 'Пользовательский URL',
    'profile_url' => 'URL-адрес профиля / Постоянная ссылка на профиль',
    'fivem_hex' => 'FiveM, HEX',
    'account_id' => 'Идентификатор учетной записи',
    'real_name' => 'Настоящее имя',
    'profile_state' => 'Состояние профиля',
    'profile_created' => 'Профиль создан',
    'vacbanned' => 'VACBanned',
    'last_ban' => 'Дней с момента последнего бана',
    'invite_url' => 'URL-адрес приглашения',
    'csgo' => 'ксго',
    'extra' => 'дополнительно',

    'search_alert' => 'Это приложение извлечет ваши данные Steam, введя любой формат steamid.',

    /* Field-instrument UI (shared design language with the HVAC suite) */
    'kicker' => 'Инструменты идентификации Steam',
    'hero_title' => 'Преобразуйте любой SteamID во все возможные форматы.',
    'hero_text' => 'Вставьте SteamID, SteamID3, SteamID64, пользовательский URL или полную ссылку на профиль — и получите все идентификаторы, состояние профиля, статус VAC и ссылку-приглашение, каждое в один клик.',
    'search_note' => 'Принимает SteamID, SteamID3, SteamID64, пользовательские URL и полные ссылки steamcommunity.com.',
    'accepts_label' => 'Принимаемые форматы ввода',
    'result_label' => 'Поля, определяемые для каждого профиля',
    'identifiers' => 'Идентификаторы',
    'identity' => 'Личность',
    'account_safety' => 'Безопасность аккаунта',
    'playtime' => 'Время в игре',

    /* badge values (previously hardcoded English in the view) */
    'state_online' => 'В сети',
    'state_offline' => 'Не в сети',
    'state_busy' => 'Занят',
    'state_away' => 'Отошёл',
    'banned' => 'Заблокирован',
    'no_bans' => 'Без блокировок',
    'ban_days' => '{0} Без блокировок|{1} 1 день с последней блокировки|[2,*] :count дней с последней блокировки',

    'open_profile' => 'Открыть профиль',
    'copy_action' => 'Копировать :label',
    'copied' => 'Скопировано',
    'copy_failed' => 'Не удалось скопировать — выделите и скопируйте вручную',
    'theme_toggle' => 'Сменить тему',
    'dismiss' => 'Закрыть',

    /* controller messages */
    'profile_found' => 'Профиль найден!',
    'error_invalid_input' => 'Введите корректный поисковый запрос.',
    'error_invalid_format' => 'Нераспознанный формат ID. Используйте SteamID, SteamID3, SteamID64, пользовательский URL или полную ссылку steamcommunity.com.',
    'error_check_id' => 'Не удалось получить данные, проверьте ID!',
    'error_no_api_key' => 'API-ключ Steam не настроен.',
    'error_api_unreachable' => 'Не удалось подключиться к API Steam. Попробуйте позже.',

    /* error pages */
    'back_home' => 'Вернуться к поиску',
    'error_404_title' => 'Такой страницы не существует',
    'error_404_text' => 'Открытый адрес не является профилем Steam. Вернитесь и попробуйте SteamID, пользовательский URL или полную ссылку на профиль.',
    'error_500_title' => 'Что-то пошло не так',
    'error_500_text' => 'Сервер столкнулся с непредвиденной ошибкой. Она записана в лог — попробуйте ещё раз через минуту.',

];
