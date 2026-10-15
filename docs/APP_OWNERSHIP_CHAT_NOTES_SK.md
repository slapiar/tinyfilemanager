# Vlastníctvo súborov a chat

Aktualizované 29. 9. 2026 pre vydanie 3.3.11. Používateľské vysvetlenie aj prevádzkové poznámky pre správcu.

## Dve vrstvy vlastníctva

Systémový vlastník je účet operačného systému alebo hostingu, pod ktorým súbor existuje. Aplikačný vlastník je používateľ TinyFileManagera evidovaný pri vzniku položky. Tieto údaje nie sú totožné a samy osebe nenahrádzajú kontrolu oprávnení.

Ak je evidovaný aplikačný tvorca, zobrazuje sa jeho odznak. Inak sa položka označuje ako systémová. Pri úprave pôvodne systémového súboru môže zostať vlastník **System** a pribudnúť odznak posledného editora. Tooltip dopĺňa podrobnosti. Filtre **Všetko / App / System** a počítadlá slúžia na orientáciu v pôvode položiek.

## Ukladanie metadát

Aktuálny backend používa `owner-meta.sqlite` v adresári prevádzkového stavu z funkcie `fm_runtime_state_dir()`. Cestu ovplyvňuje `$state_storage_path`; bez vlastnej cesty sa používa `.fm_usercfg`. Starší `owner-meta.json` slúži aj ako migračný zdroj. Nie je správne predpokladať, že všetky nové záznamy sa zapisujú iba do JSON.

Záznamy sú oddelené podľa koreňa pracovného priestoru. Obsahujú `created_by`, `created_at`, `updated_by`, `updated_at` a `last_action`. Bežné uploady, vytvorenie, editácia, kopírovanie, presun, premenovanie a mazanie majú obsluhu metadát v aplikácii. Zásah priamo na hostingu nemá automaticky identitu aplikačného používateľa; indexovanie ju nedokáže spätne určiť. Vývojový AI Browser nemožno považovať za rovnocenný auditovaný editor.

## Komu možno písať

Chat pracuje s internými účtami a zoznam príjemcov filtruje podľa pravidiel aplikácie. Priamo priradený manažér sa pridáva aj pri odlišných adresároch a je označený **(manažér)**. Systémový vlastník bez zodpovedajúceho aplikačného účtu nie je príjemcom chatu. Odznak vlastného účtu alebo nedostupného príjemcu nemusí byť klikateľný.

## História a neprečítané správy

Správy sú v `chat.sqlite` v tom istom prevádzkovom adresári. Prežijú odhlásenie. Server eviduje stav čítania v tabuľke `fm_chat_read_state`; rozhranie používa aj lokálny stav `tfm-chat-read:<user>` v `localStorage`. Preto opis „stav čítania je iba v prehliadači“ už nie je úplný.

Zobrazenie online je orientačné. Správa môže byť odoslaná aj offline príjemcovi. Indikácia neprečítaných správ upozorňuje na konverzácie, ktoré treba otvoriť.

## Premenovanie účtu a aktualizácia aplikácie

Správa používateľov podporuje premenovanie účtu a prenáša súvisiace nastavenia a odkazy vrátane chatovej histórie. Úprava mena ručne iba na jednom mieste nie je ekvivalent tohto postupu.

Pri nasadení zachovajte prevádzkový adresár, serverový `config.php` a integračné konfigurácie. Databázu chatu ani vlastníctva nemažte ako „cache“. Index `search-index.sqlite` má iný účel: je odvodený od disku; chat a metadáta obsahujú informácie, ktoré samotný disk neobnoví.

[Používateľská príručka](?help_doc=user-guide) · [Konfigurácia](?help_doc=wiki-config-flags)
