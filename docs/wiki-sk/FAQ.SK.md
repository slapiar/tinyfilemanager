# Časté otázky

## Prečo nevidím rovnaké možnosti ako kolega?

Rozhoduje rola, pridelené adresáre a povolenie hromadných akcií. Manažér nemá mazanie. AI Browser je počas vývoja iba pre administrátora.

## Čo robí Ultra?

Zhustí riadky zoznamu. **Normal** obnoví pôvodné nastavenie stránky. Voľba sa neukladá po obnovení. Ak po aktualizácii stále vidíte staré správanie, obnovte aj načítané štýly a skripty cez Ctrl + F5.

## Prečo kamera na notebooku otvorí súbory?

Tlačidlo používa výber obrázka s požiadavkou na kameru. Jej spracovanie závisí od zariadenia a prehliadača. Mobil môže otvoriť kameru, počítač výber obrázka. Fotografia sa nahráva do práve otvoreného priečinka.

## Prečo upload zlyhal?

Hlásenie môže uvádzať neprípustnú príponu, veľkosť, neplatný token alebo nemožnosť zápisu. Okrem limitu aplikácie platia limity PHP a hostingu. Pri vypršanej relácii obnovte stránku a prihlásenie. Správcovi pošlite presné hlásenie.

## Prečo je priečinok po zásahu cez FTP iný než vo vyhľadávaní?

Zoznam číta disk, vyhľadávanie index. Index sa po uplynutí 60-sekundovej platnosti obnoví pri ďalšej požiadavke. Už otvorená stránka môže držať starý výber. Obnovte ju; nevytvárajte duplicitný priečinok len podľa starého výsledku.

## Čo znamená „Už neexistujú“ pri mazaní?

Vybrané položky už nie sú v rodičovskom priečinku, napríklad po zásahu cez hosting alebo iným používateľom. Aplikácia ich preskočila a opraví index. Hlásenie o neúspešných položkách naopak uvádza skutočné chyby. Pri čiastočnom výsledku už mohli byť ostatné položky vymazané.

## Prečo administrátor dostal Permission denied?

Rola v aplikácii neposkytuje PHP procesu ďalšie systémové oprávnenia. Treba skontrolovať vlastníctvo a právo zápisu v príslušných adresároch na hostingu. Hlásenie odlíšte od už neexistujúcej položky.

## Čo je System vo vlastníctve?

Položka nemá evidovaného aplikačného tvorcu. Nie je to označenie prihláseného admina. Posledný editor môže byť zobrazený zvlášť. [Podrobnosti](?help_doc=ownership-chat).

## Ako napíšem manažérovi?

V príjemcoch chatu vyberte priamo priradeného používateľa označeného **(manažér)**. Nemusí mať rovnaký pracovný priečinok ani byť práve online.

## Prečo AI ukazuje JSON namiesto odpovede?

AI Browser je nedokončený nástroj na návrhy súborových operácií. Prázdne `operations` znamená, že nenavrhuje zmenu; nie potvrdenie, že dokument je hotový. Neúplný `content` pri `write` by mohol nahradiť celý dokument. Funkcia preto zostáva len administrátorovi.

## Kam hlásiť problém?

Najprv správcovi tejto inštalácie. Uveďte akciu, čas, cestu, presné hlásenie a prípadne snímku. Odkaz **Nahlásiť problém** v Pomoci smeruje na repozitár nášho forku; nezverejňujte v hlásení heslá ani API kľúče.
