# MyMusicStats

MyMusicStats è un'applicazione web che permette agli utenti di visualizzare statistiche avanzate sui propri ascolti musicali tramite l'integrazione con l'API di Spotify. Include funzionalità social come post, like, amici, cronologia ascolti, top artisti, top brani e generi preferiti.

## Caratteristiche principali

- Login tramite Spotify (OAuth2)
- Dashboard personale con statistiche musicali
- Cronologia ascolti e classifiche personalizzate
- Funzionalità social (post, like, amicizie)
- Dati sincronizzati con Spotify ma salvati in un database per minimizzare le richieste
- API rest protette da sanctum (descritte anche nella sezione per sviluppatori della dashboard)
---

## Requisiti

- PHP
- Composer
- MySQL o MariaDB
- Laravel 12
- API Spotify Developer (credenziali client)
- Un server web

---

## 1. Clonazione del progetto

## 2. Installazione delle dipendenze

composer install

## 3. Configurazione ambiente

Modifica le seguenti variabili nel file .env:

APP_NAME=MyMusicStats
APP_URL=http://localhost

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=mymusicstats
DB_USERNAME=tuo_username
DB_PASSWORD=tuo_password

SPOTIFY_CLIENT_ID=tuo_client_id
SPOTIFY_CLIENT_SECRET=tuo_client_secret
SPOTIFY_REDIRECT_URI=http://localhost/callback/spotify

## 4. Generazione chiavi e migrazione database

php artisan key:generate
php artisan migrate

## 5. Avvio del server locale

php artisan serve

L'app sarà disponibile su http://localhost:8000.
## 6. Configurazione delle credenziali Spotify

    Vai su Spotify Developer Dashboard

    Crea un'applicazione

    Inserisci il redirect URI esatto configurato in .env, es: http://localhost/callback/spotify

    Copia Client ID e Secret nel file .env

## 7. Comportamento dei dati

    I dati Spotify vengono salvati in tabelle come top_tracks, top_artists, generi, brani_recenti e aggiornati periodicamente (ogni 24 ore) per minimizzare l’uso dell’API.

    Il login è gestito via OAuth2, quindi la tabella utenti non contiene password.

    La dashboard attinge sempre preferibilmente dal database.

## 8. Struttura del database (tabelle principali)

    utenti

    amicizie

    posts

    post_likes

    brani_recenti

    generi

    top_tracks

    top_artists
