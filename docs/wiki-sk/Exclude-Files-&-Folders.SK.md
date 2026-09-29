# Vylúčenie súborov a priečinkov (Exclude Files & Folders)

Pomocou konfigurácie môžeš určiť súbory alebo adresáre, ktoré sa nemajú zobrazovať vo výpise.
Ak sa rovnaký názov súboru alebo priečinka nachádza na viacerých miestach, pravidlo vylúčenia sa uplatní na všetky zhody.

```php
// Files and folders to excluded from listing
// e.g. array('myfile.html', 'personal-folder', '*.php', ...)
$exclude_items = array(
    'my-folder',
    'secret-files',
    'tinyfilemanager.php',
    '*.php',
    '*.js'
);
```

## Skryté položky a práva

Názvy začínajúce bodkou, napríklad `.fm_usercfg`, ovplyvňuje aj nastavenie zobrazovania skrytých položiek. Rozdiel medzi výpisom hostingu a aplikácie preto nemusí vždy znamenať starý index. Vylúčenie z výpisu nie je náhradou oprávnení webservera ani rolí používateľa.
