<?php
// photos.php — Photo gallery page using Unsplash API, filtered by twin city.
require_once 'db.php';
$pdo    = getDB();
$cities = $pdo->query("SELECT * FROM city")->fetchAll();

// Determine selected city (defaults to first city)
$selectedCity = isset($_GET['city_id']) ? (int)$_GET['city_id'] : $cities[0]['id'];
$currentCity  = null;
foreach ($cities as $c) {
    if ($c['id'] === $selectedCity) { $currentCity = $c; break; }
}
if (!$currentCity) $currentCity = $cities[0];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Photos — Twin Cities</title>
    <link rel="stylesheet" href="style.css">
    <style>
        /* ── City filter tabs ── */
        .city-tabs {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-bottom: 20px;
        }

        .city-tab {
            padding: 7px 20px;
            border: 2px solid #1a3c5e;
            border-radius: 30px;
            background: white;
            color: #1a3c5e;
            font-weight: 600;
            font-size: 0.85rem;
            cursor: pointer;
            text-decoration: none;
            transition: background 0.2s, color 0.2s;
        }

        .city-tab:hover,
        .city-tab.active {
            background: #1a3c5e;
            color: white;
            text-decoration: none;
        }

        /* ── Search bar ── */
        .search-bar {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            align-items: center;
            margin-bottom: 15px;
        }

        .search-bar input {
            flex: 1;
            min-width: 200px;
            padding: 8px 12px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 0.9rem;
        }

        .search-bar select {
            padding: 8px 12px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 0.9rem;
            background: white;
        }

        .search-bar button {
            padding: 8px 20px;
            background: #1a3c5e;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 0.9rem;
        }

        .search-bar button:hover { background: #16334f; }

        /* ── Results info ── */
        #resultsInfo {
            font-size: 0.85rem;
            color: #666;
            margin-bottom: 15px;
        }

        /* ── Masonry-style photo grid ── */
        .photo-grid {
            columns: 3;
            column-gap: 15px;
        }

        @media (max-width: 768px) { .photo-grid { columns: 2; } }
        @media (max-width: 480px) { .photo-grid { columns: 1; } }

        .photo-item {
            break-inside: avoid;
            margin-bottom: 15px;
            border-radius: 8px;
            overflow: hidden;
            position: relative;
            cursor: pointer;
            box-shadow: 0 2px 6px rgba(0,0,0,0.12);
        }

        .photo-item img {
            width: 100%;
            display: block;
            transition: transform 0.3s ease;
        }

        .photo-item:hover img { transform: scale(1.04); }

        /* Overlay shown on hover */
        .photo-overlay {
            position: absolute;
            bottom: 0; left: 0; right: 0;
            background: linear-gradient(transparent, rgba(0,0,0,0.68));
            color: white;
            padding: 22px 10px 8px;
            opacity: 0;
            transition: opacity 0.25s;
            font-size: 0.78rem;
        }

        .photo-item:hover .photo-overlay { opacity: 1; }

        .photo-overlay a {
            color: rgba(255,255,255,0.8);
            text-decoration: none;
        }

        .photo-overlay a:hover { color: white; }

        /* ── Loading / error states ── */
        #loadingSpinner {
            text-align: center;
            padding: 50px 0;
            color: #1a3c5e;
            font-size: 1rem;
            display: none;
        }

        #errorMsg {
            background: #fdecea;
            color: #c0392b;
            border: 1px solid #e74c3c;
            border-radius: 6px;
            padding: 12px 16px;
            margin-bottom: 15px;
            display: none;
        }

        /* ── Lightbox ── */
        .lightbox {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.9);
            z-index: 9999;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .lightbox.open { display: flex; }

        .lightbox-inner {
            max-width: 900px;
            width: 100%;
            position: relative;
        }

        .lightbox img {
            width: 100%;
            border-radius: 8px;
            max-height: 80vh;
            object-fit: contain;
        }

        .lightbox-caption {
            color: rgba(255,255,255,0.8);
            text-align: center;
            margin-top: 10px;
            font-size: 0.85rem;
        }

        .lightbox-caption a { color: rgba(255,255,255,0.7); }

        .lightbox-close {
            position: absolute;
            top: -36px;
            right: 0;
            color: white;
            font-size: 1.6rem;
            cursor: pointer;
            background: none;
            border: none;
            opacity: 0.8;
            line-height: 1;
        }

        .lightbox-close:hover { opacity: 1; }

        /* ── Unsplash credit (required by API terms) ── */
        .unsplash-credit {
            text-align: center;
            font-size: 0.78rem;
            color: #999;
            margin-top: 20px;
        }

        .unsplash-credit a { color: #999; }
    </style>
</head>
<body>

<header>
    <h1>🌍 Twin &amp; Sister Cities — Birmingham</h1>
    <nav>
        <a href="index.php">Home</a>
        <a href="map.php">Maps</a>
        <a href="weather.php">Weather</a>
        <a href="photos.php">Photos</a>
        <a href="rss.php">RSS Feed</a>
    </nav>
</header>

<div class="container">

    <div class="card">
        <h2>📷 City Photo Gallery</h2>
        <p style="color:#555; font-size:0.9rem; margin-bottom:16px;">
            Browse photos of Birmingham and its twin cities — Frankfurt and Lyon.
            Photos sourced from <a href="https://unsplash.com/?utm_source=twin_cities&utm_medium=referral" target="_blank" style="color:#1a3c5e;">Unsplash</a>.
        </p>

        <!-- City Filter Tabs -->
        <div class="city-tabs">
            <?php foreach ($cities as $c): ?>
            <a href="photos.php?city_id=<?= $c['id'] ?>"
               class="city-tab <?= $c['id'] == $selectedCity ? 'active' : '' ?>">
                <?= htmlspecialchars($c['name']) ?>
            </a>
            <?php endforeach; ?>
        </div>

        <!-- Search Bar -->
        <div class="search-bar">
            <input type="text" id="searchInput"
                   placeholder="e.g. Birmingham canals, Frankfurt skyline…"
                   value="<?= htmlspecialchars($currentCity['name']) ?>">
            <select id="perPage">
                <option value="12">12 photos</option>
                <option value="24" selected>24 photos</option>
                <option value="30">30 photos</option>
            </select>
            <button onclick="searchPhotos()">🔍 Search</button>
        </div>

        <!-- Results info -->
        <div id="resultsInfo"></div>

        <!-- Error message -->
        <div id="errorMsg"></div>

        <!-- Loading indicator -->
        <div id="loadingSpinner">⏳ Loading photos…</div>

        <!-- Photo Grid -->
        <div id="photoGrid" class="photo-grid"></div>

        <!-- Unsplash attribution (required by API guidelines) -->
        <p class="unsplash-credit">
            Photos by <a href="https://unsplash.com/?utm_source=twin_cities&utm_medium=referral" target="_blank">Unsplash</a>
        </p>
    </div>

</div>

<!-- Lightbox -->
<div class="lightbox" id="lightbox" onclick="closeLightbox(event)">
    <div class="lightbox-inner">
        <button class="lightbox-close" onclick="closeLightbox()">&times;</button>
        <img id="lightboxImg" src="" alt="">
        <div class="lightbox-caption" id="lightboxCaption"></div>
    </div>
</div>

<footer>Twin &amp; Sister Cities — UFCFV4-30-2 Data, Schemas and Applications</footer>

<script>
// Unsplash API key
const UNSPLASH_ACCESS_KEY = 'FiKYXJ30YYdWPcnws8tEZNEsjQoN8LmIQII30DPXaMU';

// Default search query comes from the PHP-selected city
const DEFAULT_QUERY = '<?= addslashes($currentCity['name']) ?>';

// Fetch photos from Unsplash search API
async function fetchPhotos(query, perPage) {
    const url = 'https://api.unsplash.com/search/photos'
              + '?query='    + encodeURIComponent(query)
              + '&per_page=' + perPage
              + '&orientation=landscape'
              + '&client_id=' + UNSPLASH_ACCESS_KEY;

    const res = await fetch(url);
    if (!res.ok) throw new Error('Unsplash API error: ' + res.status);
    return await res.json();
}

// Build and insert photo cards into the grid
function renderPhotos(photos) {
    const grid = document.getElementById('photoGrid');
    grid.innerHTML = '';

    if (!photos.length) {
        grid.innerHTML = '<p style="color:#888;">No photos found for this search.</p>';
        return;
    }

    photos.forEach(photo => {
        const item = document.createElement('div');
        item.className = 'photo-item';

        const desc       = esc(photo.description || photo.alt_description || '');
        const userName   = esc(photo.user.name);
        const profileUrl = photo.user.links.html;
        const fullUrl    = photo.urls.full;

        item.innerHTML =
            '<img src="' + photo.urls.regular + '"'
            + ' alt="' + (photo.alt_description || 'City photo') + '"'
            + ' loading="lazy"'
            + ' onclick="openLightbox(\'' + fullUrl + '\',\'' + desc + '\',\'' + userName + '\',\'' + profileUrl + '\')">'
            + '<div class="photo-overlay">'
            +   '<div>' + (photo.description || photo.alt_description || '') + '</div>'
            +   '<a href="' + profileUrl + '?utm_source=twin_cities&utm_medium=referral"'
            +      ' target="_blank" onclick="event.stopPropagation()">📷 ' + photo.user.name + '</a>'
            + '</div>';

        grid.appendChild(item);
    });
}

// Triggered by Search button or Enter key
async function searchPhotos() {
    const query   = document.getElementById('searchInput').value.trim();
    const perPage = document.getElementById('perPage').value;
    if (!query) return;

    const spinner  = document.getElementById('loadingSpinner');
    const errorDiv = document.getElementById('errorMsg');
    const info     = document.getElementById('resultsInfo');

    document.getElementById('photoGrid').innerHTML = '';
    errorDiv.style.display = 'none';
    info.textContent = '';
    spinner.style.display = 'block';

    try {
        const data = await fetchPhotos(query, perPage);
        info.textContent = 'Showing ' + data.results.length
                         + ' of ' + data.total.toLocaleString()
                         + ' results for "' + query + '"';
        renderPhotos(data.results);
    } catch (err) {
        errorDiv.textContent = 'Failed to load photos: ' + err.message;
        errorDiv.style.display = 'block';
    } finally {
        spinner.style.display = 'none';
    }
}

// Open the full-resolution lightbox
function openLightbox(fullUrl, description, photographer, profileUrl) {
    document.getElementById('lightboxImg').src = fullUrl;
    document.getElementById('lightboxCaption').innerHTML =
        (description ? description + ' — ' : '')
        + 'Photo by <a href="' + profileUrl + '?utm_source=twin_cities&utm_medium=referral"'
        + ' target="_blank">' + photographer + '</a>'
        + ' on <a href="https://unsplash.com/?utm_source=twin_cities&utm_medium=referral"'
        + ' target="_blank">Unsplash</a>';
    document.getElementById('lightbox').classList.add('open');
    document.body.style.overflow = 'hidden';
}

// Close lightbox — only when clicking the backdrop or the × button
function closeLightbox(event) {
    if (event) {
        const target = event.target;
        const isBackdrop = target === document.getElementById('lightbox');
        const isCloseBtn = target.closest && target.closest('.lightbox-close');
        if (!isBackdrop && !isCloseBtn) return;
    }
    document.getElementById('lightbox').classList.remove('open');
    document.getElementById('lightboxImg').src = '';
    document.body.style.overflow = '';
}

// Escape single quotes for safe JS string injection
function esc(str) {
    return String(str).replace(/'/g, "\\'").replace(/"/g, '&quot;').replace(/\n/g, ' ');
}

// Keyboard shortcuts
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeLightbox({ target: document.getElementById('lightbox') });
});

// Allow pressing Enter in the search box
document.getElementById('searchInput').addEventListener('keydown', function(e) {
    if (e.key === 'Enter') searchPhotos();
});

// Auto-load on page open
searchPhotos();
</script>
</body>
</html>