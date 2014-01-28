# language: fr
@carte
Fonctionnalité: Voir la carte

Contexte:
    Soit le restaurant "Restaurant test" existe avec l'url "/tests/import.json"
    

@4.1
Scénario: 04.1 - Voir la carte : Authorisations
    Soit je suis sur "/carte"
    Alors je ne devrais pas voir "La carte"

    Soit l'utilisateur "cuisto" existe et a le role "ROLE_CUISINIER"
    Soit "cuisto" a pour restaurant "Restaurant test"
    Soit je suis connecté en tant que "cuisto"
    Soit je suis sur "/carte"
    Alors je ne devrais pas voir "La carte"

    Soit l'utilisateur "resto" existe et a le role "ROLE_RESTAURATEUR"
    Soit "cuisto" a pour restaurant "Restaurant test"
    Soit je suis connecté en tant que "resto"
    Soit je suis sur "/carte"
    Alors je devrais voir "La carte"

    Soit l'utilisateur "admin" existe et a le role "ROLE_ADMIN"
    Soit "cuisto" a pour restaurant "Restaurant test"
    Soit je suis connecté en tant que "admin"
    Soit je suis sur "/carte"
    Alors je devrais voir "La carte"

