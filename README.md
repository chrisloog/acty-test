## Acty RESTful API

### Paigaldamine ja käivitamine:

Lae alla XAMPP:
https://www.apachefriends.org/download.html

Järgi allalaadimise juhiseid.

### Projekti kloonimine htdocs kausta:

macOS: klooni või kopeeri projekti kaust asukohta /Applications/XAMPP/htdocs

Windows: klooni või kopeeri projekti kaust asukohta C:\xampp\htdocs

### Näide (Windows):

```bash
  cd C:\xampp\htdocs
  git clone https://github.com/sinu-kasutajanimi/acty-restful-api.git
```

Käivita Apache ja MySQL serverid XAMPP rakenduses.

### Andmebaasi seadistamine: 

Ava veebibrauseris http://localhost/phpmyadmin/

Loo uus andmebaas. Näiteks nimega acty-test. Kui kasutate muud nime, siis tuleb failis db.php see ära muuta.

Loo tabelid:

```sql
CREATE TABLE organizations (
    id INT AUTO_INCREMENT PRIMARY KEY, 
    org_name VARCHAR(255) 
    NOT NULL UNIQUE 
);

CREATE TABLE organization_relationships ( 
    id INT AUTO_INCREMENT PRIMARY KEY, 
    parent_id INT NOT NULL, child_id INT NOT NULL, 
    FOREIGN KEY (parent_id) REFERENCES organizations(id) 
    ON DELETE CASCADE ON UPDATE CASCADE, FOREIGN KEY (child_id) 
    REFERENCES organizations(id) ON DELETE CASCADE ON UPDATE CASCADE 
);
```

### API päringute tegemine: 

API põhifail on api_endpoint.php, millele saab pääseda ligi aadressilt: http://localhost/acty-restful-api/api_endpoint.php

### POST-päring (andmete sisestamiseks):

URL: http://localhost/acty-restful-api/api_endpoint.php

Body (JSON), näiteks:

```json
{
  "org_name": "A",
  "daughters": [
    {
      "org_name": "B",
      "daughters": [
        { "org_name": "C" },
        { "org_name": "D" }
      ]
    }
  ]
}
```

See lisab andmebaasi vastavad organisatsioonid ja nende omavahelised  suhted.

### GET-päring (andmete küsimiseks):

URL: http://localhost/acty-restful-api/api_endpoint.php?org_name=C

See tagastab "C" organisatsiooni vahetud vanem-, õde- ja tütarorganisatsioonid.

Kui organisatsioonil on rohkem kui 100 erinevat suhet, siis saab järgmiseid suhteid vaadata näiteks järgneva GET päringuga - http://localhost/acty-restful-api/api_endpoint.php?page=2&org_name=C, kus tuleb muuta page parameetrit vastavalt vajadusele.

### Testimine

Rakendus on testitud läbi Postman'i

Etteantud näide:

```json
{
  "org_name": "Paradise Island",
  "daughters": [
    {
      "org_name": "Banana tree",
      "daughters": [
        { "org_name": "Yellow Banana" },
        { "org_name": "Brown Banana" },
        { "org_name": "Black Banana" }
      ]
    },
    {
      "org_name": "Big banana tree",
      "daughters": [
        { "org_name": "Yellow Banana" },
        { "org_name": "Brown Banana" },
        { "org_name": "Green Banana" },
        {
          "org_name": "Black Banana",
          "daughters": [
            { "org_name": "Phoneutria Spider" }
          ]
        }
      ]
    }
  ]
}
```

POST päring tagastab:

```json
{
    "message": "Data inserted successfully"
}
```

GET päring aadressile http://localhost/acty-restful-api/api_endpoint.php?org_name=Black Banana tagastab:

```json
[
    {
        "relationship_type": "parent",
        "org_name": "Banana tree"
    },
    {
        "relationship_type": "parent",
        "org_name": "Big banana tree"
    },
    {
        "relationship_type": "sister",
        "org_name": "Yellow Banana"
    },
    {
        "relationship_type": "sister",
        "org_name": "Brown Banana"
    },
    {
        "relationship_type": "sister",
        "org_name": "Green Banana"
    },
    {
        "relationship_type": "daughter",
        "org_name": "Phoneutria Spider"
    }
]
```

#### Olukord kus organisatsioon lisatakse uuesti eraldi POST päringus, aga näiteks uue vanema, õe või tütrega.

POST päring:

```json
{
  "org_name": "Tesla",
  "daughters": [
    {
      "org_name": "Apple",
      "daughters": [
        { "org_name": "Broadcom" },
        { "org_name": "Comcast" }
      ]
    }
  ]
}
```

GET päring aadressile http://localhost/acty-restful-api/api_endpoint.php?org_name=Apple tagastab:

```json
[
    {
        "relationship_type": "parent",
        "org_name": "Tesla"
    },
    {
        "relationship_type": "daughter",
        "org_name": "Broadcom"
    },
    {
        "relationship_type": "daughter",
        "org_name": "Comcast"
    }
]
```

Eraldi päringus lisasin organisatsioonile Apple ühe õe, vanema ja kaks tütart. 

POST päring:
```json
{
  "org_name": "Ford",
  "daughters": [
    {
      "org_name": "Apple",
      "daughters": [
        { "org_name": "Samsung" },
        { "org_name": "Huawei" }
      ]
    },
    {
      "org_name": "Walmart",
      "daughters": [
        { "org_name": "Fedex" }
      ]
    }
  ]
}
```

Nüüd tagastab GET päring samale aadressile:

```json
[
    {
        "relationship_type": "parent",
        "org_name": "Tesla"
    },
    {
        "relationship_type": "parent",
        "org_name": "Ford"
    },
    {
        "relationship_type": "sister",
        "org_name": "Walmart"
    },
    {
        "relationship_type": "daughter",
        "org_name": "Broadcom"
    },
    {
        "relationship_type": "daughter",
        "org_name": "Comcast"
    },
    {
        "relationship_type": "daughter",
        "org_name": "Samsung"
    },
    {
        "relationship_type": "daughter",
        "org_name": "Huawei"
    }
]
```

#### Olukord, kus lisatakse eraldi ainult üks tütarorganisatsioon.

POST päring:

```json
{
  "org_name": "Apple",
  "daughters": [
    {
      "org_name": "Nokia"
    }
  ]
}
```

Nüüd tagastab GET päring samale aadressile:

```json
[
    {
        "relationship_type": "parent",
        "org_name": "Tesla"
    },
    {
        "relationship_type": "parent",
        "org_name": "Ford"
    },
    {
        "relationship_type": "sister",
        "org_name": "Walmart"
    },
    {
        "relationship_type": "daughter",
        "org_name": "Broadcom"
    },
    {
        "relationship_type": "daughter",
        "org_name": "Comcast"
    },
    {
        "relationship_type": "daughter",
        "org_name": "Samsung"
    },
    {
        "relationship_type": "daughter",
        "org_name": "Huawei"
    },
    {
        "relationship_type": "daughter",
        "org_name": "Nokia"
    }
]
```

#### Olukord, kus organisatsioon lisatakse ilma ühegi vanema, õe või tütreta.

```json
{
  "org_name": "Toyota"
}
```

GET päring aadressile http://localhost/acty-restful-api/api_endpoint.php?org_name=Toyota tagastab:

```json
[]
```

#### Olukord, kus organisatsiooni mida päringus küsitakse ei eksisteeri.

GET päring aadressile http://localhost/acty-restful-api/api_endpoint.php?org_name=NonExistentOrg tagastab:

```json
{
  "message": "Organization not found"
}
```

#### Olukord, kus proovitakse lisada organisatsioonile tütreid, ilma nime täpsustamata.

POST päring:

```json
{
  "daughters": [
    {
      "org_name": "Nokia"
    }
  ]
}
```

Vastus:

```json
{
    "message": "Missing organization name"
}
```
