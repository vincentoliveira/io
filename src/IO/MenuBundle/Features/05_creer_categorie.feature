# language: fr
@categorie
Fonctionnalité: Créer et modifier des categories

Contexte:
    Soit le restaurant "Restaurant test" existe avec l'url "/tests/import.json"
    Soit je supprime tous les "IOMenuBundle:Category"

@5.1
Scénario: 05.1 -  Créer une categories : Authorisations
    Soit je suis sur "/category/new"
    Alors je ne devrais pas voir "Ajouter une categorie"

    Soit l'utilisateur "cuisto" existe et a le role "ROLE_CUISINIER"
    Et l'utilisateur "cuisto" a pour restaurant "Restaurant test"
    Et je suis connecté en tant que "cuisto"
    Et je suis sur "/category/new"
    Alors je ne devrais pas voir "Ajouter une categorie"

    Soit l'utilisateur "cuisto" existe et a le role "ROLE_RESTO"
    Et l'utilisateur "cuisto" a pour restaurant "Restaurant test"
    Et je suis connecté en tant que "cuisto"
    Et je suis sur "/category/new"
    Alors je devrais voir "Ajouter une categorie"
    
