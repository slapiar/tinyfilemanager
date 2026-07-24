1. UX: Vylepšenie správy súborov a navigácie
•	Drag-and-Drop kdekoľvek na ploche:
o	Súčasný stav: Nahrávanie súborov často vyžaduje kliknutie na tlačidlo nahrávania alebo otvorenie modal okna.
o	Odporúčanie: Umožni používateľovi pustiť súbory priamo do mriežky/tabuľky s viditeľným „Dropzone“ efektom (napr. jemné stmavnutie/zomodrenie celej plochy s nápisom „Pusťte súbory sem pre nahrávanie“).
•	„Context Menu“ (Pravé kliknutie):
o	Súborové manažéry si žiadajú natívny pocit desktopu. Pravý klik na riadok/súbor by mal ihneď otvoriť kontextové menu s akciami (Stiahnuť, Premenovať, Upraviť v Joyee, Vymazať, Zdieľať).
•	Breadcrumbs (Drobienková navigácia) ako adresný riadok:
o	Zabezpeč, aby sa do jednotlivých častí cesty (/var/www/html/files/docs) dalo kliknúť priamo a tiež aby bolo možné cestu skopírovať alebo manuálne upraviť.
•	Klávesové skratky (Hotkeys):
o	Pridaj podporu pre základné skratky: F2 (Premenovanie), Del / Backspace (Vymazanie do koša/zmazanie), Ctrl+A (Označiť všetko), Esc (Zatvorenie modalu/náhľadu).
2. UI: Moderný vizuálny štýl (Look & Feel)
•	Viac „dýchania“ (Whitespace) a paddingu:
o	Staré súborové manažéry natlačia na obrazovku 50 riadkov naraz s malým fontom. Moderné UI (ako Google Drive, Nextcloud, Apple Finder) využíva väčší padding v riadkoch tabuľky (aspoň 12px až 16px na výšku) a fonty vo veľkosti 14px–15px pre lepšiu čitateľnosť.
•	Prepínač zobrazení (Grid vs. List):
o	List View (Tabuľka): Ideálne pre detailnú prácu s veľa súbormi (veľkosť, dátum, práva).
o	Grid View (Kachličky/Mriežka): Ideálne pre médiá (obrázky, videá, PDF), kde je vidieť veľký náhľad (thumbnail).
•	Farby a vizuálna hierarchia:
o	Použi neutrálne, svetlosivé/tmavosivé pozadie s jedným dominantným akcentom (napr. príjemná modrá alebo fialová pre primárne tlačidlá a vybrané položky).
o	Menej dôležité ikonky (stiahnuť, premenovať) by mali mať jemnejšiu farbu (muted gray) a zvýrazniť sa až pri nabehnutí myšou (hover).
3. Náhľady a práca s obsahom (Rich Previews)
•	Rýchly náhľad (Quick Look / Spacebar):
o	Po stlačení medzerníka na označenom súbore otvor overlay window (modal) s rýchlym náhľadom bez nutnosti prechádzať na novú stranu:
	Obrázky / Videá / Audio: Integrovaný prehrávač/prehliadač.
	PDF: Priamy PDF.js náhľad.
	Kód / Text: Syntax highlighter (napr. Prism.js alebo Monaco Editor).
	Dokumenty: Integrácia s rozšírením Joyee, ak ho máš na to určené.
4. Responsivita a mobilné UX
TinyFileManager sa na mobiloch často používa ťažko, pretože tabuľky s mnohými stĺpcami sa nezmestia na obrazovku:
•	Mobilné zobrazenie (Card Layout): Na mobilných zariadeniach skry stĺpce ako Oprávnenia (CHMOD), Vlastník či Dátum a premeň riadky tabuľky na samostatné karty (cards) s veľkými dotykovými plochami (touch targets min. 44x44px).
•	Spodná lišta akcií (Bottom Sheet): Namiesto množstva malých ikoniek pri súbore otvor po kliknutí na tútok na mobile spodné menu s veľkými tlačidlami pre akcie.
5. Mikromomenty a spätná väzba (Feedback)
•	Stav nahrávania súborov (Progress bar):
o	Pripni malý widget nahrávania do pravého dolného rohu (ako má Google Drive). Používateľ by mal vidieť priebeh (%), rýchlosť a možnosť nahrávanie zrušiť, pričom môže ďalej prehliadať adresáre.
•	Toast notifikácie:
o	Namiesto klasických PHP alert() alebo prerušujúcich správ použi elegantné plávajúce oznámenia ("Súbor bol úspešne premenovaný", "Odkaz bol skopírovaný do schránky").
💡 Rýchle "Low-hanging fruit" vylepšenia (Čím začať hneď):
1.	Vymeň ikony súborov za moderné SVG ikony: (Napr. Lucide Icons alebo Tabler Icons – napr. odlíš farebne ikony pre .pdf, .zip, .php, .png).
2.	Pridaj vyhľadávaciu lištu (Search/Filter): Rýchle lokálne filtrovanie zobrazených súborov v reálnom čase pomocou JS bez reloadu stránky.
3.	Tmavý režim (Dark Mode): Dnes je to pre mnohých vývojárov a správcov štandard. Stačí pridať CSS premenné (var(--bg-primary), var(--text-color)) a prepínač v hlavičke.

🗺 Plán implementácie (Roadmap)
 Fáza 1: Základný vizuál a príprava (UI Quick-Wins)
Cieľ: Dodať aplikácii moderný nádych bez zásahu do hlbokej logiky backendu.
•	1.1 Zavedenie CSS premenných (Design Tokens):
o	Definuj si štandardnú paletu farieb, paddingy, fonty a zaoblenia (border-radius) v hlavnom CSS súbore.
o	Priprav podporu pre Dark Mode (prepínanie CSS triedy .dark-theme na <body>).
•	1.2 Výmena ikon za moderné SVG:
o	Nahraď staré ikonky knižnicou Lucide Icons alebo Tabler Icons (sú ľahké, čisté a škálovateľné).
o	Pridaj farebné odlíšenie pre typy súborov (napr. .pdf = červená, .zip = oranžová, .php = fialová).
•	1.3 Zväčšenie priestoru (Whitespace) a typography:
o	Zvýš padding v tabuľke (min. 12px 16px) pre lepší komfort pri klikaní.
o	Zmeň systémový font na moderný bezpätkový stack (napr. Inter, System UI).
•	1.4 Pridanie Toast notifikácií:
o	Nahraď klasické JS/PHP alerty ľahkou knižnicou (napr. Toastify.js alebo vlastný jednoduchý toast skript).
📍 Fáza 2: UX interakcie a správa súborov (Desktop Experience)
Cieľ: Spraviť prácu so súbormi plynulou, rýchlou a intuitívnou.
•	2.1 Drag-and-Drop Dropzone:
o	Pridaj overlay na celú plochu pri pretiahnutí súboru do okna prehliadača.
•	2.2 Rýchle filtrovanie (Search-as-you-type):
o	Pridaj input v hlavičke, ktorý okamžite skrýva/zobrazuje riadky tabuľky podľa vpísaného textu (čisto cez JavaScript).
•	2.3 Kontextové menu (Right-Click):
o	Odchytávaj udalosť contextmenu na riadku súboru a zobraz vlastné plávajúce menu s akciami (Stiahnuť, Premenovať, Zmazať, Otvoriť v Joyee).
•	2.4 Klávesové skratky:
o	Naviaž globálne keydown udalosti: F2 (Premenovanie), Del (Vymazanie), Space (Náhľad).
📍 Fáza 3: Prepínač zobrazenia & Rýchle náhľady (Rich Features)
Cieľ: Prispôsobiť zobrazenie rôznym typom obsahu (dokumenty vs. médiá).
•	3.1 Prepínač Tabuľka (List) vs. Mriežka (Grid):
o	Pridaj prepínač v lište. Pridaj CSS štýly pre .view-grid (flex/grid kachličky s veľkými ikonami/náhľadmi).
•	3.2 Rýchly náhľad (Quick Look / Modal):
o	Vytvor univerzálny modal pre náhľady:
	Obrázky/Médiá: HTML5 <video>, <img> s lazymodedom.
	PDF: Zapojenie PDF.js vieweru.
	Kód: Integruj ľahký zväčšovač kódu (napr. Prism.js alebo Monaco Editor pre úpravy).
📍 Fáza 4: Mobilná optimalizácia a finálne ladenie
Cieľ: Urobiť web plne použiteľný aj na smartfónoch a tabletoch.
•	4.1 Responzívny Card Layout pre mobily:
o	Použi CSS @media (max-width: 768px) na skrytie podrobných stĺpcov tabuľky (Práva, Vlastník) a zmenu riadkov na samostatné "karty".
•	4.2 Bottom Sheet Menu pre mobily:
o	Na mobiloch pri ťuknutí na súbor nevysúvaj kontextové menu, ale vytiahni menu zo spodku obrazovky (ideálne pre ovládanie palcom).
•	4.3 Testovanie a ladenie výkonu:
o	Skontroluj, ako sa rozhranie správa pri adresároch s 1000+ súbormi (paginácia/virtual scroll v prípade potreby).

