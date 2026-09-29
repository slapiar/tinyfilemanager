# Používateľská príručka

Aktualizované 29. 9. 2026 pre vydanie 3.3.11. Návod opisuje náš upravený TinyFileManager.

## Prihlásenie a váš pracovný priestor

Prihláste sa údajmi od správcu. Po prihlásení sa otvorí váš predvolený pridelený priečinok. Dostupné adresáre a akcie závisia od roly a nastavenia účtu. Názov klient alebo dodávateľ sám neurčuje oprávnenia; rozhoduje pridelený prístup.

Administrátor má prístup k celému nakonfigurovanému pracovnému priestoru. Manažér spravuje pridelenú oblasť a svojich používateľov, ale nemá mazanie. Účet iba na čítanie nemôže meniť súbory. Účet iba na nahrávanie slúži na odovzdávanie súborov. Štandardný účet pracuje podľa pridelených oprávnení.

## Orientácia v okne

Horná lišta obsahuje vyhľadávanie, nahrávanie, tlačidlo **+** a menu s vaším menom. Cesta nad zoznamom ukazuje aktuálny priečinok. Kliknutím na priečinok alebo položku v strome prejdete do jeho obsahu. Odkaz na rodičovský priečinok sa ponúka len tam, kde máte prístup.

Zoznam ukazuje názov, veľkosť a ďalšie dostupné údaje. Pri jednotlivých položkách nájdete povolené akcie. Označovacie políčka slúžia na výber viacerých položiek. Na mobile sú hromadné akcie dostupné cez ovládač **Hromadné akcie**.

## Vzhľad a tlačidlo Ultra

Predvolené zobrazenie je tmavé a kompaktné. Staršie profily dostali tento predvolený vzhľad jednorazovo; neskôr uložená osobná voľba zostáva zachovaná. V menu používateľa otvorte **Nastavenia**, vyberte vzhľad a uložte ho.

**Ultra** ešte viac zhustí riadky zoznamu: zmenší medzery, písmo a ikony. Po zapnutí sa tlačidlo zmení na **Normal**. Tým sa vrátite k svojmu nastavenému zobrazeniu, ktoré môže byť aj kompaktné. Ultra je dočasné prepnutie tejto stránky; po obnovení sa neuchováva. Neovplyvňuje obsah súborov.

## Nahratie súborov a fotografia

1. Otvorte priečinok, do ktorého chcete súbory uložiť.
2. Použite ikonu nahrávania v hornej lište alebo **+ → Nahrať zo zariadenia**.
3. Vyberte súbory a počkajte na dokončenie prenosu.

V okne **+** je aj tlačidlo **Odfotiť a nahrať** s ikonou kamery. Na podporovanom mobile otvorí fotografovanie; na počítači alebo v inom prehliadači môže otvoriť výber obrázka. Vybraná fotografia sa automaticky nahrá do aktuálneho priečinka pod vytvoreným názvom začínajúcim `foto-`. Zrušením výberu sa nič neodošle. Pri chybe zostane hlásenie v okne.

Tlačidlo **+** naďalej umožňuje vytvoriť nový súbor alebo priečinok. Dostupnosť zápisu, povolené prípony a limity prenosu určuje účet a server.

## Práca so súbormi

Podľa dostupných akcií môžete súbor otvoriť, stiahnuť, premenovať, skopírovať alebo presunúť. Textové súbory možno upraviť v editore a uložiť. Podpora náhľadu závisí od formátu. Kopírovanie ponechá pôvodnú položku, presun zmení jej umiestnenie.

## Hromadné akcie a mazanie

Označte požadované položky políčkami a použite lištu hromadných akcií dole. K dispozícii môžu byť výber všetkého, zrušenie alebo obrátenie výberu, stiahnutie ZIP, archív, kopírovanie, presun a mazanie. Konkrétne možnosti závisia od oprávnení. Mazanie potvrďte v zobrazenom dialógu.

Výsledok môže byť čiastočný: niektoré položky sa vymažú a iné zostanú. Chybové hlásenie uvedie počty, názvy neúspešných položiek a dôvody zo servera. Už chýbajúca položka sa uvedie ako **Už neexistuje** a zoznam sa obnoví. Neznamená to, že ju aplikácia práve vymazala. Ak server nedokáže overiť jej existenciu, nejde automaticky o chýbajúcu položku.

Mazanie v tomto postupe nie je presun do koša a neponúka automatické vrátenie. Administrátorská rola v aplikácii nemení systémové práva webhostingu.

## Vyhľadávanie a aktuálnosť zoznamu

Zoznam aktuálneho priečinka sa načítava z disku. Vyhľadávanie používa index kvôli rýchlosti. Zmeny cez aplikáciu aktualizujú index alebo ho označia na obnovu aj pre ostatných používateľov.

Zmeny cez FTP či správcu hostingu uvidíte v zozname po jeho načítaní. Vyhľadávací index má platnosť 60 sekúnd; po jej uplynutí ho obnoví najbližšia požiadavka. Bez aktivity nebeží samostatná obnova na pozadí. Už neexistujúce súbory sa z výsledkov vyhľadávania filtrujú. Otvorená stránka so starým výberom sa môže líšiť od aktuálneho disku; vtedy ju obnovte.

## Vlastník a komunikácia

Stĺpec **Vlastník** rozlišuje používateľa evidovaného aplikáciou a systémový pôvod položky. Označenie **System** neznamená automaticky administrátora. Samostatný odznak môže ukazovať posledného editora. Filtre **App / System** menia zobrazenie, nie oprávnenia.

Chat otvoríte cez dostupný odznak používateľa alebo komunikačný panel. V zozname príjemcov je dostupný aj váš priamo priradený manažér, označený **(manažér)**, aj keď pracuje v inom priečinku. Výber je obmedzený pravidlami komunikácie; nezobrazuje automaticky všetky účty.

Správy zostávajú uložené aj po odhlásení a možno ich poslať aj neprítomnému používateľovi. Označenie online je orientačný údaj o aktivite, nie potvrdenie prečítania. Podrobnosti: [Vlastníctvo a chat](?help_doc=ownership-chat).

## Profil, heslo a Pomoc

V menu pri svojom mene nájdete **Môj profil / Zmena hesla**, **Nastavenia**, **Pomoc** a odhlásenie. Meno účtu mení oprávnený správca v správe používateľov. Minimálna dĺžka mena sú dva znaky podľa validácie formulára.

V Pomoci je táto príručka aj [slovenská wiki](?help_doc=wiki-index). Pri probléme oznámte správcovi presné hlásenie, vykonanú akciu, priečinok a čas; vhodná je snímka obrazovky bez hesiel alebo API kľúčov.

## AI Browser

Táto rozpracovaná funkcia je dostupná iba administrátorovi. Bežní používatelia ani manažéri ju v menu nevidia. Zatiaľ nie je určená na bežné upravovanie dokumentov: môže vrátiť neúplný alebo nesprávny návrh. Podrobnosti sú v [prehľade rozšírení](?help_doc=wiki-our-extensions).
