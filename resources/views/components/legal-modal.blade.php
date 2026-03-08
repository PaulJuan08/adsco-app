@php
    use App\Models\LegalPage;
    $publishedLegals = LegalPage::where('is_published', true)->get()->keyBy('type');
@endphp

<style>
#legalModalOverlay {
    position: fixed; inset: 0;
    background: rgba(0,0,0,.55); backdrop-filter: blur(5px);
    z-index: 99998;
    visibility: hidden; opacity: 0; pointer-events: none;
    transition: opacity 0.25s ease, visibility 0.25s ease;
}
#legalModalOverlay.open { visibility: visible; opacity: 1; pointer-events: all; }
#legalModalBox {
    position: fixed; top: 50%; left: 50%;
    z-index: 99999;
    width: calc(100% - 2rem); max-width: 740px; max-height: 88vh;
    background: #fff; border-radius: 20px;
    box-shadow: 0 24px 70px rgba(85,43,32,.28);
    overflow: hidden; display: flex; flex-direction: column;
    visibility: hidden; opacity: 0; pointer-events: none;
    transform: translate(-50%, calc(-50% - 20px)) scale(0.97);
    transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1), opacity 0.25s ease, visibility 0.25s ease;
}
#legalModalBox.open {
    visibility: visible; opacity: 1; pointer-events: all;
    transform: translate(-50%, -50%) scale(1);
}
</style>

{{-- Modal markup — always rendered so the JS functions are always available --}}
<div id="legalModalOverlay" onclick="closeLegalModal()"></div>

<div id="legalModalBox">
    <div style="background:linear-gradient(135deg,#552b20 0%,#3d1f17 100%);color:#fff;padding:1.25rem 1.5rem;display:flex;align-items:center;justify-content:space-between;flex-shrink:0;">
        <div style="display:flex;align-items:center;gap:.65rem;">
            <div id="legalModalIcon" style="width:36px;height:36px;border-radius:9px;background:rgba(221,178,56,.25);display:flex;align-items:center;justify-content:center;color:#ddb238;font-size:.95rem;flex-shrink:0;"></div>
            <h3 id="legalModalTitle" style="margin:0;font-size:1.1rem;font-weight:700;"></h3>
        </div>
        <button onclick="closeLegalModal()" style="background:rgba(255,255,255,.15);border:none;color:#fff;width:34px;height:34px;border-radius:9px;cursor:pointer;display:flex;align-items:center;justify-content:center;font-size:1rem;">
            <i class="fas fa-times"></i>
        </button>
    </div>

    <div id="legalModalMeta" style="padding:.55rem 1.5rem;background:#faf8f6;border-bottom:1px solid #f0ebe8;font-size:.75rem;color:#718096;display:flex;gap:1rem;flex-wrap:wrap;flex-shrink:0;"></div>

    <div id="legalModalBody" style="padding:1.5rem;overflow-y:auto;flex:1;font-size:.92rem;color:#374151;line-height:1.75;white-space:pre-wrap;word-break:break-word;"></div>

    <div style="padding:.85rem 1.5rem;border-top:1px solid #f0ebe8;display:flex;justify-content:flex-end;flex-shrink:0;background:#faf8f6;">
        <button onclick="closeLegalModal()" style="padding:.5rem 1.4rem;border-radius:9px;border:2px solid #e5e7eb;background:#fff;color:#552b20;font-size:.875rem;font-weight:600;cursor:pointer;">Close</button>
    </div>
</div>

<script>
(function() {
    var legalPages = {
        @foreach($publishedLegals as $type => $page)
        {!! json_encode($type) !!}: {
            title:     {!! json_encode($page->title) !!},
            content:   {!! json_encode($page->content) !!},
            updatedAt: {!! json_encode($page->updated_at ? $page->updated_at->format('F d, Y') : '') !!}
        },
        @endforeach
    };

    var legalIcons = {
        privacy_policy:   'fas fa-user-shield',
        terms_conditions: 'fas fa-file-contract',
        cookie_policy:    'fas fa-cookie-bite'
    };

    window.openLegalModal = function(type) {
        var page = legalPages[type];
        if (!page) {
            alert('This page is not available yet.');
            return;
        }

        document.getElementById('legalModalTitle').textContent = page.title;
        document.getElementById('legalModalIcon').innerHTML    = '<i class="' + (legalIcons[type] || 'fas fa-file') + '"></i>';
        document.getElementById('legalModalBody').textContent  = page.content;

        var meta = document.getElementById('legalModalMeta');
        meta.innerHTML = page.updatedAt
            ? '<span><i class="fas fa-clock" style="margin-right:.3rem;"></i>Last updated: ' + page.updatedAt + '</span>'
            : '';

        document.getElementById('legalModalOverlay').classList.add('open');
        document.getElementById('legalModalBox').classList.add('open');
        document.body.style.overflow = 'hidden';
    };

    window.closeLegalModal = function() {
        var overlay = document.getElementById('legalModalOverlay');
        var box     = document.getElementById('legalModalBox');
        if (overlay) overlay.classList.remove('open');
        if (box)     box.classList.remove('open');
        document.body.style.overflow = '';
    };

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') window.closeLegalModal();
    });
})();
</script>
