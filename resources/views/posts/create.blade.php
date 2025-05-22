<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=0.8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>MyMusicStats - Crea Post</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background-color: #0a0a0b;
            color: #ffffff;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }
        .stats-card {
            background-color: #1a1b1e;
            border: 1px solid #2d2f36;
            border-radius: 12px;
        }
        .form-control, .form-select {
            background-color: #2d2f36;
            border: 1px solid #404248;
            color: #ffffff;
            border-radius: 8px;
        }
        .form-control:focus, .form-select:focus {
            background-color: #2d2f36;
            border-color: #1db954;
            box-shadow: 0 0 0 0.2rem rgba(29, 185, 84, 0.25);
            color: #ffffff;
        }
        .form-control::placeholder {
            color: #9ca3af;
        }
        .form-label {
            color: #ffffff;
            font-weight: 500;
        }
        .btn-publish {
            background-color: #1db954;
            border: none;
            border-radius: 20px;
            font-weight: 600;
            padding: 12px 32px;
            transition: background-color 0.2s;
        }
        .btn-publish:hover {
            background-color: #1ed760;
        }
        .form-select option {
            background-color: #2d2f36;
            color: #ffffff;
        }
        .page-title {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 2rem;
        }
    </style>
</head>
<body>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // --- AUTOCOMPILE DA GET ---
            function getQueryParams() {
                const params = {};
                window.location.search
                    .substring(1)
                    .split("&")
                    .forEach(function (item) {
                        if (!item) return;
                        const [key, value] = item.split("=");
                        if (key.endsWith("[]")) {
                            const arrKey = key.slice(0, -2);
                            params[arrKey] = params[arrKey] || [];
                            params[arrKey].push(decodeURIComponent(value || ""));
                        } else {
                            params[key] = decodeURIComponent(value || "");
                        }
                    });
                return params;
            }
        
            const postType = document.getElementById('postType');
            const postTitle = document.getElementById('postTitle');
            const postContent = document.getElementById('postContent');
            const charCount = document.getElementById('charCount');
        
            const params = getQueryParams();
        
            if (params.type && params.content) {
                let type = params.type.toLowerCase();
                let selected = params.selected ? params.selected.toLowerCase() : "";
                let contentArr = Array.isArray(params.content) ? params.content : [params.content];
                let title = "";
                let text = "";
        
                if (type === "artista") type = "artist";
                if (type === "brano" || type === "ascoltato_di_recente") type = "track";
                if (type === "genere") type = "genre";
        
                function isLikelyDate(str) {
                    return /\d{1,2}\/\d{1,2}\/\d{2,4}/.test(str) || /AM|PM/.test(str) || /\d{1,2}:\d{2}/.test(str);
                }
        
                if (type === "artist") {
                    if (contentArr.length > 1) {
                        title = "Top Artisti";
                        text = `Ecco i miei artisti preferiti del periodo:\n` +
                            contentArr.map((a, i) => `${i + 1}. ${a}`).join('\n');
                    } else {
                        title = contentArr[0] || "Artista";
                        text = `Il mio artista preferito è ${title}! 🎤`;
                    }
                } else if (type === "track") {
                    if (selected === "toptrack" && contentArr.length > 2) {
                        title = "Top Brani";
                        let pairs = [];
                        for (let i = 0; i < contentArr.length - 1; i += 2) {
                            const track = contentArr[i];
                            const artist = contentArr[i + 1];
                            if (!isLikelyDate(artist)) {
                                pairs.push({ track, artist });
                            }
                        }
                        text = `Ecco le canzoni che sto ascoltando di più ultimamente:\n` +
                            pairs.map((p, i) => `${i + 1}. "${p.track}" di ${p.artist}`).join('\n');
                    }
                    else if (selected === "recentlyplayed" && contentArr.length > 2) {
                        title = "Brani ascoltati di recente";
                        let pairs = [];
                        for (let i = 0; i < contentArr.length - 1; i += 2) {
                            const track = contentArr[i];
                            const artistOrDate = contentArr[i + 1];
                            if (!isLikelyDate(artistOrDate)) {
                                pairs.push({ track, artist: artistOrDate });
                            }
                        }
                        if (pairs.length === 0) {
                            let lines = [];
                            for (let i = 0; i < contentArr.length - 1; i += 2) {
                                const track = contentArr[i];
                                const date = contentArr[i + 1];
                                lines.push(`${i / 2 + 1}. "${track}" il ${date}`);
                            }
                            text = `Ecco alcuni brani che ho ascoltato di recente:\n` + lines.join('\n');
                        } else {
                            text = `Ecco alcuni brani che ho ascoltato di recente:\n` +
                                pairs.map((p, i) => `${i + 1}. "${p.track}" di ${p.artist}`).join('\n');
                        }
                    }
                    else {
                        title = contentArr[0] || "Brano";
                        const artist = contentArr[1] && !isLikelyDate(contentArr[1]) ? contentArr[1] : "";
                        if (selected === "toptrack") {
                            text = `La mia canzone preferita è "${title}" di ${artist}! 🎶`;
                        } else if (selected === "recentlyplayed") {
                            text = `Ultimamente sto ascoltando "${title}" di ${artist}.`;
                        } else {
                            text = `Sto ascoltando "${title}"${artist ? " di " + artist : ""}!`;
                        }
                    }
                } else if (type === "genre") {
                    if (contentArr.length > 1) {
                        title = "Top Generi";
                        text = `Ecco i miei generi musicali preferiti:\n` +
                            contentArr.map((g, i) => `${i + 1}. ${g}`).join('\n');
                    } else {
                        title = contentArr[0] || "Genere";
                        text = `Il mio genere musicale preferito è ${title}! 🕺`;
                    }
                } else {
                    title = contentArr[0] || "";
                    text = contentArr.slice(1).join(", ");
                }
        
                postType.value = type;
                postTitle.value = title;
                postContent.value = text;
                charCount.textContent = `${text.length}/500`;
            }
        });
        </script>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-6 col-xl-5">
                <h1 class="page-title text-center">Crea Post</h1>
                
                <div class="card stats-card">
                    <div class="card-body p-4">
                        <form id="createPostForm">
                            <div class="mb-4">
                                <label for="postType" class="form-label">Tipo di Post</label>
                                <select id="postType" class="form-select" required>
                                    <option value="" disabled selected>Seleziona tipo</option>
                                    <option value="track">Brano</option>
                                    <option value="artist">Artista</option>
                                    <option value="album">Album</option>
                                    <option value="genre">Genere</option>
                                    <option value="stat">Statistica</option>
                                    <option value="general">Generale</option>
                                </select>
                            </div>
                            
                            <div class="mb-4">
                                <label for="postTitle" class="form-label">Titolo</label>
                                <input 
                                    type="text" 
                                    id="postTitle" 
                                    class="form-control" 
                                    required 
                                    placeholder="Il titolo del tuo post"
                                    maxlength="100">
                            </div>
                            
                            <div class="mb-4">
                                <label for="postContent" class="form-label">Contenuto</label>
                                <textarea 
                                    id="postContent" 
                                    class="form-control" 
                                    rows="4" 
                                    required 
                                    placeholder="Scrivi il contenuto del tuo post..."
                                    maxlength="500"
                                    onfocus="this.setSelectionRange(0,0);"
                                    onclick="this.setSelectionRange(0,0);"
                                ></textarea>
                                <div class="text-end mt-2">
                                    <small class="text-secondary" id="charCount">0/500</small>
                                </div>
                            </div>
                            
                            <div class="d-grid">
                                <button type="submit" class="btn btn-publish text-white">
                                    <i class="bi bi-send me-2"></i>
                                    Pubblica Post
                                </button>
                                <a href="{{ route('dashboard') }}" class="btn btn-secondary mt-3">
                                    <i class="bi bi-arrow-left me-2"></i>
                                    Torna alla Dashboard
                                </a>
                            </div>
                        </form>
                        
                        <div id="responseMessage" class="mt-4"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('createPostForm');
            if (!form) return;
        
            const postType = document.getElementById('postType');
            const postTitle = document.getElementById('postTitle');
            const postContent = document.getElementById('postContent');
            const charCount = document.getElementById('charCount');
            const responseMessage = document.getElementById('responseMessage');
        
            postContent.addEventListener('input', function () {
                charCount.textContent = `${postContent.value.length}/500`;
            });
        
            form.addEventListener('submit', async function (e) {
                e.preventDefault();
                responseMessage.innerHTML = '';
        
                const type = postType.value;
                const title = postTitle.value.trim();
                const content = postContent.value.trim();
        
                if (!type || !title || !content) {
                    responseMessage.innerHTML = '<div class="alert alert-danger">Compila tutti i campi.</div>';
                    return;
                }
        
                const body = {
                    type: type,
                    content: {
                        title: title,
                        text: content
                    }
                };
        
                try {
                    const token = localStorage.getItem('auth_token');
                    const res = await fetch('/api/posts/create', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Authorization': `Bearer ${token}`,
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify(body)
                    });
        
                    const data = await res.json();
                    if (res.ok) {
                        responseMessage.innerHTML = '<div class="alert alert-success">Post pubblicato con successo!</div>';
                        form.reset();
                        charCount.textContent = '0/500';
                    } else {
                        responseMessage.innerHTML = `<div class="alert alert-danger">${data.message || 'Errore nella pubblicazione.'}</div>`;
                    }
                } catch (err) {
                    responseMessage.innerHTML = '<div class="alert alert-danger">Errore di rete.</div>';
                }
            });
        });
        </script>
</body>
</html>