# Začíname a nasadenie

Aktualizované pre 3.3.11. Táto kapitola je určená správcovi webhostingu.

## Požiadavky tohto forku

`composer.json` uvádza PHP 8.0 alebo vyššie; aktuálne regresné overenie prebehlo na PHP 8.3. Starý údaj PHP 5.5 sa na tento fork nevzťahuje. Prevádzka používa okrem hlavného PHP súboru aj adresár `src`, preklady, štýly a dokumentáciu.

Pre index, chat a ukladanie nastavení je potrebné SQLite3. Prenosy a práca s textom používajú aj Fileinfo, mbstring a iconv; ZIP operácie vyžadujú podporu ZIP. Dostupnosť konkrétnych funkcií závisí od rozšírení PHP na serveri.

## Aktualizácia na webhostingu

1. Zálohujte aplikáciu, používateľské dáta a prevádzkový stav.
2. Nahrajte súbory pripraveného vydania. Samotný `tinyfilemanager.php` nestačí na prvú inštaláciu.
3. Zachovajte vlastný `config.php`, `api.config.php`, prípadnú bridge konfiguráciu a prevádzkové databázy. Repozitárový `config.php` nie je náhradou produkčnej konfigurácie.
4. Nahrajte aj celý aktuálny adresár `docs`, inak Pomoc zobrazí staré návody alebo chybu načítania.
5. Overte prihlásenie a bežné operácie na testovacích dátach s účtami rôznych rolí.

`release.sh` vytvára ZIP z verzovaných súborov; sám neaktualizuje produkčný server. Prepínače `--auto-commit` a `--auto-push` sú voliteľné. Vydanie v `RELEASE_VERSION` môže mať iné označenie než základná konštanta `VERSION` zobrazovaná aplikáciou.

## Trvalé dáta

Nastavte vhodnú perzistentnú cestu `$state_storage_path`. Bez vlastného nastavenia sa používa `.fm_usercfg`. Zachovávajte najmä konfiguráciu, chat, vlastníctvo a audit; nejde len o dočasnú cache. Technické pokyny k migrácii sú aj v koreňovom `DEPLOYMENT.md` a skripte `scripts/migrate-legacy-state.php`.

## Prevádzkové overenie

Zmeny najprv overte na oddelenej kópii. Lokálne regresné testy nenahrádzajú kontrolu oprávnení a konfigurácie konkrétneho hostingu. AI Browser zostáva dostupný iba administrátorovi a nie je súčasťou bežného pracovného postupu klientov.
