<?php

return [

    'title' => 'Steam Finder',
    'steam_finder' => 'Steam Finder',

    'enter_steamid' => 'הזן SteamID',
    'search_placeholder' => 'SteamID / SteamID3 / SteamID64 / כתובת אתר מותאמת אישית / כתובת אתר מלאה',
    'search' => 'לחפש',
    'search_button' => 'לחפש',

    'result' => 'תוֹצָאָה',
    'player_name' => 'שם שחקן',
    'steamid' => 'SteamID',
    'steamid64' => 'SteamID64',
    'steamid3' => 'SteamID3',
    'custom_url' => 'כתובת אתר מותאם אישית',
    'profile_url' => 'כתובת אתר של פרופיל / קישור פרופיל קבוע',
    'fivem_hex' => 'FiveM, HEX',
    'account_id' => 'מזהה חשבון',
    'real_name' => 'שם אמיתי',
    'profile_state' => 'מצב פרופיל',
    'profile_created' => 'פרופיל נוצר',
    'vacbanned' => 'VACB נבנה',
    'last_ban' => 'ימים מאז האיסור האחרון',
    'invite_url' => 'כתובת אתר של הזמנה',
    'csgo' => 'CSGO',
    'extra' => 'תוֹסֶפֶת',

    'search_alert' => 'אפליקציה זו תחלץ את פרטי ה-steam שלך על ידי הזנת כל פורמט steam.',

    /* Field-instrument UI (shared design language with the HVAC suite) */
    'kicker' => 'כלי זהות Steam',
    'hero_title' => 'המר כל SteamID לכל הפורמטים שהוא יכול להיות.',
    'hero_text' => 'הדבק SteamID, SteamID3, SteamID64, כתובת מותאמת אישית או קישור פרופיל מלא וקבל בחזרה את כל המזהים, מצב הפרופיל, מצב VAC וקישור ההזמנה — העתקה בלחיצה אחת לכל אחד.',
    'search_note' => 'מקבל SteamID, SteamID3, SteamID64, כתובות מותאמות אישית וקישורי steamcommunity.com מלאים.',
    'accepts_label' => 'פורמטי קלט מתקבלים',
    'result_label' => 'השדות המפוענים לכל פרופיל',
    'identifiers' => 'מזהים',
    'identity' => 'זהות',
    'account_safety' => 'בטיחות החשבון',
    'playtime' => 'זמן משחק',

    /* badge values (previously hardcoded English in the view) */
    'state_online' => 'מחובר',
    'state_offline' => 'לא מחובר',
    'state_busy' => 'עסוק',
    'state_away' => 'לא נמצא',
    'banned' => 'נחסם',
    'no_bans' => 'ללא חסימות',
    'ban_days' => '{0} ללא חסימות|{1} יום אחרי החסימה האחרונה|[2,*] :count ימים אחרי החסימה האחרונה',

    'open_profile' => 'פתח פרופיל',
    'copy_action' => 'העתק :label',
    'copied' => 'הועתק',
    'copy_failed' => 'ההעתקה נכשלה — יש לבחור ולהעתיק ידנית',
    'theme_toggle' => 'החלף ערכת נושא',
    'dismiss' => 'סגור',

    /* controller messages */
    'profile_found' => 'הפרופיל נמצא!',
    'error_invalid_input' => 'יש להזין קלט תקין.',
    'error_invalid_format' => 'פורמט מזהה לא מזוהה. השתמש ב-SteamID, SteamID3, SteamID64, כתובת מותאמת אישית או בקישור מלא ל-steamcommunity.com.',
    'error_check_id' => 'אירעה תקלה בקבלת הנתונים, בדוק את המזהה!',
    'error_no_api_key' => 'מפתח ה-API של Steam אינו מוגדר.',
    'error_api_unreachable' => 'התקשרות ל-API של Steam נכשלה. נסה שוב מאוחר יותר.',

    /* error pages */
    'back_home' => 'חזרה לחיפוש',
    'error_404_title' => 'הדף הזה אינו קיים',
    'error_404_text' => 'הכתובת שפתחת אינה פרופיל Steam. חזור לדף החיפוש ונסה SteamID, כתובת מותאמת אישית או קישור מלא לפרופיל.',
    'error_500_title' => 'משהו השתבש',
    'error_500_text' => 'השרת נתקל בשגיאה בלתי צפויה. הפעולה נרשמה ביומן — נסה שוב בעוד רגע.',

];
