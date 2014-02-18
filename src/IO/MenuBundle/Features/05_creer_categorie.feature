# language: fr
@categorie
Fonctionnalité: Créer et modifier des categories

Contexte:
    Soit le restaurant "Restaurant test" existe avec l'url "/tests/import.json"
    Soit je supprime tous les "IOMenuBundle:Category"
    Soit l'utilisateur "resto" existe et a le role "ROLE_RESTAURATEUR"
    Et l'utilisateur "resto" a pour restaurant "Restaurant test"

@5.1
Scénario: 05.1 -  Créer une categorie : Authorisations
    Soit je suis sur "/categorie/new"
    Alors je ne devrais pas voir "Ajouter une catégorie"

    Soit l'utilisateur "cuisto" existe et a le role "ROLE_CUISINIER"
    Et l'utilisateur "cuisto" a pour restaurant "Restaurant test"
    Et je suis connecté en tant que "cuisto"
    Et je suis sur "/categorie/new"
    Alors je ne devrais pas voir "Ajouter une catégorie"

    Et je suis connecté en tant que "resto"
    Et je suis sur "/categorie/new"
    Alors je devrais voir "Ajouter une catégorie"
    
@5.2
Scénario: 05.2 -  Créer une categorie avec image
    Soit je suis connecté en tant que "resto"

    Soit je suis sur "/carte"
    Alors je ne devrais pas voir "Entrées"
    Et je ne devrais pas voir "Icone de Entrées"

    Soit je suis sur "/categorie/new"
    Et je presse "Ajouter"
    Alors je devrais voir "Veuillez renseigner un nom"
    Et je remplis "category[name]" avec "Entrées"
    Et je remplis "category[file]" avec "web/tests/import.json"
    Et je presse "Ajouter"
    Alors je devrais voir "Image non valide"

    Et je remplis "category[file]" avec "web/tests/content.png"
    Et je presse "Ajouter"
    Alors je devrais voir "La categorie à bien été ajoutée"

    Soit je suis sur "/carte"
    Alors je devrais voir "Entrées"
    Et je devrais voir l'image "Icone Entrées"

    Soit je suis sur "/categorie/new"
    Et je remplis "category[name]" avec "Salades"
    Et je sélectionne "Entrées" depuis "category[parent]"
    Et je presse "Ajouter"
    Alors je devrais voir "La categorie à bien été ajoutée"

    Soit je suis sur "/carte"
    Alors je devrais voir "Salades"

@5.3
Scénario: 05.3 -  Modifier une categorie avec image
    Soit je crée une catégorie "Entrées" pour le restaurant "Restaurant test"
    Soit je suis connecté en tant que "resto"

    Soit je suis sur "/carte"
    Alors je devrais voir "Entrées"

    Soit je suis "Entrées"
    Et je suis "Modifier"
    Et je remplis "category[name]" avec ""
    Et je presse "Modifier"
    Alors je devrais voir "Veuillez renseigner un nom"
    Et je remplis "category[name]" avec "Desserts"
    Et je remplis "category[file]" avec "web/tests/import.json"
    Et je presse "Modifier"
    Alors je devrais voir "Image non valide"

    Et je remplis "category[file]" avec "web/tests/content.png"
    Et je presse "Modifier"
    Alors je devrais voir "La categorie à bien été modifiée"

    Soit je suis sur "/carte"
    Alors je devrais voir "Desserts"    
    Et je devrais voir l'image "Icone Desserts"


@5.4
Scénario: 05.4 -  Supprimer une categorie    
    Soit je crée une catégorie "Entrées" pour le restaurant "Restaurant test"
    Soit je suis connecté en tant que "resto"

    Soit je suis sur "/carte"
    Alors je devrais voir "Entrées"

    Soit je suis "Entrées"
    Et je presse "Supprimer"
    Alors je devrais voir "La categorie à bien été supprimée"

    Soit je suis sur "/carte"
    Alors je ne devrais pas voir "Entrées"
