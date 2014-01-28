# language: fr
@user
Fonctionnalité: Import wordpress


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


Scénario: 03.2 - Import : Echet
    Soit le restaurant "Restaurant test" existe avec l'url "http://urlquinexistepas.fr"
    Soit je supprime tous les "IOMenuBundle:Category"
    Soit l'utilisateur "admin" existe et a le role "ROLE_ADMIN"
    Soit je suis connecté en tant que "admin"
    Soit je suis sur "/import/"

    Soit je sélectionne "Restaurant test" depuis "import[restaurant]"
    Et je presse "Importer"
    Alors je devrais voir "Echec de la récupération des données"


#Scénario: 03.3 - Import Wordpress
#    Soit le restaurant "Restaurant test" existe avec l'url "/tests/import.json"
#    Soit je supprime tous les "IOMenuBundle:Category"
#    Soit l'utilisateur "admin" existe et a le role "ROLE_ADMIN"
#    Soit je suis connecté en tant que "admin"
#    Soit je suis sur "/import/"

#    Soit je sélectionne "Restaurant test" depuis "import[restaurant]"
#    Et je presse "Importer"
#    Et imprimer la dernière réponse
#    Alors je devrais voir "L'import s'est exécuté avec succès"
