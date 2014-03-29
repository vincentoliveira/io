# language: fr
@user
Fonctionnalité: Connexion des utilisateurs / Déconnexion


Scénario: 02.1 - Connexion : Echec
    Soit je suis sur "/"
    Et je remplis "_username" avec ""
    Et je remplis "_password" avec ""
    Lorsque je presse "_submit"
    Alors je devrais voir "Nom d'utilisateur ou mot de passe incorrect"


Scénario: 02.2 - Connexion / Déconnexion
    Soit l'utilisateur "admin" existe et a le role "ROLE_ADMIN"
    Soit je suis sur "/"
    Et je remplis "_username" avec "admin"
    Et je remplis "_password" avec "admin"
    Lorsque je presse "_submit"
    Alors je devrais voir "Déconnexion"

    Lorsque je suis "Déconnexion"
    Alors je ne devrais pas voir "Bienvenue admin"