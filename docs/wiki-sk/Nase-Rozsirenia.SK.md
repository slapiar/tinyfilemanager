# Naše rozšírenia a ich stav

Stav vydania 3.3.11, 29. 9. 2026.

## Dostupné funkcie

- Roly, pridelené adresáre, správa a premenovanie účtov, minimálne dvojznakové meno.
- Predvolené tmavé kompaktné zobrazenie s osobným nastavením a dočasným režimom Ultra.
- Kamera a nahrávanie zo zariadenia v okne **+**, zachované samostatné nahrávanie v hlavičke.
- Interný chat, história, neprečítané konverzácie a priamo priradený manažér vo výbere príjemcov.
- Aplikačné vlastníctvo a posledný editor, filtre App/System.
- Lokálna Pomoc a slovenská wiki.
- Živé načítanie priečinkov, SQLite vyhľadávanie a obnova expirovaného indexu.
- Hromadné mazanie s rozlíšením úspechu, chyby a už chýbajúcich položiek.

## Index a zmeny mimo aplikácie

Priečinky sa načítavajú z disku. Rozdiel oproti indexu spustí jeho označenie na obnovu. Index vyhľadávania má platnosť 60 sekúnd od dokončenia obnovy; ďalšia požiadavka po expirácii ho znovu vytvorí pod zámkom. Obnova zvýši revíziu stromu, ktorú otvorené rozhranie pravidelne kontroluje. Nie je to samostatný proces sledujúci disk bez požiadaviek. Pri veľkom úložisku môže obnova zvýšiť čas odozvy.

Výsledky hľadania overujú existenciu súborov. Úplné rekurzívne indexovanie nenasleduje symbolické odkazy na adresáre. Zachytávanie externých zmien neznamená automatické doplnenie autora do aplikačného vlastníctva.

## Diagnostické logovanie

V nastaveniach profilu zostáva prepínač logovania fallbackov na ladenie. Pri úspešnom uložení sa odstraňuje stará záloha nastavení z relácie, ktorá mohla prepisovať uloženú hodnotu. Čistenie logu a reset prevádzkového stavu kontrolujú administrátorskú rolu aj po premenovaní admina. Logovanie samo chybu neopravuje a nie je zárukou záznamu každej chyby súborového systému.

## AI Browser — rozpracované, iba administrátor

Od 3.3.6 je položka skrytá ostatným rolám a priamy vstup je blokovaný. Serverové nastavenie je v `api.config.php`. Spojenie používa API kľúč, model a základnú URL; diagnostika rozlišuje napríklad chybu overenia, kvótu a nedostupný model.

Funkcia zatiaľ vyžaduje vybrané súbory a vracia plán operácií, nie spoľahlivý konverzačný návod. Zobrazuje aj JSON. Operácia `write` znamená náhradu celého obsahu cieľového súboru hodnotou `content`. Zhrnutie opráv nie je opravený dokument. Kvalita a úplnosť AI návrhu nie sú automaticky zaručené a aktuálne rozhranie nemá plnohodnotné porovnanie zmien pred uložením.

**Použiť zmeny** vykonáva návrh. Režim povolenia pre reláciu vypína jednotlivé potvrdenia, nie potrebu samostatne spustiť aplikovanie. Administrátor nemá interný limit počtu, prípon a skracovania textov; technické limity servera a poskytovateľa zostávajú. Binárne formáty nie sú týmto textovým postupom spracúvané ako dokumenty alebo obrázky.

## API, vydania a overovanie

Samostatné tokenové API a bridge sú oddelené od používateľského AI Browsera. Ich nastavenia sú v príslušných lokálnych konfiguráciách; skrytie AI položky nemení existujúce tokeny API.

`release.sh` vytvára archív, vykonáva PHP lint a voliteľne commit/push. Nie je to automatické produkčné nasadenie. Regresie v `tests/regression/admin-permissions.py` a `settings-client.cjs` overujú vybrané toky; AI odpovede sa v HTTP testoch simulujú. Úspešný test preto nepotvrdzuje kvalitu odpovedí živého modelu ani funkčnosť každého hostingu.
