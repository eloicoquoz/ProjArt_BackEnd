<h1 dir="ltr">
    Documentation d’installation
</h1>
<h2 dir="ltr">
    Téléchargement du Backend
</h2>
<p dir="ltr">
    Le code source du Backend de l’application est disponible au téléchargement
sur ce    <a href="https://github.com/eloicoquoz/ProjArt_BackEnd">repo Github</a>.
</p>
<h2 dir="ltr">
    Téléchargement et installation de PostgreSQL et pgAdmin4
</h2>
<p dir="ltr">
    Le Backend de cette application est créé pour communiquer avec une base de
    données PostgreSQL, il faut donc s’assurer que la technologie pgsql est
    installée sur la machine sur laquelle le backend tournera. 
</p>
<p dir="ltr">
    PostgreSQL peut être téléchargé
    <a href="https://www.postgresql.org/download/">
        à travers le site officiel
    </a>
    . Vous trouverez également des installateurs sur
    <a
        href="https://www.enterprisedb.com/downloads/postgres-postgresql-downloads"
    >
        le site EntrepriseDB
    </a>
    .
</p>
<br/>
<p dir="ltr">
    Suivez les instructions de l’installateur téléchargé ou celles offertes par
    la documentation officielle dans le cas où vous ne souhaitez pas utiliser
    un installateur.
</p>
<br/>
<p dir="ltr">
    Dans le cadre de ce projet nous nous sommes servis de pgAdmin 4 et vous
    recommandons d’en faire de même. Le téléchargement pour tous les systèmes
d’exploitation est disponible    <a href="https://www.pgadmin.org/download/">ici</a>. Téléchargez et
    installez la dernière version du programme sur votre machine. (Si vous vous
    êtes servi d’un installateur pour psql, le programme devrait avoir été
    installé en même temps.)
</p>
<br/>
<p dir="ltr">
    Enregistrez un nouveau serveur dans le groupe créé par défaut comme ceci:
</p>
<p dir="ltr">
    <img
        src="https://lh5.googleusercontent.com/dMaPaoX50mGnn4BtiW2CueIR5gQl9AYZ9A23q8FXwp0NrCcowirX3sIxcd7-ksm4y8bZCF5YDRLwZjN-0VEuzI7elv7RXf1tR4s7lIPtydOJiiJWufG_Wv6AETy3YrvT5LEsKlyNegHgg1cZxg"
        width="525"
        height="399.01980198019805"
    />
</p>
<p dir="ltr">
    Un menu s’ouvre, rentrez le nom du serveur qui vous convient puis cliquez
    sur l’onglet “Connection” et entrez les informations de connexion à votre
    serveur (Host, Port, Username, Password). N’oubliez pas ces informations,
    nous en aurons besoin plus tard lors de la liaison entre Laravel et la base
    de données. Enregistrez votre nouveau serveur.
</p>
<p dir="ltr">
    <img
        src="https://lh3.googleusercontent.com/j0qK_1qanmGrS5pWSPZ0fBmaGzjJEDyUt8r35jEz12c-pinYgb7SrrykOE77YUmJNFbg-jWw_B3SA-SlSQtcTl3BUcGK_KSrpB9o2RH0-_7rR0_PppRLvZWrEjZygxf_P1x7Q_Qcig_b4OXXwA"
        width="265"
        height="291"
    />
    <img
        src="https://lh4.googleusercontent.com/IpWHDBFv7gYN5WeOFoEzd6vmpylYThv_6sqmTkbd6fDSdmHE-8n4SiECiCDnBvqrYRUpG_dc8FfEPCt75DcbNqV9vKqxXAuVSXmjdSGCo4BME8kuAB0JxZk8lDhyG-mjnzG7E6IGs8_wI2fwNg"
        width="265"
        height="291"
    />
</p>
<br/>
<p dir="ltr">
    Créez une nouvelle base de données dans le serveur.
</p>
<p dir="ltr">
    <img
        src="https://lh6.googleusercontent.com/m6IrfiDsAiRQ-KBPJ3Jgv0MP_i5tIhU6Cb8H3qP4wSraMjzgtq2nn4Qjx0M-MupR3Fcbl-wDVFChEM5aahdQc19a1JsjiVwQ6B5yg5NVmNDXXOjN2TKBLJGBb48qe024x5C-4pg4KpIwlUDVKA"
        width="626"
        height="477"
    />
</p>
<p dir="ltr">
    Entrez le nom de votre nouvelle base de données et enregistrez. Comme pour
    les informations relatives au serveur, nous aurons besoin du nom de la base
    de données pour la liaison avec Laravel.
</p>
<br/>
<p dir="ltr">
    <img
        src="https://lh6.googleusercontent.com/ERUEDr4pMf3uIwAmHmU7aKhW3vq66BjNFJf2lTkS9PGtoIKZvM3G7zXIL2Vy_dLvAoLyk0uPXoD6nhR4ewa4Dw4u9TLMJKP_WTGvw4SRDxxf6TterOa5JKUUk77r0R7XByfnp8PckbqrJt3sIQ"
        width="374"
        height="291"
    />
</p>
<h2 dir="ltr">
    Liaison de Laravel avec la base de données
</h2>
<p dir="ltr">
    Dans le dossier racine du backend, renommez le fichier .env-example en .env
    et adaptez les lignes correspondantes à votre base de données.
</p>
<p dir="ltr">
    <img
        src="https://lh6.googleusercontent.com/N9IKZFnMJs4t5kSgkfKDU1vvaCJKP-_17d7emjySdXzPlALjKKnky4_ZxI6t2IEYn6ooxXZ9JOAbeuAKysa4ycjWL0dwwgqXiigrTUtQV0vNxW9Uc7pKi4rKE73_lZ6c-HGW6uHhffzjq_UiVg"
        width="524"
        height="405"
    />
</p>
<h2 dir="ltr">
    Création des tables et peuplement
</h2>
<p dir="ltr">
    Une fois la liaison établie, ouvrez un terminal et déplacez vous dans le
    dossier racine du Backend.
</p>
<p dir="ltr">
    Ici, lancez les commandes suivantes dans l’ordre:
</p>
<ol>
    <li dir="ltr">
        <p dir="ltr">
            php artisan migrate:install
        </p>
    </li>
    <li dir="ltr">
        <p dir="ltr">
            php artisan migrate
        </p>
    </li>
</ol>
<p dir="ltr">
    Après avoir lancé ces commandes, vous pouvez vérifier que les tables ont
    été correctement créées dans votre base de données en vous servant de
    pgAdmin4.
</p>
<br/>
<p dir="ltr">
    Avant de peupler la base de donnée, il faut éditer le fichier
    racineBackend/database/seeders/DatabaseSeeder.php. 
</p>
<p dir="ltr">
    Cherchez la méthode ‘scrapeForClasses()’  et entrer un email et
    mot-de-passe permettant de se connecter à la plateforme GAPS dans les
    variables correspondantes.
</p>
<p dir="ltr">
    <img
        src="https://lh3.googleusercontent.com/vOSf4DycVVbHwNozmRxGOLpgy3ifTTFXPlvSYY0BR9RnlCb9325QjMz6eFM1EQ084Tv9mWvAs1ykAXrAmbgMJNA5c_aJxwxeq6Hz_yqLLvFzxEFohG_jBqGRCIz4yvyOQDxItBA_cUt0pyLEfA"
        width="541"
        height="417.48699249503517"
    />
</p>
<p dir="ltr">
    Une fois le seeder modifié et enregistré, retournez dans le terminal et
    lancer la commande suivante (cette dernière prend quelques minutes à cause
    du temps de chargement des données à récupérer sur GAPS): 
</p>
<ol>
    <li dir="ltr">
        <p dir="ltr">
            php artisan db:seed
        </p>
    </li>
</ol>
<p dir="ltr">
    Le seeder crée les utilisateurs des professeurs et chargés de cours COMEM+ 
    entrés dans le fichier racineBackend/resources/Professeurs.txt  (au moment
    du développement du projet, c’est à dire juin 2022), d’administrateur(s)
    entré(s) dans le fichier racineBackend/resources/Administration.txt (pour
    le moment uniquement Daniela Oberlojer) et des classes COMEM+ entrées dans
    le fichier racineBackend/resources/Classes.txt (pour le moment seules les
    volées 48, 49 et 50 sont entrées).
</p>
<br/>
<p dir="ltr">
    Finalement vous pouvez lancer le backend à l’aide de la commande suivante:
</p>
<ol>
    <li dir="ltr">
        <p dir="ltr">
            php artisan serve
        </p>
    </li>
</ol>
