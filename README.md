MyMusicStats

1. Introduzione

MyMusicStats è un'applicazione web che consente agli utenti di analizzare e condividere le proprie statistiche musicali. Sfrutta l'integrazione con Spotify per recuperare informazioni personali sull’ascolto musicale e presenta un'interfaccia interattiva con funzionalità sociali come post, reazioni e feed condivisi.

2. Stack Tecnologico

* Backend: Laravel 10 (PHP 8.2+)
* Frontend: Blade templates, JavaScript, jQuery
* Database: MySQL
* Librerie:

  * Chart.js per la visualizzazione grafica
  * Bootstrap per il design responsive
  * Font Awesome per le icone
* API esterne: Spotify Web API (OAuth2)

3. Struttura del Database

Il database principale include le seguenti tabelle:

* utenti: informazioni sul profilo dell'utente (username, Spotify ID, avatar, ecc.)
* amicizie: gestione delle relazioni tra utenti (id\_mittente, id\_destinatario, status)
* posts: contiene i post pubblicati, compresi tipo e contenuto JSON
* post\_reactions: mappa le reazioni degli utenti ai post (like, love, ecc.)
* brani\_recenti: ultimi brani ascoltati (caching)
* top\_tracks: brani più ascoltati per ciascun utente
* top\_artists: artisti più ascoltati per ciascun utente
* generi: generi musicali derivati dagli artisti ascoltati
* altri eventuali: audio features, statistiche aggregate

4. Funzionalità Principali

4.1 Autenticazione via Spotify

Gli utenti accedono tramite OAuth2 usando il proprio account Spotify. L’app salva i token temporanei e li utilizza per interrogare l’API.

4.2 Dashboard Personale

Dopo il login, l'utente accede a una dashboard dinamica che mostra:

* Artisti e brani top per intervalli (4 settimane, 6 mesi, 1 anno)
* Generi musicali predominanti (grafico a torta)
* Cronologia degli ascolti recenti

La dashboard è caricata in modalità SPA (Single Page Application), con sezioni navigabili dinamicamente tramite sidebar e top bar.

4.3 Feed e Post

Ogni utente può condividere contenuti sotto forma di post nel proprio feed o in quello degli amici. Tipologie di post supportate:

* 🎵 track: “Sto ascoltando ‘Blinding Lights’ di The Weeknd”
* 🎤 artist: “Il mio artista più ascoltato è Taylor Swift”
* 📈 stat: “Ho ascoltato 1200 minuti di musica questo mese”
* 💽 album: “Il mio album top è ‘After Hours’”
* 🧩 genre: “I miei generi più ascoltati sono Pop e Rock”

Ogni post ha:

* type: tipo di post
* content: oggetto JSON con dati o messaggio
* id\_utente: autore
* created\_at: data di creazione

4.4 Reazioni ai Post

Gli utenti possono reagire ai post con una delle seguenti reazioni:

* like
* love
* laugh
* angry

Una reazione è associata a un singolo utente e un singolo post, ed è gestita con un toggle: cliccando nuovamente sulla stessa reazione, questa viene rimossa.

4.5 Feed Amici

La sezione “Post Feed” mostra:

* I propri post
* I post degli amici con status “accepted” (gli amici che hanno accettato la nostra richiesta di amicizia)

I post sono ordinati per data di creazione decrescente. Ogni post mostra il numero di reazioni e i dati dell’utente che lo ha pubblicato.

4.6 Ricerca e Richiesta Amicizia

Un utente può cercare amici tramite il loro Spotify ID e inviare una richiesta. Le richieste sono gestite da:

* pending: in attesa
* accepted: accettata
* rejected: rifiutata

Nella topbar è presente un'icona campanella con un badge che segnala il numero di richieste in sospeso.

4.7 SPA Navigation

La dashboard è gestita tramite un sistema JavaScript che:

* Mostra una sola sezione alla volta
* Carica dinamicamente tutte le sezioni

5. API Endpoints

POST /api/posts
Crea un nuovo post
Body JSON:
{
"type": "track",
"content": {
"message": "Sto ascoltando 'Blinding Lights' di The Weeknd"
}
}

POST /api/posts/{id}/react
Aggiunge o rimuove una reazione al post

GET /api/posts/feed
Recupera tutti i post dell’utente e dei suoi amici

GET /api/friends/list
Lista degli amici

GET /api/friend-requests/pending
Restituisce il numero di richieste di amicizia in sospeso

GET /api/spotify/top-genres
Restituisce la lista dei generi principali dell’utente

GET /api/spotify/top-tracks
Restituisce la lista dei brani top

GET /api/spotify/top-artists
Restituisce la lista degli artisti top

GET /api/spotify/recently-played
Restituisce la cronologia di ascolto

6. Comportamenti Dinamici JS

* Script dashboard.js gestisce caricamento asincrono dei dati e rendering dei grafici
* Script social.js gestisce feed, ricerca amici, invio richieste e reazioni
* Script sidebar-navigation.js gestisce navigazione interna SPA
* Gli script sono caricati nella dashboard in <script defer> o prima di </body>

7. Sicurezza

* Accesso protetto da OAuth2
* Gli endpoint API richiedono token di autenticazione
* Ogni utente può modificare solo i propri post e dati

8. Espandibilità futura

* Paginazione e caricamento lazy per feed e liste
* Aggiunta di commenti ai post
* Notifiche in tempo reale via Laravel Echo
* Integrazione con ulteriori fonti musicali (es. YouTube Music)

9. Conclusioni

MyMusicStats è una piattaforma incentrata sull’analisi musicale personale e l’interazione sociale. Combina visualizzazione dati, API e funzionalità social per offrire un'esperienza coinvolgente e speciale all’utente.
