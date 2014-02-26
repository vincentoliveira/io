# language: fr
@api @apigetalldishes
Fonctionnalité: API : Get dishes

Contexte:
    Soit l'utilisateur "test" existe et a le role "ROLE_RESTAURATEUR"
    Soit le restaurant "Restaurant test" existe avec l'url "/tests/import.json"
    Soit l'utilisateur "test" a pour restaurant "Restaurant test"

@83.1
Scénario: 83.1 -  Get all dishes
    Lorsque je suis sur "/api/dishes"
    Alors le code de status de la réponse ne devrait pas être 403

    Lorsque j'appelle "/api/dishes" authentifié avec "test"
    Alors le json devrait convenir:
        | status |
        | ok     |
