# Appointment calendar

Ce projet contient une API Symfony permettant de gérer des rendez-vous (par exemple pour un kiné) et un front Vite (React) qui va consommer cet API.

## Sommaire
- [Backend (API)](#backend-api)
	- [Installation (Linux)](#installation-linux)
		- [Prérequis](#prérequis)
		- [Configuration](#configuration)
	- [Fonctionnalités](#fonctionnalités)
 - [Ftont](#front)
	- [Installation (pour Linux)](#installation-pour-linux)
	- [Fonctionnalités](#fonctionnalités-front)
	- [Choses utiles](#choses-utiles)

## Backend (API)

## Installation (Linux)

### Prérequis
- Une base de donnée accessible (par exemple une bdd MySQL qui tourne sur le même Linux)
- Cron d'installé 
	- ``systemctl status cron`` pour checker l'installation
	- Si Cron n'est pas présent, l'installer avec `sudo apt-get install cron`
- Composer et PHP (>=8.1)
	```console
	sudo apt-get update
	sudo apt-get install php-cli php-zip php-xml php-mbstring php-json php-curl
	cd ~
	curl -sS https://getcomposer.org/installer -o /tmp/composer-setup.php
	sudo php /tmp/composer-setup.php --install-dir=/usr/local/bin --filename=composer
	```
- Symfony CLI
	```console
	curl -sS https://get.symfony.com/cli/installer | bash
	sudo mv ~/.symfony5/bin/symfony /usr/local/bin/symfony
	```
- Redis
	```console
	sudo apt-get install redis-server
	sudo apt-get install php-redis
	```
Vous avez à présent tout ce qu'il faut pour pouvoir lancer l'API !

### Configuration

Installer les paquets avec composer :
```console
cd /var/www/html/appointment-calendar/appointment-calendar-api/
sudo composer update
```

A partir de .env, créer un fichier .env.local qui contiendra les variables d'environnement permettant de paramétrer l'application.
- DATABASE_URL : Connection string pour se connecter à la base de donnée voulue
- JWT_SECRET_KEY: Path vers la clé privée utilisée pour l'authentification par JWT
	- `openssl genpkey -algorithm RSA -out private_key.pem -pkeyopt rsa_keygen_bits:2048`
- JWT_PUBLIC_KEY: Path vers la clé publique utilisée pour l'authentification par JWT
	- `openssl rsa -pubout -in private_key.pem -out public_key.pem`
- JWT_PASSPHRASE: Mot de passe utilisé pour générer la clé privée
- REDIS_DSN: ip vers le serveur Redis utilisé par le cache
- MAILER_DSN: Connection string pour se connecter à la base de donnée voulue
**Pour la démo, un compte Gmail a été créé et configuré pour l'envoie des emails**, voici la connection string complète : `smtp://emailerdemo8@gmail.com:ytzkzczekkleoggq@smtp.gmail.com:587?encryption=tls&auth_mode=login`

A présent il faut construire la base de données et la populer avec des fixtures :
Toujours en étant bien dans le dossier "appointment-calendar-api"
```console
	php bin/console d:s:c
	php bin/console d:f:l
```
C'est bon ! La base de donnée est prête à être utilisée.
Il reste une dernière étape à la configuration : activer le job Cron pour envoyer des mails aux utilisateurs qui ont un rendez-vous dans un jour.

    crontab -e
Dans le fichier qui s'ouvre il faut ajouter une ligne :

    */15 * * * * /usr/bin/php /var/www/html/appointment-calendar/appointment-calendar-api/bin/console App:CheckAppointments

Si besoin, remplacer "/var/www/html/appointment-calendar/appointment-calendar-api" par le chemin vers le dossier de l'API.
Pour tester le bon fonctionnement il peut être utile de mettre "1" à la place de "15" pour que l'execution soit toutes les minutes, et vérifier que le fichier check_appointments.log est créé se remplie bien toutes les minutes dans ..../appointment-calendar/appointment-calendar-api (ne pas oublier de repasser à 15 minutes après vérification)

Plus qu'à `symfony serve`

**Et voilà !**

## Fonctionnalités
L'API sert à stocker des rendez-vous (Appointments), des créneaux (Slots) et des types de prestation (ServiceType). Pour rendre plus clair avec le code je les appellerais par leur nom anglais.

Chaque ressource possède un CRUD que je ne détaillerais pas ici. Pour voir l'ensemble des routes disponibles, veuillez vous référer aux fichiers "Appointment_calendar.postman_collection.json" que vous pouvez importer dans Postman.
A ajouter : les variables d'environnement "{{currentIp}}" qui correspond au port utilisé par Symfony, {{jwtTokenAdmin}} à remplir avec le token renvoyé par "LoginCheck_Admin" et {{jwtTokenUser}}  à remplir avec le token renvoyé par "LoginCheck"

Toutes les 15 minutes un check sera fait pour savoir si un utilisateur a un Appointment prévu le lendemain à la même heure.
Lorsqu'un utilisateur crée un compte, un mail lui est envoyé.

Toutes les routes à part /api/register et /api/login_check nécessitent d'être authentifié par un JWT token obtenu en utilisant un de ces deux endpoint.

Il y a un cache serveur et les routes qui récupèrent des ressources possèdent un ETag pour le caching client.

Il y a deux middleware, un pour checker si le compte auquel un utilisateur essaie de s'authentifier avec est désactivé et un pour gérer les erreurs.

## Front

## Installation (pour Linux)

L'installation du front est une chose extrêmement longue et compliquée. Voici les étapes :

S'assurer d'avoir la dernière version de NodeJS et de npm
```
npm install
npm run dev
```
Et voilà ! Bravo.

Ne pas oublier de changer, si nécessaire, la constante "apiUrl" dans ..../appointment-calendar-front/src/services/ApiService.tsx pour renvoyer vers la base de l'url utilisé par l'API Symfony.

## Fonctionnalités Front

Le front affiche un calendrier avec des zones bleues qui correspondent aux créneaux disponibles enregistrés par l'administrateur (dans l'idée le practicien). Les zones oranges correspondent aux rendez-vous déjà pris, avec le nom du type de prestation dessus.

Il est possible de passer d'une semaine à une autre avec les flèches en haut à droite.

Il est possible de se connecter ou créer un compte. Lorsqu'un utilisateur est connecté, il lui est possible de prendre un rendez-vous en choisissant le type de prestation souhaitée et l'heure de début. L'heure de fin est choisie en fonction de la durée de la prestation choisie.

Lorsqu'un l'utilisateur connecté est un admin, il lui est possible de créer un nouveau créneau (si des créneaux se chevauchent ils fusionneront ensemble) et de créer un nouveau type de prestation.

## Choses utiles

Lors de l'application des fixtures, un compte utilisateur par défaut est créé : "sample_user@gmail.com" avec pour mot de passer "user"
Un compte admin est aussi créé : "emailerdemo8@gmail.com" avec pour mot de passer "admin"

En cas de problème avec le cache serveur, il suffit d'utiliser la commande `redis-cli FLUSHALL`pour effacer tout le cache existant
