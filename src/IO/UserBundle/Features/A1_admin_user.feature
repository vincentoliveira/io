# language: fr
@admin
Fonctionnalité: Administration des utilisateurs


Contexte:
    Soit je vide les entités "IOUserBundle:User"
    Soit l'utilisateur "admin" existe et a le role "ROLE_ADMIN"
    Soit je suis connecté en tant que "admin"


@A1
Scénario: A1 -  Création d'un utilisateur
    # L'utilisateur n'existe pas
    Soit je suis sur la page d'accueil
    Et je suis "Administration des utilisateurs"
    Alors je ne devrais pas voir "usertest"

    # Creation de l'utilisateur
    Soit je suis "Ajouter un utilisateur"
    Et je presse "Ajouter"
    Alors je ne devrais pas voir "L'utilisateur \"usertest\" a bien été ajouté"
    Lorsque je remplis "user[username]" avec "usertest"
    Lorsque je remplis "user[email]" avec "usertest@io.fr"
    Lorsque je remplis "user[plainPassword]" avec "usertest"
    Et je presse "Ajouter"
    Alors je devrais voir "L'utilisateur \"usertest\" a bien été ajouté"

    # Test de l'utilisateur
    Soit je suis "Déconnexion"
    Et je remplis "_username" avec "usertest"
    Et je remplis "_password" avec "usertest"
    Et je presse "Connexion"
    Alors le code de status de la réponse devrait être 200
