Abakaffe-YO
===========

Abakaffe-YO sender deg et YO når kaffen er ferdig på Abakuskontoret.

### Steg: 
* Last ned YO fra AppStore eller Google Play 
* Legg til ABAKAFFE i applikasjonen.
* Send YO til ABAKAFFE når du er tørst. 
* Sett deg tilbake og les VG til du mottar et YO fra ABAKAFFE som signaliserer at kaffen er ferdig.

### Filer:
* Abakaffe.php kjøres hvert minutt og sjekker om det er nytraktet kaffe.
* Receiver.php tar imot YO som blir sendt til ABAKAFFE. Forespørslene registreres i en MYSQL database.
* Statistics.php henter ut all informasjon fra databasen og viser det til brukeren ( http://yo.founder.no/abakaffe/ )
