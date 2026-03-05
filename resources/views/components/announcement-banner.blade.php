@php
    use App\Models\Announcement;
    $activeAnnouncements = Announcement::active()->latest()->get();
    $annCount = $activeAnnouncements->count();
@endphp

@if($annCount > 0)
{{-- ─── Floating Notification Bell ─── --}}
<div id="annBell" style="
    position: fixed;
    top: 20px;
    right: 24px;
    z-index: 9999;
">
    {{-- Bell Button --}}
    <button id="annBellBtn" onclick="toggleAnnPanel()" style="
        width: 48px; height: 48px;
        border-radius: 14px;
        background: linear-gradient(135deg, #552b20 0%, #3d1f17 100%);
        border: none;
        cursor: pointer;
        display: flex; align-items: center; justify-content: center;
        box-shadow: 0 4px 16px rgba(85,43,32,.35);
        position: relative;
        transition: transform .2s, box-shadow .2s;
    " onmouseenter="this.style.transform='scale(1.08)';this.style.boxShadow='0 6px 22px rgba(85,43,32,.45)'"
       onmouseleave="this.style.transform='scale(1)';this.style.boxShadow='0 4px 16px rgba(85,43,32,.35)'"
       title="Announcements ({{ $annCount }})"
    >
        <i class="fas fa-bell" style="color:#ddb238; font-size:1.2rem; animation: bellRing 3s ease-in-out infinite;"></i>
        <span id="annBadge" style="
            position: absolute;
            top: -5px; right: -5px;
            background: #ef4444;
            color: #fff;
            font-size: .65rem;
            font-weight: 700;
            width: 20px; height: 20px;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            border: 2px solid #fff;
            box-shadow: 0 2px 6px rgba(239,68,68,.4);
        ">{{ $annCount }}</span>
    </button>

    {{-- Dropdown Panel --}}
    <div id="annPanel" style="
        display: none;
        position: absolute;
        top: calc(100% + 10px);
        right: 0;
        width: 360px;
        max-height: 480px;
        background: #fff;
        border-radius: 18px;
        box-shadow: 0 16px 50px rgba(85,43,32,.22);
        overflow: hidden;
        animation: panelIn .2s ease;
        border: 1px solid #f0ebe8;
    ">
        {{-- Panel Header --}}
        <div style="
            background: linear-gradient(135deg, #552b20 0%, #3d1f17 100%);
            padding: 1rem 1.25rem;
            display: flex; align-items: center; justify-content: space-between;
        ">
            <div style="display:flex; align-items:center; gap:.6rem;">
                <i class="fas fa-bullhorn" style="color:#ddb238; font-size:1rem;"></i>
                <span style="color:#fff; font-weight:700; font-size:.95rem;">Announcements</span>
                <span style="
                    background: #ddb238; color: #552b20;
                    font-size: .65rem; font-weight: 700;
                    padding: 1px 7px; border-radius: 20px;
                ">{{ $annCount }}</span>
            </div>
            <button onclick="toggleAnnPanel()" style="
                background: rgba(255,255,255,.15); border: none; color: #fff;
                width: 28px; height: 28px; border-radius: 7px;
                cursor: pointer; display: flex; align-items: center; justify-content: center;
                font-size: .9rem;
            "><i class="fas fa-times"></i></button>
        </div>

        {{-- Announcement List --}}
        <div style="overflow-y: auto; max-height: 400px;">
            @foreach($activeAnnouncements as $ann)
                @php
                    $colors = [
                        'info'    => ['bg' => '#eff6ff', 'border' => '#3b82f6', 'icon' => '#3b82f6', 'iconClass' => 'fa-info-circle'],
                        'warning' => ['bg' => '#fffbeb', 'border' => '#f59e0b', 'icon' => '#d97706', 'iconClass' => 'fa-exclamation-triangle'],
                        'success' => ['bg' => '#f0fdf4', 'border' => '#10b981', 'icon' => '#059669', 'iconClass' => 'fa-check-circle'],
                        'danger'  => ['bg' => '#fef2f2', 'border' => '#ef4444', 'icon' => '#dc2626', 'iconClass' => 'fa-exclamation-circle'],
                    ];
                    $c = $colors[$ann->type] ?? $colors['info'];
                @endphp
                <div style="
                    padding: 1rem 1.25rem;
                    border-bottom: 1px solid #f7f0ec;
                    background: {{ $c['bg'] }};
                    border-left: 3px solid {{ $c['border'] }};
                    transition: background .15s;
                " onmouseenter="this.style.filter='brightness(.97)'" onmouseleave="this.style.filter='brightness(1)'">
                    <div style="display: flex; gap: .65rem; align-items: flex-start;">
                        <i class="fas {{ $c['iconClass'] }}" style="color: {{ $c['icon'] }}; font-size:1rem; margin-top:.1rem; flex-shrink:0;"></i>
                        <div style="flex:1; min-width:0;">
                            <div style="font-weight:700; font-size:.88rem; color:#1a202c; margin-bottom:.25rem; word-break:break-word;">
                                {{ $ann->title }}
                            </div>
                            <div style="font-size:.8rem; color:#4a5568; line-height:1.5; word-break:break-word;">
                                {{ Str::limit(strip_tags($ann->content), 120) }}
                            </div>
                            @if($ann->end_date)
                                <div style="font-size:.72rem; color:#718096; margin-top:.4rem;">
                                    <i class="fas fa-calendar-alt"></i>
                                    Until {{ $ann->end_date->format('M d, Y') }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

<style>
@@keyframes bellRing {
    0%, 100% { transform: rotate(0deg); }
    10% { transform: rotate(-15deg); }
    20% { transform: rotate(15deg); }
    30% { transform: rotate(-10deg); }
    40% { transform: rotate(10deg); }
    50% { transform: rotate(0deg); }
}
@@keyframes panelIn {
    from { opacity:0; transform: translateY(-8px) scale(.97); }
    to   { opacity:1; transform: translateY(0) scale(1); }
}
</style>

<script>
    function toggleAnnPanel() {
        const panel = document.getElementById('annPanel');
        const isOpen = panel.style.display !== 'none';
        panel.style.display = isOpen ? 'none' : 'block';
    }
    // Close when clicking outside
    document.addEventListener('click', function(e) {
        const bell  = document.getElementById('annBell');
        const panel = document.getElementById('annPanel');
        if (bell && panel && !bell.contains(e.target)) {
            panel.style.display = 'none';
        }
    });
</script>
@endif
