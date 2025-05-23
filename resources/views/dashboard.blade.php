<!DOCTYPE html>
<html lang="it">

    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>MyMusicStats - Dashboard</title>

        <!-- Bootstrap CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

        <!-- Font Awesome -->
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
        <!-- jQuery -->
        <script src="https://code.jquery.com/jquery-3.7.1.js"></script>

        <!-- Chart.js -->
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

        <script src="/assets/js/dashboard.js"></script>
        <link href="/assets/css/dashboard.css" rel="stylesheet">
    </head>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Condividi Top Artisti (passa tutta la lista)
            document.querySelectorAll('.share-top-artists').forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    // Prendi tutti i nomi degli artisti dalla lista
                    const artistEls = document.querySelectorAll('#top-artists .artist-info p');
                    const artists = Array.from(artistEls).map(el => el.textContent.trim()).filter(
                        Boolean);
                    if (artists.length) {
                        const params = artists.map(a => `content[]=${encodeURIComponent(a)}`).join(
                            '&');
                        window.location.href =
                            `{{ route('crea-post') }}?selected=topartist&type=artista&${params}`;
                    }
                });
            });

            // Condividi Top Brani (passa tutta la lista: titolo e artista alternati)
            document.querySelectorAll('.share-top-tracks').forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    // Prendi tutti i brani dalla lista
                    const trackEls = document.querySelectorAll('#top-tracks .track-info p');
                    const tracks = [];
                    Array.from(trackEls).forEach(el => {
                        // Supponiamo formato: "Titolo - Artista"
                        const [title, artist] = el.textContent.split(' - ');
                        if (title && artist) {
                            tracks.push(title.trim(), artist.trim());
                        }
                    });
                    if (tracks.length) {
                        const params = tracks.map(t => `content[]=${encodeURIComponent(t)}`).join(
                            '&');
                        window.location.href =
                            `{{ route('crea-post') }}?selected=toptrack&type=brano&${params}`;
                    }
                });
            });

            // Condividi Ascoltati di Recente (passa tutta la lista: titolo e artista alternati)
            document.querySelectorAll('.share-recent-track').forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    // Prendi tutti i brani ascoltati di recente
                    const trackEls = document.querySelectorAll('#recently-played .track-info p');
                    const tracks = [];
                    Array.from(trackEls).forEach(el => {
                        // Supponiamo formato: "Titolo - Artista"
                        const [title, artist] = el.textContent.split(' - ');
                        if (title && artist) {
                            tracks.push(title.trim(), artist.trim());
                        }
                    });
                    if (tracks.length) {
                        const params = tracks.map(t => `content[]=${encodeURIComponent(t)}`).join(
                            '&');
                        window.location.href =
                            `{{ route('crea-post') }}?selected=recentlyplayed&type=ascoltato_di_recente&${params}`;
                    }
                });
            });

            // Condividi Top Generi (passa tutta la lista)
            document.querySelectorAll('.share-top-genre').forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    // Usa window.lastGenres per ottenere i generi
                    let genres = [];
                    if (window.lastGenres) {
                        genres = Object.keys(window.lastGenres);
                    }
                    if (genres.length) {
                        const params = genres.map(g => `content[]=${encodeURIComponent(g)}`).join(
                            '&');
                        window.location.href =
                            `{{ route('crea-post') }}?selected=genere&type=genere&${params}`;
                    } else {
                        alert("Nessun genere trovato da condividere!");
                    }
                });
            });
        });
    </script>

    <body>
        <!-- Navbar -->
        <nav class="navbar navbar-expand-lg navbar-dark fixed-top bg-dark">
            <div class="container-fluid">
                <a class="navbar-brand" href="#">MyMusicStats</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav me-auto">
                        <li class="nav-item"><a class="nav-link nav-link-page active" href="#dashboard">Dashboard</a>
                        </li>
                        <li class="nav-item"><a class="nav-link nav-link-page" href="#ricerca-amici">Ricerca Amici</a>
                        </li>
                        <li class="nav-item"><a class="nav-link nav-link-page" href="#lista-amici">I tuoi Amici</a></li>
                        <li class="nav-item"><a class="nav-link nav-link-page" href="#post-feed">Post Feed</a></li>
                    </ul>
                    <div class="d-flex align-items-center">
                        <div class="position-relative me-3">

                        </div>
                        <div class="dropdown me-3" id="friend-requests-dropdown">
                            <button class="btn btn-dark position-relative" type="button" data-bs-toggle="dropdown"
                                aria-expanded="false">
                                <i class="fas fa-user-friends"></i>
                                <span
                                    class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
                                    id="friend-request-count">
                                    0
                                </span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-dark p-2 text-white" style="width: 250px;"
                                id="friend-request-list">
                                <li><span class="dropdown-item-text text-white">Nessuna richiesta di amicizia</span>
                                </li>
                            </ul>
                        </div>
                        <img src=" {{ Auth::user()->profile_picture }} " alt="pfp" class="rounded-circle me-2"
                            width="32" height="32">
                        <span class="text-white">{{ Auth::user()->username }}</span>
                        <a href="{{ route('crea-post') }}" class="btn btn-success mx-2">
                            <i class="fas fa-plus"></i> Crea Post
                        </a>
                        <button type="button" class="btn btn-outline-info me-lg-auto mx-3"
                            id="logoutBtn">Logout</button>
                        <script>
                            document.getElementById('logoutBtn').addEventListener('click', async () => {
                                const token = localStorage.getItem('auth_token');
                                if (!token) return;

                                try {
                                    await fetch('/api/logout', {
                                        method: 'POST',
                                        headers: {
                                            'Authorization': `Bearer ${token}`,
                                            'Accept': 'application/json'
                                        }
                                    });
                                } catch (err) {
                                    console.error('Errore durante il logout:', err);
                                }

                                // Rimuovi token dal localStorage e reindirizza o aggiorna
                                localStorage.removeItem('auth_token');
                                window.location.href = '/';
                            });
                        </script>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <div class="main-content" style="margin-top: 80px;">
            <div class="container-fluid">
                <div class="row">
                    <!-- Sidebar -->
                    <div class="col-lg-2 d-none d-lg-block mb-4 absolute">
                        <div class="sidebar sticky-top" style="top: 100px;">
                            <h5 class="mb-3">Menu</h5>
                            <a href="#dashboard" class="sidebar-link nav-link-page active"><i class="fas fa-home"></i>
                                Dashboard</a>

                            <h5 class="mt-4 mb-3">Social</h5>
                            <a href="#ricerca-amici" class="sidebar-link nav-link-page"><i class="fas fa-user-plus"></i>
                                Ricerca Amici</a>
                            <a href="#lista-amici" class="sidebar-link nav-link-page"><i
                                    class="fas fa-user-friends"></i> I tuoi Amici</a>
                            <a href="#post-feed" class="sidebar-link nav-link-page mb-3"><i
                                    class="fas fa-newspaper"></i>
                                Post Feed</a>
                            <a href="#web-api" class="sidebar-link nav-link-page border-bottom-0"><i
                                    class="fas fa-code"></i>
                                Sviluppatori</a>
                        </div>

                    </div>

                    <!-- Content Area -->
                    <div class="col-lg-10">
                        <!-- Sezioni -->
                        <div id="dashboard" class="page-section">
                            <!-- Content -->
                            <div class="col-lg-10">
                                <!-- Profile Header -->
                                <div id="dashboard" class="section profile-header">
                                    <img src="{{ Auth::user()->profile_picture }}" alt="Profile Picture" />
                                    <div class="profile-info">
                                        <h1>Ciao, <span id="username"> {{ $username }} </span>! 👋</h1>
                                        <p>Ecco le tue statistiche musicali</p>
                                    </div>
                                </div>

                                <!-- Time Filter -->
                                <div class="time-filter">
                                    <div class="filter-option active" data-period="short_term">
                                        4 Settimane
                                    </div>
                                    <div class="filter-option" data-period="medium_term">6 mesi</div>
                                    <div class="filter-option" data-period="long_term">Anno</div>
                                </div>



                                <div class="section-header">
                                    <h2>Le Tue Top Statistiche</h2>
                                    <button class="btn btn-sm btn-dark" id="refresh-recent">
                                        <i class="fas fa-sync-alt"></i> Aggiorna
                                    </button>
                                    <script>
                                        document.addEventListener('DOMContentLoaded', function() {
                                            document.getElementById('refresh-recent').addEventListener('click', async function() {
                                                try {
                                                    const token = localStorage.getItem('auth_token');
                                                    const response = await fetch('/api/spotify/renew', {
                                                        method: 'GET',
                                                        headers: {
                                                            'Accept': 'application/json',
                                                            'Authorization': `Bearer ${token}`
                                                        }
                                                    });
                                                    location.reload();
                                                } catch (e) {
                                                }
                                            });
                                        });
                                    </script>
                                </div>

                                <div class="row">
                                    <!-- Top Artists -->
                                    <div class="col-md-6 mb-4">
                                        <div class="card h-100">
                                            <div class="card-header d-flex justify-content-between align-items-center">
                                                <h5 class="mb-0">Top Artisti</h5>
                                                <div class="dropdown">
                                                    <button class="btn btn-sm btn-dark dropdown-toggle" type="button"
                                                        id="artistsDropdown" data-bs-toggle="dropdown"
                                                        aria-expanded="false">
                                                        <i class="fas fa-ellipsis-v"></i>
                                                    </button>
                                                    <ul class="dropdown-menu dropdown-menu-dark"
                                                        aria-labelledby="artistsDropdown">
                                                        <li>
                                                            <a class="dropdown-item share-top-artists"
                                                                href="#">Condividi</a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                            <div class="card-body" id="top-artists">
                                                <!-- I dati verranno caricati qui -->

                                                <div class="loading">
                                                    <div class="spinner-border" role="status">
                                                        <span class="visually-hidden">Caricamento...</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Top Tracks -->
                                    <div class="col-md-6 mb-4">
                                        <div class="card h-100">
                                            <div class="card-header d-flex justify-content-between align-items-center">
                                                <h5 class="mb-0">Top Brani</h5>
                                                <div class="dropdown">
                                                    <button class="btn btn-sm btn-dark dropdown-toggle" type="button"
                                                        id="tracksDropdown" data-bs-toggle="dropdown"
                                                        aria-expanded="false">
                                                        <i class="fas fa-ellipsis-v"></i>
                                                    </button>
                                                    <ul class="dropdown-menu dropdown-menu-dark"
                                                        aria-labelledby="tracksDropdown">
                                                        <li>
                                                            <a class="dropdown-item share-top-tracks"
                                                                href="#">Condividi</a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                            <div class="card-body" id="top-tracks">
                                                <!-- I dati verranno caricati qui -->

                                                <div class="loading">
                                                    <div class="spinner-border" role="status">
                                                        <span class="visually-hidden">Caricamento...</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Brani Recenti -->
                                    <div class="col-md-8 mb-4">
                                        <div class="card">
                                            <div class="card-header d-flex justify-content-between align-items-center">
                                                <h5 class="mb-0">Ascoltati di recente</h5>
                                                <div class="dropdown">
                                                    <button class="btn btn-sm btn-dark dropdown-toggle" type="button"
                                                        id="trendsDropdown" data-bs-toggle="dropdown"
                                                        aria-expanded="false">
                                                        <i class="fas fa-ellipsis-v"></i>
                                                    </button>
                                                    <ul class="dropdown-menu dropdown-menu-dark"
                                                        aria-labelledby="trendsDropdown">
                                                        <li>
                                                            <a class="dropdown-item share-recent-track">Condividi</a>
                                                        </li>

                                                    </ul>
                                                </div>
                                            </div>
                                            <div class="card-body">
                                                <div id="recently-played">
                                                    <div class="loading h-100">
                                                        <div class="spinner-border" role="status">
                                                            <span class="visually-hidden">Caricamento...</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Music Genres -->
                                    <div class="col-md-4 mb-4">
                                        <div class="card">
                                            <div class="card-header d-flex justify-content-between align-items-center">
                                                <h5 class="mb-0">Generi Musicali</h5>
                                                <div class="dropdown">
                                                    <button class="btn btn-sm btn-dark dropdown-toggle" type="button"
                                                        id="genresDropdown" data-bs-toggle="dropdown"
                                                        aria-expanded="false">
                                                        <i class="fas fa-ellipsis-v"></i>
                                                    </button>
                                                    <ul class="dropdown-menu dropdown-menu-dark"
                                                        aria-labelledby="genresDropdown">

                                                        <li>
                                                            <a class="dropdown-item share-top-genre"
                                                                href="#">Condividi</a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                            <div class="card-body">
                                                <div id="genres-chart" class="chart-container">
                                                    <!-- Il grafico a torta verrà caricato qui -->
                                                    <div class="loading h-100">
                                                        <div class="spinner-border" role="status">
                                                            <span class="visually-hidden">Caricamento...</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- End of Content -->

                        </div>



                        <div id="ricerca-amici" class="page-section" style="display:none;">
                            <h1>Ricerca Amici</h1>
                            <input type="text" id="search-friend-input" class="form-control mb-3"
                                placeholder="Cerca un utente...">
                            <div id="search-friend-result" class="mt-3"></div>
                        </div>

                        <div id="lista-amici" class="page-section" style="display:none;">
                            <h1>I tuoi Amici</h1>
                            <ul id="friends-list" class="list-group "></ul>
                        </div>

                        <div id="post-feed" class="page-section" style="display:none;">
                            <h1>Post Feed</h1>
                            <div id="posts-feed-container" class="row"></div>
                        </div>

                        <div id="web-api" class="page-section" style="display:none;">
                            <h1>Web API</h1>
                            <p>Scopri come utilizzare le nostre API per accedere ai tuoi dati musicali.</p>

                        </div>
                    </div>
                </div>
            </div>

            <!-- Bootstrap JS -->
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

            <!-- Navigazione e API -->
            <script src="/assets/js/social.js"></script>
    </body>

</html>
