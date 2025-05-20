
$(document).ready(function () {
    function loginWithSpotify() {
        fetch("http://127.0.0.1:8000/api/auth/api-token", {
            method: "GET",
            headers: {
                Accept: "application/json",
            },
        })
            .then((response) => {
                 
                return response.json();
            })
            .then((data) => {
                 
                if (data.auth_token) {
                     
                    localStorage.setItem("auth_token", data.auth_token);
                     
                    initDashboard();
                } else {
                    console.error("Token non trovato nella risposta:", data);
                }
            })
            .catch((error) =>
                console.error("Errore durante il recupero del token:", error)
            );
    }
     
    loginWithSpotify();
    let token = localStorage.getItem("auth_token");
       
    

    // Inizializza la dashboard (carica dati e imposta listener)
    async function initDashboard() {
        await loadData("short_term");
        setupEventListeners();
    }

    // Carica i dati dalle API Spotify e li mette in "data"
    async function loadData(term = "short_term") {
        let data = {
            top_tracks: [],
            top_artists: [],
            recently_played: [],
            top_genres: {},
        };

         
        let top_tracks = await fetchData(
            "api/spotify/top-tracks?limit=5&time_range=" + term
        );
        data.top_tracks = top_tracks?.items;
         

         
        let top_artists = await fetchData(
            "api/spotify/top-artists?limit=5&time_range=" + term
        );
        data.top_artists = top_artists?.items || [];
         

         
        let recent_tracks = await fetchData(
            "api/spotify/recently-played?limit=5&time_range=" + term
        );
        data.recently_played = recent_tracks?.items || [];
         

         
        let top_genres = await fetchData(
            "api/spotify/top-genres?time_range=" + term
        );
        data.top_genres = top_genres || {};
         

        renderDashboard(data);
    }

    // Rimuove loader e avvia sezione render
    function renderDashboard(data) {
         
        $("#card-body").empty();
        $(".loading").hide();
        renderRecentlyPlayed(data.recently_played);
        renderTopTracks(data.top_tracks);
        renderTopArtists(data.top_artists);
        renderTopGenres(data.top_genres);
    }

    function fetchData(url) {
        const token = localStorage.getItem('auth_token');

        if (!token) {
            console.error("Token non trovato nel localStorage");
            return Promise.resolve({}); // Ritorna un oggetto vuoto se non c'è il token
        }

        return fetch(url, {
            headers: {
                Accept: "application/json",
                Authorization: `Bearer ${token}`,
            },
            credentials: "same-origin",
        })
            .then((res) => {
                if (!res.ok) {
                    console.error(
                        "Errore nella risposta:",
                        res.status,
                        res.statusText
                    );
                    return {}; // Ritorna un oggetto vuoto in caso di errore
                }
                return res.json();
            })
            .catch((err) => {
                console.error("Fetch error", url, err);
                return {}; // Ritorna un oggetto vuoto in caso di errore
            });
    }



    // Listener refresh
    async function setupEventListeners() {
        $("#refresh-recent").on("click", () =>
            loadData($(".time-filter").find(".active").data("period"))
        );
        $(".filter-option").on("click", async function () {
            $(".filter-option").removeClass("active");
            $(this).addClass("active");
            const term = $(this).data("period");
             
            await loadData(term);
        });
    }
});

// Render recently played usando array items
function renderRecentlyPlayed(recentlyPlayed) {
    const container = document.getElementById("recently-played");
    const loading = container.querySelector(".loading");
    if (loading) loading.remove();
    container.innerHTML = ""; // Pulisci il contenitore
    const list = document.createElement("div");
    list.className = "track-list";

    recentlyPlayed.forEach((item) => {
        const track = item.track;
        if (!track) return;

        const trackInfo = document.createElement("div");
        trackInfo.className = "track-info d-flex align-items-center mb-2";

        // Immagine della traccia
        const trackImage = document.createElement("img");
        trackImage.src =
            track.album?.images?.[2]?.url || // Immagine più piccola se esiste
            track.album?.images?.[0]?.url ||
            "https://via.placeholder.com/50";
        trackImage.alt = track.name || "Traccia sconosciuta";
        trackImage.className = "track-image me-2";
        trackImage.style.width = "50px";
        trackImage.style.height = "50px";
        trackImage.style.objectFit = "cover";
        trackImage.style.borderRadius = "5px";

        // Testo della traccia
        const trackText = document.createElement("div");
        const name = track.name || "Traccia sconosciuta";
        const artist = track.artists?.[0]?.name || "Artista sconosciuto";
        const playedAt = item.played_at
            ? new Date(item.played_at).toLocaleString()
            : "Data sconosciuta";

        trackText.innerHTML = `<p>${name}</p><p>${artist} - ${playedAt}</p>`;

        // Aggiungi immagine e testo
        trackInfo.appendChild(trackImage);
        trackInfo.appendChild(trackText);
        list.appendChild(trackInfo);
    });

    container.appendChild(list);
}

// Render top tracks con nome variabile top_tracks
function renderTopTracks(top_tracks) {
     
    const container = document.getElementById("top-tracks");
    container.innerHTML = "";
    if (!top_tracks.length) {
        container.innerHTML = "<p>Nessuna top track disponibile.</p>";
        return;
    }
    const list = document.createElement("div");
    list.className = "track-list";
    top_tracks.forEach((track) => {
        const info = document.createElement("div");
        info.className = "track-info";

        // Controlla se album esiste
        const img = document.createElement("img");
        img.src =
            track.album?.images?.[1]?.url || "https://via.placeholder.com/50";
        img.alt = track.name || "Traccia sconosciuta";
        img.className = "track-image";

        const p = document.createElement("p");
        p.textContent = `${track.name} - ${
            track.artists?.[0]?.name || "Artista sconosciuto"
        }`;

        info.append(img, p);
        list.appendChild(info);
    });
    container.appendChild(list);
}

// Render top artists con nome variabile top_artists
function renderTopArtists(top_artists) {
     
    const container = document.getElementById("top-artists");
    container.innerHTML = "";
    if (!top_artists.length) {
        container.innerHTML = "<p>Nessun artista disponibile.</p>";
        return;
    }
    const list = document.createElement("div");
    list.className = "artist-list";
    top_artists.forEach((artist) => {
        const info = document.createElement("div");
        info.className = "artist-info";
        const img = document.createElement("img");
        img.src = artist.images[1]?.url || "https://via.placeholder.com/50";
        img.alt = artist.name;
        img.className = "artist-image";
        const p = document.createElement("p");
        p.textContent = artist.name;
        info.append(img, p);
        list.appendChild(info);
    });
    container.appendChild(list);
}

function renderTopGenres(top_genres) {
     
    const container = document.getElementById("genres-chart");
    container.innerHTML = "";

    if (!Object.keys(top_genres).length) {
        container.innerHTML = "<p>Nessun dato disponibile per i generi.</p>";
        return;
    }
    //prendi i primi 5 generi
    const limitedGenres = Object.entries(top_genres)
        .slice(0, 5) // Prendi i primi 5 generi
        .reduce((acc, [key, value]) => {
            acc[key] = value;
            return acc;
        }, {});

    // Crea un canvas per il grafico
    const canvas = document.createElement("canvas");
    canvas.id = "genresPieChart";
    container.appendChild(canvas);

    // Prepara i dati per il grafico
    const labels = Object.keys(limitedGenres);
    const values = Object.values(limitedGenres);
    const colors = labels.map(() => `hsl(${Math.random() * 360}, 70%, 75%)`);

    // Inizializza il grafico con Chart.js
    new Chart(canvas, {
        type: "pie",
        data: {
            labels,
            datasets: [
                {
                    data: values,
                    backgroundColor: colors,
                },
            ],
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: "bottom",
                },
            },
        },
    });
}
