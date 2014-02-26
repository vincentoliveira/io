# language: fr
@commandes
Fonctionnalité: Commande en cours

Contexte:
    Soit le restaurant "RestoTest" existe avec l'url "/tests/import.json"
    Soit l'utilisateur "cuisto" existe et a le role "ROLE_CUISINIER"
    Soit l'utilisateur "cuisto" a pour restaurant "RestoTest"

    Soit le restaurant "RestoTest2" existe avec l'url "/tests/import.json"
    Soit l'utilisateur "cuisto2" existe et a le role "ROLE_CUISINIER"
    Soit l'utilisateur "cuisto2" a pour restaurant "RestoTest2"
    Soit il n'y a aucune commande


@11.1
Scénario: 11.1 - Commande en cours : Authorisations
    Soit je suis sur "/encours"
    Alors je ne devrais pas voir "Commande en cours"

    Soit je suis connecté en tant que "cuisto"
    Soit je suis sur "/encours"
    Alors je devrais voir "Aucune commande en cours"

@11.2
Scénario: 11.2 - Commande en cours
    Soit il y a une commande en cours pour "RestoTest"

    Soit je suis connecté en tant que "cuisto"
    Soit je suis sur "/encours"
    Alors je ne devrais pas voir "Aucune commande en cours"
    Et je devrais voir "En attente"

    Soit je suis connecté en tant que "cuisto2"
    Soit je suis sur "/encours"
    Alors je devrais voir "Aucune commande en cours"
