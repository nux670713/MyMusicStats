// Navigazione SPA
const sections = document.querySelectorAll(".page-section");
const navLinks = document.querySelectorAll(".nav-link-page");

navLinks.forEach((link) => {
    link.addEventListener("click", function (e) {
        e.preventDefault();
        const targetId = this.getAttribute("href").substring(1);

        // Mostra solo la sezione selezionata
        sections.forEach((section) => {
            section.style.display = section.id === targetId ? "block" : "none";
        });

        // Aggiorna lo stato attivo dei link
        navLinks.forEach((l) => l.classList.remove("active"));
        this.classList.add("active");

        // Caricamento dinamico quando necessario
        if (targetId === "lista-amici") {
            loadFriends();
        }
        if (targetId === "post-feed") {
            loadPosts();
        }
        if (targetId === "statistiche") {
            loadStatistiche();
        }
        if (targetId == "web-api"){
            loadDeveloperPage();
        }
    });
});

function loadDeveloperPage() {
    const token = localStorage.getItem("auth_token");
    const container = document.getElementById("web-api");
    if (!container) return;

    container.innerHTML = `
        <h1>Developer API</h1>
        <div class="mb-4">
            <h5>Il tuo Auth Token</h5>
            <div class="input-group mb-2">
                <input type="text" class="form-control" id="dev-api-token" value="${token || ""}" readonly>
                <button class="btn btn-outline-secondary" type="button" id="copy-api-token">Copia</button>
            </div>
            <small class="text-white">Usa questo token come <b>Bearer Token</b> nell'header <code>Authorization</code> delle tue richieste API.</small>
        </div>
        <h5>Rotte API disponibili</h5>
        <div class="table-responsive">
        <table class="table table-dark table-bordered table-sm align-middle mb-3" style="font-size: 0.95em;">
            <thead>
                <tr>
                    <th>Metodo</th>
                    <th>Endpoint</th>
                    <th>Descrizione</th>
                </tr>
            </thead>
            <tbody>
                <tr><td>GET</td><td><code>/api/spotify/renew</code></td><td>Forza il refresh dei dati Spotify</td></tr>
                <tr><td>GET</td><td><code>/api/spotify/top-tracks</code></td><td>Top brani Spotify</td></tr>
                <tr><td>GET</td><td><code>/api/spotify/top-artists</code></td><td>Top artisti Spotify</td></tr>
                <tr><td>GET</td><td><code>/api/spotify/recently-played</code></td><td>Brani ascoltati di recente</td></tr>
                <tr><td>GET</td><td><code>/api/spotify/top-genres</code></td><td>Top generi Spotify</td></tr>
                <tr><td>GET</td><td><code>/api/friend-request/send/{spotify_id}</code></td><td>Invia richiesta amicizia</td></tr>
                <tr><td>GET</td><td><code>/api/friend-request/accept/{spotify_id}</code></td><td>Accetta richiesta amicizia</td></tr>
                <tr><td>GET</td><td><code>/api/friend-request/reject/{spotify_id}</code></td><td>Rifiuta richiesta amicizia</td></tr>
                <tr><td>GET</td><td><code>/api/friend-request/cancel/{spotify_id}</code></td><td>Annulla richiesta inviata</td></tr>
                <tr><td>GET</td><td><code>/api/friend/remove/{spotify_id}</code></td><td>Rimuovi amico</td></tr>
                <tr><td>GET</td><td><code>/api/friends/list</code></td><td>Lista amici</td></tr>
                <tr><td>GET</td><td><code>/api/friend-requests/pending</code></td><td>Richieste di amicizia in sospeso</td></tr>
                <tr><td>GET</td><td><code>/api/user/search/{spotify_id}</code></td><td>Cerca utente per Spotify ID</td></tr>
                <tr><td>GET</td><td><code>/api/users/list/{username}</code></td><td>Cerca utenti per username</td></tr>
                <tr><td>POST</td><td><code>/api/posts/create</code></td><td>Crea nuovo post (<code>{ type, content }</code> nel body)</td></tr>
                <tr><td>GET</td><td><code>/api/posts/{id}/react/{reaction_type}</code></td><td>Aggiungi/aggiorna reazione a un post</td></tr>
                <tr><td>GET</td><td><code>/api/posts/feed</code></td><td>Feed dei post tuoi e amici</td></tr>
                <tr><td>GET</td><td><code>/api/post/{id}/MyReaction</code></td><td>La tua reazione a un post</td></tr>
                <tr><td>GET</td><td><code>/api/statistiche/top-tracks</code></td><td>Top brani ascoltati</td></tr>
                <tr><td>GET</td><td><code>/api/statistiche/top-artists</code></td><td>Top artisti ascoltati</td></tr>
                <tr><td>GET</td><td><code>/api/statistiche/top-genres</code></td><td>Top generi ascoltati</td></tr>
                <tr><td>GET</td><td><code>/api/statistiche/audio-features</code></td><td>Audio features dei tuoi brani</td></tr>
                <tr><td>GET</td><td><code>/api/statistiche/audio-features-average</code></td><td>Media delle audio features</td></tr>
                <tr><td>GET</td><td><code>/api/statistiche/extreme-tracks</code></td><td>Brani estremi per energia, felicità, acusticità</td></tr>
            </tbody>
        </table>
        </div>
        <div class="alert alert-info">
            <b>Nota:</b> Tutte le rotte richiedono l'header <code>Authorization: Bearer &lt;token&gt;</code>.<br>
            Consulta la documentazione o il codice sorgente per dettagli su parametri e risposte.
        </div>
    `;

    // Copia token negli appunti
    const copyBtn = document.getElementById("copy-api-token");
    if (copyBtn) {
        copyBtn.addEventListener("click", function () {
            const input = document.getElementById("dev-api-token");
            input.select();
            input.setSelectionRange(0, 99999);
            document.execCommand("copy");
            copyBtn.innerText = "Copiato!";
            setTimeout(() => (copyBtn.innerText = "Copia"), 1500);
        });
    }
}

// Funzione generica per aggiornare un contenitore
function updateContainer(containerId, items, renderItem) {
    const container = document.getElementById(containerId);
    container.innerHTML = "";
    if (!Array.isArray(items) || items.length === 0) {
        container.innerHTML = "<p>Nessun elemento disponibile.</p>";
        return;
    }
    items.forEach((item) => {
        const element = renderItem(item);
        container.appendChild(element);
    });
}

async function loadStatistiche(timeRange = 'medium_term', limit = 20) {
    const token = localStorage.getItem("auth_token");
    if (!token) {
        console.error("Token di autenticazione non trovato");
        document.getElementById("statistiche").innerHTML = '<div class="alert alert-danger">Token di autenticazione non trovato. Effettua il login.</div>';
        return;
    }

    const fetchApi = async (url, params = {}) => {
        const queryParams = new URLSearchParams(params).toString();
        const fullUrl = `${url}${queryParams ? `?${queryParams}` : ''}`;
        
        try {
            const res = await fetch(fullUrl, {
                headers: {
                    Accept: "application/json",
                    Authorization: `Bearer ${token}`,
                },
            });
            
            if (!res.ok) {
                const errorData = await res.json().catch(() => ({ error: "Errore sconosciuto" }));
                console.error(`Errore API (${res.status}):`, errorData);
                return null;
            }
            
            return res.json();
        } catch (error) {
            console.error(`Errore richiesta: ${url}`, error);
            return null;
        }
    };

    // Mostra loader
    const container = document.getElementById("statistiche");
    container.innerHTML = '<div class="text-center my-5"><div class="spinner-border" role="status"><span class="visually-hidden">Caricamento...</span></div></div>';

    try {
        // Carica tutte le statistiche in parallelo
        const [
            topTracks,
            topArtists,
            topGenres,
            audioFeatures,
            audioFeaturesAvg,
            extremeTracks,
        ] = await Promise.all([
            fetchApi("/api/statistiche/top-tracks", { time_range: timeRange, limit }),
            fetchApi("/api/statistiche/top-artists", { time_range: timeRange, limit }),
            fetchApi("/api/statistiche/top-genres", { time_range: timeRange, limit }),
            fetchApi("/api/statistiche/audio-features", { time_range: timeRange, limit: 50 }),
            fetchApi("/api/statistiche/audio-features-average", { time_range: timeRange, limit }),
            fetchApi("/api/statistiche/extreme-tracks", { time_range: timeRange, limit }),
        ]);

        // Se tutte le richieste falliscono, mostra un messaggio di errore
        if (!topTracks && !topArtists && !topGenres && !audioFeatures && !audioFeaturesAvg && !extremeTracks) {
            container.innerHTML = '<div class="alert alert-danger">Impossibile caricare le statistiche. Controlla la console per dettagli.</div>';
            return;
        }
    container.innerHTML = `
        <h1>Statistiche Musicali</h1>
        <div class="row">
            <div class="col-md-6">
                <h4>Top Brani</h4>
                <ul id="stat-top-tracks"></ul>
            </div>
            <div class="col-md-6">
                <h4>Top Artisti</h4>
                <ul id="stat-top-artists"></ul>
            </div>
        </div>
        <div class="row mt-4">
            <div class="col-md-6">
                <h4>Top Generi</h4>
                <ul id="stat-top-genres"></ul>
            </div>
            <div class="col-md-6">
                <h4>Media Audio Features</h4>
                <ul id="stat-audio-features-avg"></ul>
            </div>
        </div>
        <div class="row mt-4">
            <div class="col-md-12">
                <h4>Brani Estremi</h4>
                <ul id="stat-extreme-tracks"></ul>
            </div>
        </div>
    `;

    const tracksList = document.getElementById("stat-top-tracks");
    if (Array.isArray(topTracks)) {
        topTracks.slice(0, 5).forEach((track) => {
            const li = document.createElement("li");
            li.innerHTML = `<strong>${track.name}</strong> di ${track.artists.join(", ")}`;
            tracksList.appendChild(li);
        });
    }

    const artistsList = document.getElementById("stat-top-artists");
    if (Array.isArray(topArtists)) {
        topArtists.slice(0, 5).forEach((artist) => {
            const li = document.createElement("li");
            li.innerHTML = `<strong>${artist.name}</strong> (${artist.genres.slice(0, 2).join(", ")})`;
            artistsList.appendChild(li);
        });
    }

    const genresList = document.getElementById("stat-top-genres");
    if (Array.isArray(topGenres)) {
        topGenres.forEach((genre) => {
            const li = document.createElement("li");
            li.textContent = `${genre.genre} (${genre.count})`;
            genresList.appendChild(li);
        });
    }

    const featuresList = document.getElementById("stat-audio-features-avg");
    if (audioFeaturesAvg && typeof audioFeaturesAvg === "object" && !audioFeaturesAvg.error) {
        Object.entries(audioFeaturesAvg).forEach(([key, value]) => {
            const li = document.createElement("li");
            li.textContent = `${key}: ${value}`;
            featuresList.appendChild(li);
        });
    } else {
        featuresList.innerHTML = '<li class="text-muted">Nessun dato disponibile</li>';
    }

    const extremeList = document.getElementById("stat-extreme-tracks");
    if (extremeTracks && typeof extremeTracks === "object" && !extremeTracks.error) {
        if (extremeTracks.piu_energetico) {
            const t = extremeTracks.piu_energetico;
            const li = document.createElement("li");
            li.innerHTML = `Brano più energetico: <strong>${t.track.name}</strong> (${t.energy})`;
            extremeList.appendChild(li);
        }
        if (extremeTracks.piu_felice) {
            const t = extremeTracks.piu_felice;
            const li = document.createElement("li");
            li.innerHTML = `Brano più felice: <strong>${t.track.name}</strong> (${t.valence})`;
            extremeList.appendChild(li);
        }
        if (extremeTracks.piu_acustico) {
            const t = extremeTracks.piu_acustico;
            const li = document.createElement("li");
            li.innerHTML = `Brano più acustico: <strong>${t.track.name}</strong> (${t.acousticness})`;
            extremeList.appendChild(li);
        }
    }
        } catch (error) {
            console.error("Errore nel caricamento delle statistiche:", error);
            container.innerHTML = '<div class="alert alert-danger">Errore nel caricamento delle statistiche.</div>';
        }
    }


// Funzione ricerca amici per nome utente
const searchInput = document.getElementById("search-friend-input");
const resultContainer = document.getElementById("search-friend-result");

let searchTimeout;
searchInput.addEventListener("input", function () {
    clearTimeout(searchTimeout);
    const username = searchInput.value.trim();
    resultContainer.innerHTML = "";
    if (!username) {
        resultContainer.innerText = "Inserisci un nome utente!";
        return;
    }
    searchTimeout = setTimeout(async () => {
        try {
            const res = await fetch(`/api/users/list/${encodeURIComponent(username)}`, {
                headers: {
                    Authorization: "Bearer " + localStorage.getItem("auth_token"),
                },
            });
            if (!res.ok) {
                resultContainer.innerText = "Errore nella ricerca utenti.";
                return;
            }
            const data = await res.json();
            const users = Array.isArray(data.utenti) ? data.utenti : [];
            if (users.length === 0) {
                resultContainer.innerText = "Nessun utente trovato.";
                return;
            }
            // Mostra risultati come card
            const row = document.createElement("div");
            row.className = "row g-3";
            users.forEach((user) => {
                const col = document.createElement("div");
                col.className = "col-12";
                const card = document.createElement("div");
                card.className = "card d-flex flex-row align-items-center p-2";
                card.style.background = "#222";
                card.style.color = "#fff";
                // Immagine profilo
                const img = document.createElement("img");
                img.src = user.profile_picture || "https://via.placeholder.com/48";
                img.alt = `${user.username}'s profile picture`;
                img.className = "rounded-circle me-3";
                img.style.width = "48px";
                img.style.height = "48px";
                img.style.objectFit = "cover";
                // Info utente
                const info = document.createElement("div");
                info.className = "flex-grow-1";
                info.innerHTML = `<div class="fw-semibold">@${user.username}</div>
                                  <div class="text-secondary small">${user.spotify_id}</div>`;
                // Bottone invia richiesta
                const btn = document.createElement("button");
                btn.className = "btn btn-success ms-auto";
                btn.innerText = "Invia richiesta";
                btn.onclick = async () => {
                    btn.disabled = true;
                    btn.innerText = "Invio...";
                    try {
                        const sendRes = await fetch(`/api/friend-request/send/${user.spotify_id}`, {
                            headers: {
                                Authorization: "Bearer " + localStorage.getItem("auth_token"),
                            },
                        });
                        const sendData = await sendRes.json();
                        if (!sendRes.ok) {
                            btn.className = "btn btn-danger ms-auto";
                            btn.innerText = sendData.message || "Errore";
                        } else {
                            btn.className = "btn btn-secondary ms-auto";
                            btn.innerText = sendData.message || "Richiesta inviata!";
                        }
                    } catch (err) {
                        btn.className = "btn btn-danger ms-auto";
                        btn.innerText = "Errore";
                    }
                };
                card.appendChild(img);
                card.appendChild(info);
                card.appendChild(btn);
                col.appendChild(card);
                row.appendChild(col);
            });
            resultContainer.appendChild(row);
        } catch (error) {
            console.error(error);
            resultContainer.innerText = "Errore nella ricerca.";
        }
    }, 350); 
});
// Funzione carica amici
async function loadFriends() {
    try {
        const res = await fetch("/api/friends/list", {
            headers: {
                Authorization: "Bearer " + localStorage.getItem("auth_token"),
            },
        });
         
        const data = await res.json();
        const friends = data.friends || [];

        // Ottieni il container
        const friendsContainer = document.getElementById("friends-list");
        friendsContainer.innerHTML = ""; // Pulisci il contenuto esistente

        // Se non ci sono amici, mostra lo stato vuoto
        if (friends.length === 0) {
            const emptyState = document.createElement("div");
            emptyState.className = "text-center py-5 text-secondary";
            emptyState.innerHTML = `
        <i class="bi bi-people-fill fs-1 mb-3"></i>
        <h3 class="fs-5 mb-2">Nessun amico trovato</h3>
        <p class="fs-6">Aggiungi amici per vedere le loro statistiche d'ascolto!</p>
      `;
            friendsContainer.appendChild(emptyState);
            return;
        }

        // Creazione del contenitore a griglia per le card
        const row = document.createElement("div");
        row.className = "row g-4"; // g-4 per uno spaziamento tra le card
        friendsContainer.appendChild(row);

        // Creazione delle card per ogni amico
        friends.forEach((friend) => {
            // Crea colonna per la card
            const col = document.createElement("div");
            col.className = "col-12 col-md-6 col-lg-4 col-xl-3";

            // Crea la card
            const card = document.createElement("div");
            card.className = "card h-100";

            // Crea il corpo della card
            const cardBody = document.createElement("div");
            cardBody.className = "card-body";

            // Header della card con immagine e username
            const cardHeader = document.createElement("div");
            cardHeader.className = "d-flex align-items-center mb-3";

            // Immagine profilo
            const profileImg = document.createElement("img");
            profileImg.src =
                friend.profile_picture || "https://via.placeholder.com/60";
            profileImg.alt = `${
                friend.username || friend.spotify_id
            }'s profile picture`;
            profileImg.className = "rounded-circle me-3";
            profileImg.style.width = "60px";
            profileImg.style.height = "60px";
            profileImg.style.objectFit = "cover";

            // Container info utente
            const userInfo = document.createElement("div");
            userInfo.className = "d-flex flex-column";

            // Username con @ davanti
            const userName = document.createElement("div");
            userName.className = "fw-semibold text-truncate text-white";
            userName.innerText = `@${friend.username}`;

            // Spotify ID
            const userId = document.createElement("div");
            userId.className = "text-secondary small";
            userId.innerText = friend.spotify_id;

            userInfo.appendChild(userName);
            userInfo.appendChild(userId);

            // Assembla header
            cardHeader.appendChild(profileImg);
            cardHeader.appendChild(userInfo);

            // Funzione helper per creare un elemento statistica
            function createStatItem(value, label) {
                const stat = document.createElement("div");
                stat.className = "text-center";

                const statValue = document.createElement("div");
                statValue.className = "fw-bold text-success";
                statValue.innerText = value;

                const statLabel = document.createElement("div");
                statLabel.className = "text-secondary small";
                statLabel.innerText = label;

                stat.appendChild(statValue);
                stat.appendChild(statLabel);

                return stat;
            }

            // Pulsante rimuovi
            const removeButton = document.createElement("button");
            removeButton.className = "btn btn-outline-danger w-100";
            removeButton.innerText = "Rimuovi amico";

            // Aggiungi event listener per la rimozione
            removeButton.addEventListener("click", async () => {
                try {
                    const res = await fetch(
                        `/api/friend/remove/${friend.spotify_id}`,
                        {
                            method: "GET",
                            headers: {
                                Authorization:
                                    "Bearer " +
                                    localStorage.getItem("auth_token"),
                            },
                        }
                    );
                    if (!res.ok)
                        throw new Error("Errore nella rimozione dell'amico");

                    // Animazione di rimozione
                    card.style.opacity = "0.5";
                    setTimeout(() => {
                        col.remove();

                        // Controlla se ci sono ancora amici
                        if (friendsContainer.querySelector(".card") === null) {
                            loadFriends(); // Ricarica per mostrare stato vuoto
                        }
                    }, 500);
                } catch (error) {
                    console.error(error);
                    alert("Errore nella rimozione dell'amico.");
                }
            });

            // Aggiungi tutti gli elementi alla card
            cardBody.appendChild(cardHeader);

            cardBody.appendChild(removeButton);

            card.appendChild(cardBody);
            col.appendChild(card);
            row.appendChild(col);
        });

        // Aggiungi funzionalità di ricerca al campo esistente (assumendo che esista)
        const searchInput = document.querySelector(".search-friends");
        if (searchInput) {
            searchInput.addEventListener("input", function () {
                const searchValue = this.value.toLowerCase();
                const friendCards = document.querySelectorAll(".card");

                let visibleCount = 0;

                friendCards.forEach((card) => {
                    const username = card
                        .querySelector(".fw-semibold")
                        .innerText.toLowerCase();
                    const spotifyId = card
                        .querySelector(".text-secondary.small")
                        .innerText.toLowerCase();

                    if (
                        username.includes(searchValue) ||
                        spotifyId.includes(searchValue)
                    ) {
                        card.closest(".col-12").style.display = "";
                        visibleCount++;
                    } else {
                        card.closest(".col-12").style.display = "none";
                    }
                });

                // Se non ci sono risultati, mostra messaggio
                const emptySearch = document.getElementById("empty-search");
                if (emptySearch) {
                    emptySearch.style.display =
                        visibleCount === 0 ? "block" : "none";
                }
            });
        }
    } catch (error) {
        console.error(error);
        alert("Errore nel caricamento degli amici.");
    }
}

// Funzione carica post (user-friendly)
async function loadPosts() {
    try {
        const res = await fetch("/api/posts/feed", {
            headers: {
                Accept: "application/json",
                "Content-Type": "application/json",
                Authorization: "Bearer " + localStorage.getItem("auth_token"),
            },
        });

        if (!res.ok) {
            throw new Error("Errore nel caricamento dei post");
        }

        const data = await res.json();
        const posts = data.posts;

        updateContainer("posts-feed-container", posts, (post) => {
            // User info
            const user = post.utenti || {};
            const profilePic = user.profile_picture || "https://via.placeholder.com/48";
            const username = user.username || "Utente";
            // Data e ora
            const date = new Date(post.created_at);
            const formattedDate = date.toLocaleDateString("it-IT", {
                day: "2-digit",
                month: "2-digit",
                year: "2-digit",
                hour: "2-digit",
                minute: "2-digit",
            });

            // Content rendering per tipo post
            let contentHtml = "";
            if (post.type === "track") {
                if (post.content.title && post.content.artist) {
                    contentHtml = `
                        <div>
                            <span class="fw-bold">🎵 ${post.content.title}</span>
                            <span class="text-secondary">di ${post.content.artist}</span>
                        </div>
                        ${post.content.album ? `<div class="text-muted small">Album: ${post.content.album}</div>` : ""}
                    `;
                } else if (post.content.message) {
                    contentHtml = `<div>${post.content.message}</div>`;
                }
            } else if (post.type === "activity") {
                contentHtml = `<div class="text-info">${post.content.text || ""}</div>`;
            } else if (post.type === "status") {
                contentHtml = `<div>${post.content.text || ""}</div>`;
            } else {
                // fallback
                contentHtml = Object.entries(post.content || {})
                    .map(([key, value]) => `<strong>${key}:</strong> ${value}`)
                    .join("<br>");
            }

            // Card DOM
            const card = document.createElement("div");
            card.className = "col-12 mb-3";
            card.innerHTML = `
                <div class="card shadow-sm" style="background-color: #181818; border-radius: 12px; border: none;">
                    <div class="card-body pb-2">
                        <div class="d-flex align-items-center mb-2">
                            <img src="${profilePic}" alt="${username}" class="rounded-circle me-2" style="width:40px;height:40px;object-fit:cover;">
                            <div>
                                <span class="fw-semibold text-white">@${username}</span>
                                <div class="text-secondary small">${formattedDate}</div>
                            </div>
                            <span class="badge ms-auto ${post.type === "track" ? "bg-success" : post.type === "activity" ? "bg-primary" : "bg-secondary"}" style="font-size:0.8em;">
                                ${post.type}
                            </span>
                        </div>
                        <div class="mb-2" style="color:#e0e0e0;font-size:1.08em;">
                            ${contentHtml}
                        </div>
                        <div class="reactions d-flex align-items-center gap-2 mt-2">
                            <div style="position:relative;">
                                <img src="/assets/img/like.png" alt="Like" class="reaction-icon" data-reaction="like" style="width:22px;height:22px;opacity:0.7;cursor:pointer;">
                            </div>
                            <div style="position:relative;">
                                <img src="/assets/img/laugh.png" alt="Laugh" class="reaction-icon" data-reaction="laugh" style="width:22px;height:22px;opacity:0.7;cursor:pointer;">
                            </div>
                            <div style="position:relative;">
                                <img src="/assets/img/love.png" alt="Love" class="reaction-icon" data-reaction="love" style="width:22px;height:22px;opacity:0.7;cursor:pointer;">
                            </div>
                            <div style="position:relative;">
                                <img src="/assets/img/angry.png" alt="Angry" class="reaction-icon" data-reaction="angry" style="width:22px;height:22px;opacity:0.7;cursor:pointer;">
                            </div>
                            <span class="ms-2 text-muted small">Reazioni: ${post.post_reactions_count || 0}</span>
                        </div>
                    </div>
                </div>
            `;

            // Event listener per le reazioni
            const reactionIcons = card.querySelectorAll(".reaction-icon");
            reactionIcons.forEach((icon) => {
                icon.addEventListener("click", () => {
                    const reaction = icon.getAttribute("data-reaction");
                    likePost(post.id_post, reaction);
                });
            });

            // Mostra la reazione dell'utente (check)
            fetch(`/api/post/${post.id_post}/MyReaction`, {
                headers: {
                    Accept: "application/json",
                    Authorization: "Bearer " + localStorage.getItem("auth_token"),
                },
            })
                .then((reactionRes) => {
                    if (
                        reactionRes.ok &&
                        reactionRes.headers
                            .get("Content-Type")
                            ?.includes("application/json")
                    ) {
                        return reactionRes.json();
                    }
                    return null;
                })
                .then((reactionData) => {
                    if (reactionData && reactionData.reaction) {
                        const userReaction = reactionData.reaction;
                        const selectedIcon = card.querySelector(
                            `.reaction-icon[data-reaction="${userReaction}"]`
                        );
                        if (selectedIcon) {
                            const checkIcon = document.createElement("img");
                            checkIcon.src = "/assets/img/check.png";
                            checkIcon.alt = "Selected";
                            checkIcon.className = "reaction-check";
                            checkIcon.style.position = "absolute";
                            checkIcon.style.top = "-8px";
                            checkIcon.style.right = "-8px";
                            checkIcon.style.width = "16px";
                            checkIcon.style.height = "16px";
                            selectedIcon.parentElement.appendChild(checkIcon);
                        }
                    }
                })
                .catch((error) => {
                    console.error(
                        "Errore nel caricamento della reazione dell'utente:",
                        error
                    );
                });

            return card;
        });
    } catch (error) {
        console.error(error);
        alert("Errore nel caricamento dei post.");
    }
}

// Funzione like post
async function likePost(postId, reaction) {
    try {
        const res = await fetch(`/api/posts/${postId}/react/${reaction}`, {
            method: "GET",
            headers: {
                "Content-Type": "application/json",
                Accept: "application/json",
                Authorization: "Bearer " + localStorage.getItem("auth_token"),
            },
        });

        const successMessage = res.headers
            .get("Content-Type")
            ?.includes("application/json")
            ? (await res.json()).message
            : "Reazione aggiunta!";
        loadPosts(); // Ricarica il feed per aggiornare il conteggio delle reazioni
    } catch (error) {
        console.error(error);
    }
}

document.addEventListener("DOMContentLoaded", function () {
    const token = localStorage.getItem("auth_token");
    const listEl = document.getElementById("friend-request-list");
    const countEl = document.getElementById("friend-request-count");

    fetch("/api/friend-requests/pending", {
        headers: {
            Authorization: `Bearer ${token}`,
        },
    })
        .then((res) => res.json())
        .then((data) => {
            const pendingRequests = data.pending_requests || [];
            listEl.innerHTML = "";
            if (pendingRequests.length === 0) {
                listEl.innerHTML =
                    '<li><span class="dropdown-item-text text-white text-small">Nessuna richiesta di amicizia</span></li>';
                countEl.style.display = "none";
            } else {
                countEl.textContent = pendingRequests.length;
                countEl.style.display = "inline-block";

                pendingRequests.forEach((req) => {
                    const item = document.createElement("li");
                    item.classList.add("dropdown-item");
                    item.innerHTML = `
                                    <span>${req.username}</span>
                                    <button class="btn btn-sm btn-success ms-2 accept-request-btn" data-id="${req.spotify_id}">
                                        Accetta
                                    </button>
                                    <button class="btn btn-sm btn-danger ms-2 decline-request-btn" data-id="${req.spotify_id}">
                                        Rifiuta
                                    </button>
                                `;
                    listEl.appendChild(item);
                });

                // Add event listeners to accept buttons
                document
                    .querySelectorAll(".accept-request-btn")
                    .forEach((button) => {
                        button.addEventListener("click", function () {
                            const spotifyId = this.getAttribute("data-id");
                            fetch(`/api/friend-request/accept/${spotifyId}`, {
                                method: "GET",
                                headers: {
                                    Authorization: `Bearer ${token}`,
                                },
                            })
                                .then((res) => {
                                    if (!res.ok) {
                                        throw new Error(
                                            "Errore nell'accettare la richiesta"
                                        );
                                    }
                                    alert("Richiesta accettata!");
                                    location.reload(); // Reload to update the list
                                })
                                .catch((err) => {
                                    console.error("Errore:", err);
                                    alert(
                                        "Errore nell'accettare la richiesta."
                                    );
                                });
                        });
                    });

                // Add event listeners to decline buttons
                document
                    .querySelectorAll(".decline-request-btn")
                    .forEach((button) => {
                        button.addEventListener("click", function () {
                            const spotifyId = this.getAttribute("data-id");
                            fetch(`/api/friend-request/reject/${spotifyId}`, {
                                method: "GET",
                                headers: {
                                    Authorization: `Bearer ${token}`,
                                },
                            })
                                .then((res) => {
                                    if (!res.ok) {
                                        throw new Error(
                                            "Errore nel rifiutare la richiesta"
                                        );
                                    }
                                    alert("Richiesta rifiutata!");
                                    location.reload(); // Reload to update the list
                                })
                                .catch((err) => {
                                    console.error("Errore:", err);
                                    alert("Errore nel rifiutare la richiesta.");
                                });
                        });
                    });
            }
        })
        .catch((err) => {
            console.error("Errore nel caricamento delle richieste:", err);
            listEl.innerHTML =
                '<li><span class="dropdown-item-text text-danger">Errore</span></li>';
            countEl.style.display = "none";
        });
});
