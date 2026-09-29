# Docker v tomto repozitári

Toto je technická kapitola pre správcu, stav 3.3.11. Nepoužívajte image pôvodného projektu ako záruku prítomnosti našich rozšírení.

Repozitár obsahuje `Dockerfile` založený na PHP 8.3 CLI a `docker-compose.yml`. Compose mapuje port 8080, adresáre `data`, `uploads` a `config.php`. Kontajner používa neprivilegovaného používateľa.

## Aktuálne obmedzenia konfigurácie

Dockerfile nekopíruje adresár `docs`, takže bez jeho doplnenia do image alebo pripojenia ako volume nie je lokálna Pomoc kompletná. Rovnako nekopíruje všetky samostatné API/bridge súbory. Overte dostupnosť SQLite3 a ďalších potrebných PHP rozšírení vo výslednom image; Dockerfile explicitne inštaluje ZIP. Táto dokumentačná aktualizácia Docker nasadenie neopravuje ani nepotvrdzuje jeho produkčné overenie.

Pred použitím pripravte vlastnú konfiguráciu pracovného koreňa a trvalého stavu a overte úplnosť balíka. Základné príkazy pre testovaciu kópiu repozitára:

```sh
docker compose build
docker compose up -d
```

Aplikácia je potom dostupná na nakonfigurovanom porte 8080. Samotná odpoveď HTTP na hlavnej stránke nepotvrdzuje funkčnosť chatu, indexu, API ani Pomoci.

[Požiadavky a nasadenie](?help_doc=wiki-get-started)
