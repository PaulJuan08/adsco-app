@php
    use App\Models\LegalPage;
    $footerLegals = LegalPage::where('is_published', true)
        ->orderByRaw("FIELD(type, 'privacy_policy', 'terms_conditions', 'cookie_policy')")
        ->get();
@endphp

<footer style="
    margin-top: auto;
    padding: .85rem 1.5rem;
    background: #fff;
    border-top: 1px solid #f0ebe8;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: .5rem;
    font-size: .8rem;
    color: #9ca3af;
">
    <span>
        <i class="far fa-copyright" style="margin-right:.3rem;"></i>{{ date('Y') }} Agusan Del Sur College. All rights reserved.
    </span>

    @if($footerLegals->isNotEmpty())
        <div style="display: flex; align-items: center; gap: .1rem; flex-wrap: wrap;">
            @foreach($footerLegals as $legal)
                @if(!$loop->first)
                    <span style="color: #d1d5db; margin: 0 .35rem;">·</span>
                @endif
                <a href="javascript:void(0)" onclick="openLegalModal('{{ $legal->type }}')" style="
                    color: #552b20; font-size: .8rem;
                    cursor: pointer; padding: .15rem .1rem;
                    transition: color .15s;
                    text-decoration: underline;
                    text-underline-offset: 2px;
                    text-decoration-color: transparent;
                " onmouseenter="this.style.color='#ddb238';this.style.textDecorationColor='#ddb238'"
                   onmouseleave="this.style.color='#552b20';this.style.textDecorationColor='transparent'"
                >{{ $legal->title }}</a>
            @endforeach
        </div>
    @endif
</footer>

