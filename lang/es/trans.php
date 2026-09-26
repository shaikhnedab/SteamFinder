<?php

return [

    'title' => 'Buscador de Steam',
    'steam_finder' => 'Buscador de Steam',

    'enter_steamid' => 'Ingresar ID de Steam',
    'search_placeholder' => 'SteamID / SteamID3 / SteamID64 / URL personalizada / URL completa',
    'search' => 'Buscar',
    'search_button' => 'Buscar',

    'result' => 'Resultado',
    'player_name' => 'Nombre del jugador',
    'steamid' => 'ID de Steam',
    'steamid64' => 'SteamID64',
    'steamid3' => 'SteamID3',
    'custom_url' => 'URL personalizada',
    'profile_url' => 'URL de perfil / Enlace de perfil permanente',
    'fivem_hex' => 'CincoM, HEX',
    'account_id' => 'ID de cuenta',
    'real_name' => 'Nombre real',
    'profile_state' => 'Estado del perfil',
    'profile_created' => 'Perfil creado',
    'vacbanned' => 'VACBanned',
    'last_ban' => 'Días desde la última prohibición',
    'invite_url' => 'URL de invitación',
    'csgo' => 'CSGO',
    'extra' => 'Extra',

    'search_alert' => 'Esta aplicación extraerá sus detalles de Steam ingresando cualquier formato de SteamID.',

    /* Field-instrument UI */
    'kicker' => 'Utilidades de identidad de Steam',
    'hero_title' => 'Convierte cualquier SteamID en todos los formatos que puede ser.',
    'hero_text' => 'Pega un SteamID, SteamID3, SteamID64, URL personalizada o enlace completo de perfil y obtén todos los identificadores, estado del perfil, estado de VAC y enlace de invitación: copia cada uno con un clic.',
    'search_note' => 'Acepta SteamID, SteamID3, SteamID64, URL personalizadas y enlaces completos de steamcommunity.com.',
    'accepts_label' => 'Formatos de entrada aceptados',
    'result_label' => 'Campos resueltos para cada perfil',
    'identifiers' => 'Identificadores',
    'identity' => 'Identidad',
    'account_safety' => 'Seguridad de la cuenta',
    'playtime' => 'Tiempo de juego',

    /* badge values (previously hardcoded English in the view) */
    'state_online' => 'Conectado',
    'state_offline' => 'Desconectado',
    'state_busy' => 'Ocupado',
    'state_away' => 'Ausente',
    'banned' => 'Bloqueado',
    'no_bans' => 'Sin bloqueos',
    'ban_days' => '{0} Sin bloqueos|{1} 1 día desde el último bloqueo|[2,*] :count días desde el último bloqueo',

    'open_profile' => 'Abrir perfil',
    'copy_action' => 'Copiar :label',
    'copied' => 'Copiado',
    'copy_failed' => 'Error al copiar: selecciona y copia manualmente',
    'theme_toggle' => 'Cambiar tema',
    'dismiss' => 'Cerrar',

    /* controller messages */
    'profile_found' => '¡Perfil encontrado!',
    'error_invalid_input' => 'Introduce un valor de búsqueda válido.',
    'error_invalid_format' => 'Formato de ID no reconocido. Usa un SteamID, SteamID3, SteamID64, URL personalizada o un enlace completo de steamcommunity.com.',
    'error_check_id' => 'No se pudieron obtener los datos, ¡comprueba el ID!',
    'error_no_api_key' => 'La clave de API de Steam no está configurada.',
    'error_api_unreachable' => 'No se pudo conectar con la API de Steam. Inténtalo de nuevo más tarde.',

    /* error pages */
    'back_home' => 'Volver a la búsqueda',
    'error_404_title' => 'Esa página no existe',
    'error_404_text' => 'La dirección que seguiste no es un perfil de Steam. Vuelve e intenta con un SteamID, una URL personalizada o un enlace completo de perfil.',
    'error_500_title' => 'Algo salió mal',
    'error_500_text' => 'El servidor encontró un error inesperado. Quedó registrado en el log; inténtalo de nuevo en un momento.',

];
