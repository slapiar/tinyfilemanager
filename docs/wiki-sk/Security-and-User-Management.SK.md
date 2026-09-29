# Účty, roly a oprávnenia

Aktualizované pre 3.3.11.

## Správa účtov

Administrátor otvorí správu používateľov ikonou v hlavičke. Môže vytvoriť alebo upraviť účet, zmeniť jeho meno, heslo, rolu, pridelené adresáre, manažéra a dostupnosť hromadných akcií. Meno musí mať aspoň dva znaky a nesmie kolidovať s iným účtom; ďalšie podmienky kontroluje formulár.

Premenovanie vykonávajte cez správu účtov, ktorá prenesie súvisiace údaje. Prázdne heslo pri úprave existujúceho účtu zachová pôvodné heslo. Používateľ si vlastné heslo mení cez **Môj profil / Zmena hesla**.

## Význam rolí

- **Administrátor:** celý nakonfigurovaný pracovný priestor, správa účtov a hromadné akcie. Uložené zoznamy readonly, upload-only, manažérov či zákaz hromadných akcií ho neobmedzujú ako bežný účet. Rola neudeľuje systémové práva root na webhostingu.
- **Manažér:** pridelené adresáre a správa používateľov v povolenom rozsahu; mazanie súborov a priečinkov nie je dostupné.
- **Štandardný účet:** práca v povolených adresároch podľa konfigurácie.
- **Iba na čítanie:** bez vytvárania, úprav, uploadu a mazania.
- **Iba na nahrávanie:** odovzdávanie súborov; ostatné operácie sú obmedzené.

Označenia klient a dodávateľ sú pracovné pomenovania; prístup určuje rola a priradené adresáre. Ak má účet neplatné pridelenie adresárov, aplikácia môže použiť spoločný priečinok `free`; pri nedostupnosti náhradnej cesty prístup odmietne.

## Heslá a konfigurácia

Heslá sa ukladajú ako hashe vytvorené `password_hash()`, nie ako čitateľné heslá ani vratné šifrovanie. Nasadenie používa vlastné účty; údaje z pôvodných ukážok nie sú prihlasovacími údajmi vašej inštalácie.

`$use_auth` zapína overovanie používateľov. `$directories_users`, `$manager_users`, `$readonly_users`, `$upload_only_users` a `$user_manager_owners` sú súvisiace konfiguračné položky. Zápis z rozhrania používa aj prevádzkové úložisko konfigurácie; náhodná ručná zmena jedného súboru nemusí zodpovedať všetkým uloženým údajom.

## Pri probléme s mazaním

Rozlišujte zákaz operácie rolou, chýbajúcu položku zo starého zoznamu a chybu súborového systému. Od verzie 3.3.10 hromadné mazanie už chýbajúce položky preskočí po overení rodičovského adresára. Chyby prístupu sa nezamieňajú za potvrdenú neexistenciu.

[Používateľská príručka](?help_doc=user-guide) · [Časté otázky](?help_doc=wiki-faq)
