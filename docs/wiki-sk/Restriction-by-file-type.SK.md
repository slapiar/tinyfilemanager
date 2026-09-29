# Obmedzenie podľa typu súboru (Restriction by file type)

Nahrávanie, vytváranie a premenovanie súborov je možné obmedziť podľa prípony,
pomocou nasledujúcich aplikačných nastavení.

- Povolené prípony pre nahrávanie sú definované v premennej `$allowed_upload_extensions`.
- Povolené prípony pre vytváranie a premenovanie sú definované v premennej `$allowed_file_extensions`.

```php
// Allowed file extensions for create and rename files
// e.g. 'txt,html,css,js'
$allowed_file_extensions = 'txt,html,js,css,scss';

// Allowed file extensions for upload files
// e.g. 'gif,png,jpg,html,txt'
$allowed_upload_extensions = 'jpg,jpeg,gif,txt,mp4';
```

## Náš fork (3.3.11)

Administrátor má z oboch filtrov prípon výnimku. Limity PHP a práva súborového systému zostávajú platné. Fotografia z kamery používa rovnaký upload a rovnaké pravidlá ako ostatné nahrávané súbory. Tieto nastavenia neurčujú podporu náhľadu alebo analýzy binárneho formátu v AI.
