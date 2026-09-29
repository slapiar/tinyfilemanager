# Účty a databázové úložisko

Aktuálny fork pracuje s účtami aplikácie a vrstvou `ConfigStore`, ktorá používa SQLite aj migračné postupy zo starších konfigurácií. Neznamená to, že možno bez úprav pripojiť ľubovoľnú externú databázu používateľov.

Pre bežné založenie, zmenu hesla alebo premenovanie účtu použite [správu používateľov](?help_doc=wiki-security-users). Externé prihlasovanie alebo SSO vyžaduje samostatnú integráciu a overenie oprávnení, relácií a súvisiacich údajov.

Táto kapitola nahrádza starý odkaz na samostatný balík `tfm-db.zip`, ktorého kompatibilita s týmto forkom nebola overená. Neinštalujte ho ako aktualizáciu aktuálneho správcu.
