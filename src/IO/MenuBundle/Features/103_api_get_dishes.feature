# language: fr
@api @apigetdishes
Fonctionnalité: API : Get dishes

Contexte:
    Soit l'utilisateur "test" existe et a le role "ROLE_RESTAURATEUR"
    Soit le restaurant "Restaurant test" existe avec l'url "/tests/import.json"
    Soit l'utilisateur "test" a pour restaurant "Restaurant test"

@103.1
Scénario: 103.1 -  Get dishes
    Lorsque je suis sur "/api/dishes"
    Alors le code de status de la réponse ne devrait pas être 403

    Lorsque j'appelle "/api/dishes" authentifié avec "test"
    Alors le json devrait convenir:
        | status |
        | ok     |
