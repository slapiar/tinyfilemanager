# Konfigurácia a prevádzkový stav

Aktualizované pre 3.3.11. Hodnoty závisia od konkrétnej inštalácie; ukážky v repozitári nie sú náhradou jej konfigurácie.

## Pracovný priestor a účty

- `$root_path`: koreň spravovaného úložiska.
- `$root_url`: URL pre súbory, ak sú dostupné cez web.
- `$use_auth`, `$auth_users`: prihlasovanie a účty.
- `$directories_users`: pridelené adresáre.
- `$manager_users`, `$readonly_users`, `$upload_only_users`: role.
- `$user_manager_owners`: priamo priradený manažér používateľa.
- `$bulk_actions_disabled_users`: vypnutie hromadných akcií pre vybrané účty; admin má výnimku.
- `$global_readonly`: režim iba na čítanie; v tomto forku neobmedzuje superadministrátora.

## Súbory a zobrazenie

`$allowed_upload_extensions` riadi prípony uploadu a `$allowed_file_extensions` vytváranie/premenovanie. Admin má z týchto aplikačných filtrov výnimku. `$exclude_items` a voľba skrytých položiek ovplyvňujú výpis. Nastavenie prípon nie je zmenou systémových práv.

Téma a hustota sa ukladajú ako osobné nastavenia. Predvolené sú dark/compact. Ultra je samostatná dočasná voľba v prehliadači. Editor a online náhľady ovplyvňujú aj `$edit_files`, `$use_highlightjs` a `$online_viewer`.

## Ukladanie

`$state_storage_path` nastavuje perzistentný prevádzkový adresár. Bez neho sa používa `.fm_usercfg`. V tomto priestore sú okrem iného `config.sqlite`, `chat.sqlite`, `owner-meta.sqlite` a `search-index.sqlite`. Nie všetky sú cache: chat a vlastníctvo sú nenahraditeľné iba opätovným skenovaním disku. Staršie JSON súbory sa môžu používať pri migrácii.

Konfigurácia účtov a osobné nastavenia používajú aj úložisko `ConfigStore`; pri administrácii uprednostnite príslušné formuláre.

## Index

Platnosť indexu je v aktuálnej implementácii 60 sekúnd a je nastavená v kóde, nie samostatným prepínačom v profile. Nasledujúca požiadavka po expirácii vykoná obnovu. Živý zoznam priečinkov funguje aj bez dostupnej indexovej databázy; ostatné funkcie používajúce SQLite tým nezískavajú náhradné úložisko.

## AI a integrácie

`api.config.php` obsahuje napríklad `$assistant_enabled`, `$assistant_openai_api_key`, `$assistant_openai_model` a `$assistant_openai_base_url`. Skutočný kľúč patrí do serverovej konfigurácie. Nastavenie API neznamená, že je AI Browser sprístupnený ostatným rolám.

[Začíname](?help_doc=wiki-get-started) · [Naše rozšírenia](?help_doc=wiki-our-extensions)
