<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('createPostForm');
    const postType = document.getElementById('postType');
    const postTitle = document.getElementById('postTitle');
    const postContent = document.getElementById('postContent');
    const charCount = document.getElementById('charCount');
    const responseMessage = document.getElementById('responseMessage');

    // Aggiorna il contatore caratteri
    postContent.addEventListener('input', function () {
        charCount.textContent = `${postContent.value.length}/500`;
    });

    form.addEventListener('submit', async function (e) {
        e.preventDefault();
        responseMessage.innerHTML = '';

        // Prepara il body secondo il controller (type, content)
        const type = postType.value;
        const title = postTitle.value.trim();
        const content = postContent.value.trim();

        if (!type || !title || !content) {
            responseMessage.innerHTML = '<div class="alert alert-danger">Compila tutti i campi.</div>';
            return;
        }
        // Il controller si aspetta un oggetto content (array), qui usiamo un oggetto con titolo e testo
        const body = {
            type: type,
            content: {
            text: content,
            title: title
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
<body>
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
                                    maxlength="500">
                                </textarea>
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
</body>
</html>