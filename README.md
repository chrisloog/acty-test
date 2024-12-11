## Acty RESTful API

### Nõuded:

PHP 7.4+ (töötab ka XAMPP-iga, mis sisaldab Apache ja MySQL)

MySQL andmebaas

Postman või mõni muu HTTP-päringute tegemise tööriist (nt cURL, Insomnia)

### Paigaldamine ja käivitamine:

Lae alla XAMPP:
https://www.apachefriends.org/download.html

Järgi allalaadimise ajal esitatavaid juhiseid.

### Projekti kloonimine htdocs kausta:

macOS: klooni või kopeeri projekti kaust asukohta /Applications/XAMPP/htdocs

Windows: klooni või kopeeri projekti kaust asukohta C:\xampp\htdocs

### Näide (Windows):

```bash
  cd C:\xampp\htdocs
  git clone https://github.com/sinu-kasutajanimi/acty-restful-api.git
```

Käivita XAMPP-is Apache ja MySQL serverid.

### Andmebaasi seadistamine: 

Ava veebibrauseris http://localhost/phpmyadmin/

Loo uus andmebaas, näiteks nimega acty-test.

Loo tabelid:

CREATE TABLE organizations (
    id INT AUTO_INCREMENT PRIMARY KEY, 
    org_name VARCHAR(255) 
    NOT NULL UNIQUE );

CREATE TABLE organization_relationships ( 
    id INT AUTO_INCREMENT PRIMARY KEY, 
    parent_id INT NOT NULL, child_id INT NOT NULL, 
    FOREIGN KEY (parent_id) REFERENCES organizations(id) 
    ON DELETE CASCADE ON UPDATE CASCADE, FOREIGN KEY (child_id) 
    REFERENCES organizations(id) ON DELETE CASCADE ON UPDATE CASCADE );

### API päringute tegemine: 

API põhifail on api_endpoint.php, millele saab pääseda ligi aadressilt: http://localhost/acty-restful-api/api_endpoint.php

### POST-päring (andmete sisestamiseks):

URL: http://localhost/acty-restful-api/api_endpoint.php

Body (JSON), näiteks:

{ "org_name": "Paradise Island", "daughters": [ { "org_name": "Banana tree", "daughters": [ { "org_name": "Yellow Banana" }, { "org_name": "Brown Banana" }, { "org_name": "Black Banana" } ] } ] }

See lisab andmebaasi vastavad organisatsioonid ja nende omavahelised  suhted.

### GET-päring (andmete küsimiseks):

URL: http://localhost/acty-restful-api/api_endpoint.php?org_name=Black Banana

See tagastab "Black Banana" organisatsiooni vahetud vanem-, õde- ja tütarorganisatsioonid.

### Testimine

Rakendus on testitud läbi Postman'i