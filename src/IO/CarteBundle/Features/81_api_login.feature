# language: fr
@api @apilogin
Fonctionnalité: API : Login

Contexte:
    Soit l'utilisateur "test" existe et a le role "ROLE_RESTAURATEUR"
    Soit l'utilisateur "test" a pour salt "saltdelutilisateurtest"
    Soit l'utilisateur "nouser" n'existe pas

@81.1
Scénario: 81.1 -  Get salt
    Lorsque je suis sur "/api/salt"
    Alors le json devrait convenir:
        | status    |
        | ko        |

    Lorsque je suis sur "/api/salt?username=nouser"
    Alors le json devrait convenir:
        | status    |
        | ko        |

    Lorsque je suis sur "/api/salt?username=test"
    Alors le json devrait convenir:
        | status    | salt                      |
        | ok        | saltdelutilisateurtest    |


@81.2
Scénario: 81.2 -  Login
    Lorsque je suis sur "/api/check_login"
    Alors le json devrait convenir:
        | status    | login |
        | ok        | FALSE |

    Lorsque j'appelle du passé "/api/check_login" authentifié avec "test"
    Alors le json devrait convenir:
        | status    | login |
        | ok        | FALSE |

    Lorsque j'appelle du futur "/api/check_login" authentifié avec "test"
    Alors le json devrait convenir:
        | status    | login |
        | ok        | FALSE |

    Lorsque j'appelle "/api/check_login" authentifié avec "test"
    Alors le json devrait convenir:
        | status    | login |
        | ok        | TRUE  |