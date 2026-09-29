<?php
// =============================================================================
// Tiny File Manager – externá konfigurácia
// Tento súbor prepíše predvolené hodnoty v tinyfilemanager.php.
// Upravujte IBA tento súbor, hlavný tinyfilemanager.php nechajte nedotknutý.
//
// Generovanie hashov hesiel: https://tinyfilemanager.github.io/docs/pwd.html
//   alebo v PHP: echo password_hash('moje_heslo', PASSWORD_BCRYPT);
// =============================================================================

// Globálne predvolené UI nastavenia (fallback pre používateľov bez vlastného profilu).
$CONFIG = '{"lang":"sk","error_reporting":false,"show_hidden":false,"hide_Cols":false,"theme":"light","list_density":"compact","fallback_logging":false}';

// --- POUŽÍVATELIA A HESLÁ ---
// Formát: 'meno' => 'bcrypt_hash_hesla'
$auth_users = array(
    'KE' => '$2y$12$Ar2WZ5UMFn6hFU.wPmdrNeurbh70La.Rh0Z5dePPkuXMAOE3ZroA2',
    'Maluzina' => '$2y$12$b5b0HzOeY.uRHN5ZMyytBOYKGajPQ2mvTWw3cP045CB1PkMcnyO4e',
    'NR' => '$2y$12$EFqgZfk55H3kiw7ZnkPxXu4rGqB9JvxgCXvl.ePc8V1dooItJNLSe',
    'PD' => '$2y$12$DYrcjD8t.oDzHxtORIuTv.ZUcLj4LNkLnxWyjJZov/vVShI9Lyzwy',
    'ZA' => '$2y$12$POwPx4XzCsSxuSiTZVyaD.0lYE4P1Ww/jN1agf3I7WfBlC.VYQYm6',
    'ZV' => '$2y$12$KfbQkOsFEL9u4/taIgPCR.333CnvJ6BBFjgxknc1XSBkmcNQbjNgi',
    'admin' => '$2y$10$MDkNAqrsNXnWDpWSUe9po.luFRyHwfktNXEcX0/cqKsnq9NJqPmIG',
    'bilek' => '$2y$10$wC5xZkDTUuwHaaLOqe7pFufzs263KpAXb6CMDjUChfEetUHOOsz5i',
    'chachula' => '$2y$10$aXrwD.R2BgClZAuGDkiwc.twb2UKgPWh7WxYVqdG9eYwP7C1cUUfW',
    'fero' => '$2y$10$CAp.GThS7P4/C7GtWCGM3O.WxICGFjSrV2Xxoi4RsXi4gOlMQvIlW',
    'joyee' => '$2y$10$npAJkc9BGaVg.Wzyf0t/DuAKyk6nDwRWEBTV6YPH1LiokG4weQQm2',
    'kicin' => '$2y$10$WiObQoB/OV.f46d7lIj9ZODXaaxWNGX4m3dUqPu9xWo.ijsruqpEG',
    'kristian' => '$2y$10$564SbNzU0Yxo180LKdobDOPQoAx8ETwdSyMp2meq5gSPtkmktfmEq',
    'marian' => '$2y$10$01c7A019ZigsppBmpnZ42OFL5T.Q44XXyO8yCVM0ufUSFoM.S6gcS',
    'rehak' => '$2y$10$WUikAfymhLzLrYe51kVC3.YlanYCZMb0ZO7ENhnigFEp3m3AgrzX.',
    'sano' => '$2y$10$.lkxOvPFDOiTG5/sAqe8JeE/JzrkWeqLyJ39uD6VL.go18g4UpNYa',
    'supplier1' => '$2y$10$IyPHHkxanSnPh.LyI3gNxugUprkJfyBa6Rn6vkrYLO03Q8kFGBF22',
    'supplier2' => '$2y$10$oCZ3F1n6/Kzu7zoYGFbpNev4Fq2RzWGq3ydevru7RunYcKIC/JgT6',
    'znava' => '$2y$10$DQ3pvHPHxYp.5ehBn/M7AOOUn.56Ixkdl..0sEINquYopIA7Evhqy',
);

$readonly_users = array(
    'chachula',
    'kicin',
);

$upload_only_users = array(
    'fero',
    'kristian',
    'marian',
    'sano',
);

$manager_users = array(
    'bilek',
    'rehak',
    'znava',
);

$directories_users = array(
    'KE' => __DIR__ . '/Mirko/Nemocnica Prešov/',
    'Maluzina' => __DIR__ . '/Mirko/Nemocnica Prešov/',
    'NR' => __DIR__ . '/Mirko/Nemocnica Prešov/',
    'PD' => __DIR__ . '/Mirko/Nemocnica Prešov/',
    'ZA' => __DIR__ . '/Mirko/Nemocnica Prešov/',
    'ZV' => __DIR__ . '/Mirko/Nemocnica Prešov/',
    'admin' => __DIR__ . '/Mirko/',
    'bilek' => __DIR__ . '/Mirko/',
    'chachula' => __DIR__ . '/Mirko/Nemocnica PP',
    'fero' => __DIR__ . '/Mirko/',
    'joyee' => __DIR__ . '/Joyee',
    'kicin' => __DIR__ . '/Mirko/Nemocnica PP',
    'kristian' => __DIR__ . '/Mirko/BARMO',
    'marian' => array(
        __DIR__ . '/Mirko/Nemocnica PP',
        __DIR__ . '/Mirko/free',
    ),
    'rehak' => __DIR__ . '/Mirko/',
    'sano' => __DIR__ . '/Mirko/BARMO',
    'supplier1' => __DIR__ . '/uploads/supplier1',
    'supplier2' => __DIR__ . '/uploads/supplier2',
    'znava' => __DIR__ . '/Mirko/',
);

$user_manager_owners = array(
    'KE' => 'rehak',
    'Maluzina' => 'rehak',
    'NR' => 'rehak',
    'PD' => 'rehak',
    'ZA' => 'rehak',
    'ZV' => 'rehak',
    'admin' => 'admin',
    'bilek' => 'admin',
    'chachula' => 'bilek',
    'fero' => 'rehak',
    'joyee' => 'admin',
    'kicin' => 'bilek',
    'kristian' => 'rehak',
    'marian' => 'rehak',
    'rehak' => 'admin',
    'sano' => 'rehak',
    'supplier1' => 'admin',
    'supplier2' => 'admin',
    'znava' => 'admin',
);

$user_notes = array(
);

$admin_identity = array(
    'username' => 'admin',
);

$bulk_actions_disabled_users = array(
);

$user_welcome_messages = array(
    'KE' => 'Ahoj {username}, vitaj v Správcovi súborov. Ak budeš potrebovať pomoc, ozvi sa prosím adminovi.',
    'Maluzina' => 'Ahoj {username}, vitaj v Správcovi súborov. Ak budeš potrebovať pomoc, ozvi sa prosím adminovi.',
    'NR' => 'Ahoj {username}, vitaj v Správcovi súborov. Ak budeš potrebovať pomoc, ozvi sa prosím adminovi.',
    'PD' => 'Ahoj {username}, vitaj v Správcovi súborov. Ak budeš potrebovať pomoc, ozvi sa prosím adminovi.',
    'ZA' => 'Ahoj {username}, vitaj v Správcovi súborov. Ak budeš potrebovať pomoc, ozvi sa prosím adminovi.',
    'ZV' => 'Ahoj {username}, vitaj v Správcovi súborov. Ak budeš potrebovať pomoc, ozvi sa prosím adminovi.',
);

$welcome_message_shown_users = array(
    'KE',
    'PD',
    'ZA',
);
