<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>MyMusicStats - Benvenuto</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <script>
        window.addEventListener('load', function() {
            const urlParams = new URLSearchParams(window.location.search);
            const code = urlParams.get('code'); // Ottieni il parametro 'code' dalla query string

            if (code) {
                // Fai una richiesta al server per scambiare il codice con il token di accesso
                fetch(`/auth/spotify/callback?code=${code}`, {
                        method: 'GET',
                        headers: {
                            'Accept': 'application/json',
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.access_token) {
                            // Salva il token nel localStorage
                            localStorage.setItem('access_token', data.access_token);
                            // Reindirizza alla dashboard
                            window.location.href = '/dashboard';
                        } else {
                            console.error('Errore durante il login con Spotify');
                        }
                    })
                    .catch(error => {
                        console.error('Errore nella richiesta:', error);
                    });
            }
        });
    </script>
    <style>
        :root {
            --primary-color: #1DB954;
            --dark-bg: #121212;
            --card-bg: #181818;
            --text-color: #FFFFFF;
            --text-secondary: #B3B3B3;
        }

        body {
            background-color: var(--dark-bg);
            color: var(--text-color);
            font-family: 'Montserrat', sans-serif;
        }

        .navbar {
            background-color: var(--dark-bg);
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
            padding: 15px 0;
        }

        .navbar-brand {
            font-weight: 700;
            color: var(--primary-color) !important;
        }

        .nav-link {
            color: var(--text-color) !important;
            font-weight: 600;
            margin-right: 15px;
        }

        .btn-primary {
            background-color: var(--primary-color);
            border: none;
            border-radius: 50px;
            padding: 8px 20px;
            font-weight: 600;
        }

        .btn-outline-primary {
            border: 2px solid var(--primary-color);
            color: var(--primary-color);
            border-radius: 50px;
            padding: 6px 20px;
            font-weight: 600;
            background-color: transparent;
        }

        .btn-outline-primary:hover {
            background-color: var(--primary-color);
            color: var(--text-color);
        }

        .hero {
            min-height: 90vh;
            display: flex;
            align-items: center;
            padding-top: 80px;
        }

        .hero h1 {
            font-size: 3.5rem;
            font-weight: 800;
            margin-bottom: 1.5rem;
        }

        .hero p {
            font-size: 1.25rem;
            color: var(--text-secondary);
            margin-bottom: 2rem;
        }

        .stat-card {
            background-color: var(--card-bg);
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
            transition: transform 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-10px);
        }

        .stat-value {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--primary-color);
        }

        .gradient-text {
            background: linear-gradient(90deg, var(--primary-color), #4F46E5);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .features {
            padding: 5rem 0;
        }

        .feature-icon {
            font-size: 2.5rem;
            color: var(--primary-color);
            margin-bottom: 1.5rem;
        }

        .cta-section {
            background: linear-gradient(45deg, #1DB954, #4F46E5);
            border-radius: 20px;
            padding: 3rem;
            margin: 3rem 0;
        }
    </style>
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top">
        <div class="container">
            <a class="navbar-brand" href="#">MyMusicStats</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item"><a class="nav-link" href="#">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="#features">Funzionalità</a></li>
                </ul>
                <div class="auth-buttons">
                    <a class="btn btn-primary" href="{{ route('spotify.redirect') }}">Inizia Gratis</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero -->
    <section class="hero pt-5">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h1>Scopri la tua <span class="gradient-text">identità musicale</span></h1>
                    <p>Statistiche approfondite sul tuo ascolto, classifiche personalizzate e interazione con i tuoi
                        amici. Il tutto sincronizzato con Spotify.</p>
                    <div class="d-flex">
                        <a href="{{ route('spotify.redirect') }}" class="btn btn-primary me-3">Collega Spotify</a>
                        <a href="#features" class="btn btn-outline-primary">Esplora le funzionalità</a>
                    </div>
                </div>
                <div class="col-lg-6 mt-5 mt-lg-0">
                    <svg viewBox="0 0 600 400" width="100%" height="auto" class="img-fluid rounded-3 shadow" style="background:#181818;">
                        <!-- Axes -->
                        <line x1="60" y1="350" x2="560" y2="350" stroke="#B3B3B3" stroke-width="2"/>
                        <line x1="60" y1="50" x2="60" y2="350" stroke="#B3B3B3" stroke-width="2"/>
                       
                        <rect x="80"  y="260" width="22" height="90"  fill="#1DB954" rx="2"/>
                        <rect x="110" y="180" width="22" height="170" fill="#4F46E5" rx="2"/>
                        <rect x="140" y="220" width="22" height="130" fill="#F59E42" rx="2"/>
                        <rect x="170" y="120" width="22" height="230" fill="#E53E3E" rx="2"/>
                        <rect x="200" y="200" width="22" height="150" fill="#38BDF8" rx="2"/>
                        <rect x="230" y="140" width="22" height="210" fill="#A21CAF" rx="2"/>
                        <rect x="260" y="170" width="22" height="180" fill="#FBBF24" rx="2"/>
                        <rect x="290" y="90"  width="22" height="260" fill="#10B981" rx="2"/>
                        <rect x="320" y="240" width="22" height="110" fill="#6366F1" rx="2"/>
                        <rect x="350" y="210" width="22" height="140" fill="#F472B6" rx="2"/>
                        <rect x="380" y="130" width="22" height="220" fill="#F87171" rx="2"/>
                        <rect x="410" y="170" width="22" height="180" fill="#34D399" rx="2"/>
                        <rect x="440" y="250" width="22" height="100" fill="#FBBF24" rx="2"/>
                        <rect x="470" y="110" width="22" height="240" fill="#3B82F6" rx="2"/>
                        <rect x="500" y="200" width="22" height="150" fill="#A3E635" rx="2"/>
                    </svg>
                </div>
            </div>
        </div>
    </section>

    <!-- Features -->
    <section class="features">
        <div class="container">
            <div class="text-center mb-5">
                <h2 id="features" class="mb-4">Cosa puoi fare con MyMusicStats</h2>
                <p class="text-secondary col-lg-8 mx-auto">Monitora i tuoi ascolti, confrontali con i tuoi amici,
                    esplora i tuoi artisti top e molto altro.</p>
            </div>
            <div class="row">
                <div class="col-md-4 mb-4 text-center">
                    <div class="feature-icon"><i class="fas fa-chart-bar"></i></div>
                    <h4>Statistiche Ascolti</h4>
                    <p class="text-secondary">Grafici, medie settimanali e top brani personalizzati.</p>
                </div>
                <div class="col-md-4 mb-4 text-center">
                    <div class="feature-icon"><i class="fas fa-user-friends"></i></div>
                    <h4>Amici e Confronti</h4>
                    <p class="text-secondary">Segui i tuoi amici e scopri cosa ascoltano più spesso.</p>
                </div>
                <div class="col-md-4 mb-4 text-center">
                    <div class="feature-icon"><i class="fas fa-music"></i></div>
                    <h4>Analisi Dei Tuoi Dati</h4>
                    <p class="text-secondary">Analisi automatica delle tue canzoni preferite.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA -->
    <section class="container">
        <div class="cta-section text-center text-white">
            <h2 class="mb-4">Unisciti alla community musicale</h2>
            <p class="mb-4">Inizia oggi a tracciare e condividere la tua esperienza musicale.</p>
            <a href="{{ route('spotify.redirect') }}" class="btn btn-light btn-lg">Crea un Account Gratuito</a>
        </div>
    </section>

    <!-- Footer -->
    <footer class="py-5">
        <div class="container text-center">
            <p class="text-secondary mb-2">© 2025 MyMusicStats. Analizza. Condividi. Scopri.</p>
            <div>
                <a href="#" class="text-secondary me-3"><i class="fab fa-facebook-f"></i></a>
                <a href="#" class="text-secondary me-3"><i class="fab fa-twitter"></i></a>
                <a href="#" class="text-secondary me-3"><i class="fab fa-instagram"></i></a>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
