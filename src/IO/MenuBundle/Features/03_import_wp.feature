# language: fr
@import
Fonctionnalité: Import wordpress


@3.1
Scénario: 03.1 - Import Wordpress : Authorisations
    Soit je suis sur "/import/"
    Alors je ne devrais pas voir "Importer"

    Soit l'utilisateur "cuisto" existe et a le role "ROLE_CUISINIER"
    Soit je suis connecté en tant que "cuisto"
    Soit je suis sur "/import/"
    Alors je ne devrais pas voir "Importer"

    Soit l'utilisateur "resto" existe et a le role "ROLE_RESTAURATEUR"
    Soit je suis connecté en tant que "resto"
    Soit je suis sur "/import/"
    Alors je ne devrais pas voir "Importer"

    Soit l'utilisateur "admin" existe et a le role "ROLE_ADMIN"
    Soit je suis connecté en tant que "admin"
    Soit je suis sur "/import/"
    Alors je devrais voir "Importer"


@3.2
Scénario: 03.2 - Import : Echec
    Soit l'utilisateur "admin" existe et a le role "ROLE_ADMIN"
    Et je suis connecté en tant que "admin"
    Et je suis sur "/import/"    
    Soit je remplis le champ caché "import[_token]" avec "-"
    Et je presse "Importer"
    Alors je devrais voir "Ce restaurant n'existe pas"

    Et le restaurant "Restaurant test" existe avec l'url "/tests/aucun_fichier.json"
    Soit je suis sur "/import/"
    Soit je sélectionne "Restaurant test" depuis "import[restaurant]"
    Et je presse "Importer"
    Alors je devrais voir "Echec de la récupération des données"

    Soit le restaurant "Restaurant test" existe avec l'url "/tests/import_ko.json"
    Soit je suis sur "/import/"
    Soit je sélectionne "Restaurant test" depuis "import[restaurant]"
    Et je presse "Importer"
    Alors je devrais voir "Echec de la récupération des données"


@3.3
Scénario: 03.3 - Import Wordpress
    Soit le restaurant "Restaurant test" existe avec l'url "/tests/import.json"
    Soit je supprime tous les "IOMenuBundle:Category"
    Soit l'utilisateur "admin" existe et a le role "ROLE_ADMIN"
    Soit je suis connecté en tant que "admin"
    Soit je suis sur "/import/"

    Soit je sélectionne "Restaurant test" depuis "import[restaurant]"
    Et je presse "Importer"
    Alors je devrais voir "L'import s'est exécuté avec succès"
