Abakaffe-YO
===========

Abakaffe-YO Sender et YO hver gang kaffetrakteren på Abakuskontoret skrus på.

### Steg: 
* Last ned YO fra AppStore eller Google Play 
* Legg til ABAKAFFE! 

### Filer:
* Abakaffe.php kjøres hvert minutt og sjekker om det er nytraktet kaffe.
* Receiver.php tar imot YO som blir sendt til ABAKAFFE. Forespørslene registreres i en MYSQL database.
* Statistics.php henter ut all informasjon fra databasen og viser det til brukeren ( http://yo.founder.no/abakaffe/ )
